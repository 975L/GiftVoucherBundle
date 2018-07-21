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
use c975L\PaymentBundle\Service\PaymentService;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Form\GiftVoucherAvailableType;
use c975L\GiftVoucherBundle\Form\GiftVoucherPurchasedType;
use c975L\GiftVoucherBundle\Service\GiftVoucherService;

class GiftVoucherController extends Controller
{
//DASHBOARD
    /**
     * @Route("/gift-voucher/dashboard",
     *      name="giftvoucher_dashboard")
     * @Method({"GET", "HEAD"})
     */
    public function dashboard(Request $request)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Purchased
            if ($request->query->get('v') === null || $request->query->get('v') == '') {
                //Gets repository
                $repository = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased');

                //Gets GiftVouchers
                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $repository->findPurchased(),
                    $request->query->getInt('p', 1),
                    15
                );
            } elseif ($request->query->get('v') == 'available') {
                //Gets repository
                $repository = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable');

                //Gets GiftVouchers
                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $repository->findAllAvailable(),
                    $request->query->getInt('p', 1),
                    15
                );
            //Not found
            } else {
                throw $this->createNotFoundException();
            }

            //Returns the dashboard
            return $this->render('@c975LGiftVoucher/pages/dashboard.html.twig', array(
                'giftVouchers' => $pagination,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//NEW AVAILABLE
    /**
     * @Route("/gift-voucher/new-available",
     *      name="giftvoucher_new_available")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function newAvailable(Request $request, GiftVoucherService $giftVoucherService)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Defines form
            $giftVoucher = new GiftVoucherAvailable();
            $giftVoucherConfig = array('action' => 'new');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucher, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adjust slug in case of not accepted signs
                $giftVoucher->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

                //Persists data in DB
                $em = $this->getDoctrine()->getManager();
                $em->persist($giftVoucher);
                $em->flush();

                //Redirects to the GiftVoucher created
                return $this->redirectToRoute('giftvoucher_display_available', array(
                    'id' => $giftVoucher->getId(),
                ));
            }

            return $this->render('@c975LGiftVoucher/forms/new.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DISPLAY AVAILABLE
    /**
     * @Route("/gift-voucher/display-available/{id}",
     *      name="giftvoucher_display_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function displayAvailable($id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $giftVoucher = $this->getDoctrine()
                ->getManager()
                ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
                ->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            return $this->render('@c975LGiftVoucher/pages/displayAvailable.html.twig', array(
                'giftVoucher' => $giftVoucher,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//MODIFY AVAILABLE
    /**
     * @Route("/gift-voucher/modify-available/{id}",
     *      name="giftvoucher_modify_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function modifyAvailable(Request $request, GiftVoucherService $giftVoucherService, $id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            //Defines form
            $giftVoucherConfig = array('action' => 'modify');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucher, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adjust slug in case of not accepted signs
                $giftVoucher->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

                //Persists data in DB
                $em->persist($giftVoucher);
                $em->flush();

                //Redirects to the GiftVoucher
                return $this->redirectToRoute('giftvoucher_display_available', array(
                    'id' => $giftVoucher->getId(),
                ));
            }

            return $this->render('@c975LGiftVoucher/forms/modify.html.twig', array(
                'giftVoucher' => $giftVoucher,
                'form' => $form->createView(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DUPLICATE AVAILABLE
    /**
     * @Route("/gift-voucher/duplicate-available/{id}",
     *      name="giftvoucher_duplicate_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function duplicateAvailable(Request $request, GiftVoucherService $giftVoucherService, $id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            //Defines form
            $giftVoucherClone = clone $giftVoucher;
            $giftVoucherClone
                ->setObject(null)
                ->setSlug(null)
            ;
            $giftVoucherConfig = array('action' => 'duplicate');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucherClone, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adjust slug in case of not accepted signs
                $giftVoucherClone->setSlug($giftVoucherService->slugify($form->getData()->getSlug()));

                //Persists data in DB
                $em->persist($giftVoucherClone);
                $em->flush();

                //Redirects to the GiftVoucher
                return $this->redirectToRoute('giftvoucher_display_available', array(
                    'id' => $giftVoucherClone->getId(),
                ));
            }

            //Returns the form to duplicate content
            return $this->render('@c975LGiftVoucher/forms/duplicate.html.twig', array(
                'form' => $form->createView(),
                'giftVoucher' => $giftVoucherClone,
                'object' => $giftVoucher->getObject(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DELETE AVAILABLE
    /**
     * @Route("/gift-voucher/delete-available/{id}",
     *      name="giftvoucher_delete_available",
     *      requirements={
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function deleteAvailable(Request $request, $id)
    {
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the GiftVoucher
            $em = $this->getDoctrine()->getManager();
            $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

            //Not existing GiftVoucher
            if (!$giftVoucher instanceof GiftVoucherAvailable) {
                throw $this->createNotFoundException();
            }

            //Defines form
            $giftVoucherConfig = array('action' => 'delete');
            $form = $this->createForm(GiftVoucherAvailableType::class, $giftVoucher, array('giftVoucherConfig' => $giftVoucherConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Persists data in DB
                $giftVoucher->setSuppressed(true);

                $em->persist($giftVoucher);
                $em->flush();

                //Redirects to the dashboard
                return $this->redirectToRoute('giftvoucher_dashboard');
            }

            return $this->render('@c975LGiftVoucher/forms/delete.html.twig', array(
                'form' => $form->createView(),
                'giftVoucher' => $giftVoucher,
            ));
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
        $giftVoucher = $this->getDoctrine()
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
        $giftVoucher = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findOneById($id);

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

            //Gets user
            $userId = null !== $this->getUser() ? $this->getUser()->getId() : null;

            //Payment
            $translator = $this->get('translator');
            $paymentData = array(
                'amount' => $giftVoucherPurchased->getAmount(),
                'currency' => $giftVoucherPurchased->getCurrency(),
                'action' => json_encode(array('validateGiftVoucher' => $giftVoucherPurchased->getId())),
                'description' => $translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' - ' . $giftVoucherPurchased->getObject(),
                'userId' => $userId,
                'userIp' => $request->getClientIp(),
                'live' => $this->getParameter('c975_l_gift_voucher.live'),
                'returnRoute' => 'giftvoucher_payment_done',
                'vat' => $this->getParameter('c975_l_gift_voucher.vat'),
                );
            $paymentService = $this->get(\c975L\PaymentBundle\Service\PaymentService::class);
            $paymentService->create($paymentData);

            //Redirects to the payment
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
    public function paymentDone(Request $request, GiftVoucherService $giftVoucherService, $orderId)
    {
        //Gets Stripe payment
        $em = $this->getDoctrine()->getManager();
        $payment = $em->getRepository('c975L\PaymentBundle\Entity\Payment')
            ->findOneByOrderIdNotFinished($orderId);

        if ($payment instanceof Payment) {
            //Validates the GiftVoucher
            $action = (array) json_decode($payment->getAction());

            if (array_key_exists('validateGiftVoucher', $action)) {
                $repository = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased');
                $giftVoucher = $repository->findOneById($action['validateGiftVoucher']);

                //Gets identifier
                $identifierExists = true;
                do {
                    $identifier = $giftVoucherService->getIdentifier();
                    $identifierExists = $repository->findOneBy(array('identifier' => substr($identifier, 0, 12), 'secret' => substr($identifier, 12)));
                } while ($identifierExists !== null);

                //Updates GiftVoucher
                $giftVoucher
                    ->setPurchase(new \DateTime())
                    ->setIdentifier(substr($identifier, 0, 12))
                    ->setSecret(substr($identifier, 12))
                    ->setorderId($payment->getOrderId())
                    ;
                $em->persist($giftVoucher);

                //Set payment as finished
                $payment->setFinished(true);
                $em->persist($payment);
                $em->flush();

                //Sends email
                $giftVoucherService->sendEmail($giftVoucher);

                //Creates flash
                $flash = $translator->trans('text.voucher_purchased', array(), 'giftVoucher');
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', $flash)
                    ;

                //Redirects to the Gift-Voucher
                return $this->redirectToRoute('giftvoucher_display', array(
                    'identifier' => $giftVoucher->getIdentifier() . $giftVoucher->getSecret(),
                ));
            }
        }

        //Redirects to the display of payment
        return $this->redirectToRoute('payment_display', array(
            'orderId' => $orderId,
        ));
    }

//DISPLAY PURCHASED
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

//SLUG
    /**
     * @Route("/gift-voucher/slug/{text}",
     *      name="giftvoucher_slug")
     * @Method({"POST"})
     */
    public function slug(GiftVoucherService $giftVoucherService, $text)
    {
        return $this->json(array('a' => $giftVoucherService->slugify($text)));
    }

//QRCODE
    /**
     * @Route("/gift-voucher/qrcode/{identifier}",
     *      name="giftvoucher_qrcode",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function qrcode(GiftVoucherService $giftVoucherService,$identifier)
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

        //Gets the formatted identifier
        $identifierFormatted = $giftVoucherService->getIdentifierFormatted($giftVoucher->getIdentifier());

        //Returns QrCode
        $qrCode = new QrCode();
        $qrCode
            ->setSize(150)
            ->setMargin(10)
            ->setValidateResult(true)
            ->setText($this->generateUrl('giftvoucher_display', array('identifier' => $identifier), UrlGeneratorInterface::ABSOLUTE_URL))
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setLabel($identifierFormatted)
            ->setLabelFontSize(11)
            ->setLabelAlignment('center')
            ;

        return new Response($qrCode->writeString(), 200, array('Content-Type' => $qrCode->getContentType()));
    }

//HELP
    /**
     * @Route("/gift-voucher/help",
     *      name="giftvoucher_help")
     * @Method({"GET", "HEAD"})
     */
    public function help()
    {
        //Returns the help
        if (null !== $this->getUser() && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            return $this->render('@c975LGiftVoucher/pages/help.html.twig');
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }
}