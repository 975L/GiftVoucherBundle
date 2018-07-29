<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use c975L\PaymentBundle\Entity\Payment;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Form\GiftVoucherAvailableType;
use c975L\GiftVoucherBundle\Form\GiftVoucherPurchasedType;
use c975L\GiftVoucherBundle\Service\GiftVoucherService;

class PurchasedController extends Controller
{
//DISPLAY
    /**
     * @Route("/gift-voucher/{identifier}",
     *      name="giftvoucher_display",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function display(Request $request, $identifier)
    {
        //Gets the GiftVoucher
        $giftVoucher = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')
            ->findOneBasedOnIdentifier($identifier);

        //Not existing GiftVoucher
        if (!$giftVoucher instanceof GiftVoucherPurchased) {
            throw $this->createNotFoundException();
        }

        //Defines display rights
        $display = 'basic';
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            $display = 'admin';
        }

        return $this->render('@c975LGiftVoucher/pages/display.html.twig', array(
            'giftVoucher' => $giftVoucher,
            'display' => $display,
        ));
    }

//USE
    /**
     * @Route("/gift-voucher/use/{identifier}",
     *      name="giftvoucher_use",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function use(Request $request, $identifier)
    {
        //Allows use if user has rights
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')->findOneBasedOnIdentifier($identifier);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherPurchased) {
                throw $this->createNotFoundException();
            }

            //Valid
            $now = new \DateTime();
            if ($giftVoucher->getValid() === null || $giftVoucher->getValid() > $now || $request->get('force') == 'true') {
                $giftVoucher->setUsed($now);

                $em->persist($giftVoucher);
                $em->flush();

                //Creates flash
                $translator = $this->get('translator');
                $flash = $translator->trans('text.voucher_used', array(), 'giftVoucher');
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', $flash)
                    ;
            //Out of date not "forced"
            } else {
                //Returns GiftVoucher to allow force use
                return $this->render('@c975LGiftVoucher/pages/display.html.twig', array(
                    'giftVoucher' => $giftVoucher,
                    'display' =>'admin',
                    'forceUse' => true,
                ));
            }

            return $this->redirectToRoute('giftvoucher_display', array('identifier' => $identifier));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//OFFER
    /**
     * @Route("/gift-voucher/offer",
     *      name="giftvoucher_offer_all")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function offerAll(Request $request)
    {
        //Gets the GiftVouchers
        $giftVouchers = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findAll();

        //Renders the page
        return $this->render('@c975LGiftVoucher/pages/offerAll.html.twig', array(
            'giftVouchers' => $giftVouchers,
        ));
    }

//OFFER SPECIFIED GIFT-VOUCHER
    /**
     * @Route("/gift-voucher/offer/{id}",
     *      name="giftvoucher_offer_id_redirect",
     *      requirements={
     *          "id": "^([0-9])+$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function offerIdRedirect(Request $request, $id)
    {
        //Gets the GiftVoucher
        $giftVoucherAvailable = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findOneById($id);

        //Not existing GiftVoucher
        if (!$giftVoucherAvailable instanceof GiftVoucherAvailable) {
            throw $this->createNotFoundException();
        }

        //Redirects to the Gift-Voucher
        return $this->redirectToRoute('giftvoucher_offer', array(
            'slug' => $giftVoucherAvailable->getSlug(),
            'id' => $giftVoucherAvailable->getId(),
        ));
    }
    /**
     * @Route("/gift-voucher/offer/{slug}/{id}",
     *      name="giftvoucher_offer",
     *      requirements={
     *          "slug": "^([a-zA-Z0-9\-]+)$",
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function offer(Request $request, GiftVoucherService $giftVoucherService, $slug, $id)
    {
        //Gets the GiftVoucher
        $em = $this->getDoctrine()->getManager();
        $giftVoucherAvailable = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

        //Not existing GiftVoucher
        if (!$giftVoucherAvailable instanceof GiftVoucherAvailable) {
            throw $this->createNotFoundException();
        }

        //Wrong slug redirects to right one
        if ($slug != $giftVoucherAvailable->getSlug()) {
            return $this->redirectToRoute('giftvoucher_offer', array(
                'slug' => $giftVoucherAvailable->getSlug(),
                'id' => $giftVoucherAvailable->getId(),
            ));
        }

        //Defines form
        $giftVoucherPurchased = new GiftVoucherPurchased();
        $validDate = new \DateTime();
        $validDate->add($giftVoucherAvailable->getValid());
        $giftVoucherPurchased
            ->setObject($giftVoucherAvailable->getObject())
            ->setDescription($giftVoucherAvailable->getDescription())
            ->setAmount($giftVoucherAvailable->getAmount())
            ->setCurrency($giftVoucherAvailable->getCurrency())
            ->setValid($validDate)
            ;
        $form = $this->createForm(GiftVoucherPurchasedType::class, $giftVoucherPurchased);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Adds test data
            if ($this->getParameter('c975_l_gift_voucher.live') === false) {
                $giftVoucherPurchased->setObject('(TEST) ' . $giftVoucherPurchased->getObject());
            }

            //Persists data in DB
            $em->persist($giftVoucherPurchased);
            $em->flush();

            //Redirects to the payment
            $userId = null !== $this->getUser() ? $this->getUser()->getId() : null;
            $giftVoucherService->payment($giftVoucherPurchased, $userId);

            return $this->redirectToRoute('payment_form');
        }

        return $this->render('@c975LGiftVoucher/forms/offer.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherPurchased,
            'giftVoucherAvailable' => $giftVoucherAvailable,
            'live' => $this->getParameter('c975_l_gift_voucher.live'),
            'tosUrl' => $giftVoucherService->getTosUrl(),
        ));
    }

//PAYMENT DONE
    /**
     * @Route("/gift-voucher/payment-done/{orderId}",
     *      name="giftvoucher_payment_done")
     * @Method({"GET", "HEAD"})
     */
    public function paymentDone(GiftVoucherService $giftVoucherService, $orderId)
    {
        //Gets Stripe payment
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository('c975L\PaymentBundle\Entity\Payment')
            ->findOneByOrderIdNotFinished($orderId);

        if ($payment instanceof Payment) {
            //Validates the GiftVoucher
            $giftVoucherIdentifier = $giftVoucherService->validate($payment);

            //Redirects to the Gift-Voucher
            if (false !== $giftVoucherIdentifier) {
                return $this->redirectToRoute('giftvoucher_display', array(
                    'identifier' => $giftVoucherIdentifier,
                ));
            }
        }

        //Redirects to the display of payment
        return $this->redirectToRoute('payment_display', array(
            'orderId' => $orderId,
        ));
    }
}