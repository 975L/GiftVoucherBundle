<?php
/*
 * (c) 2017: 975l <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use c975L\Email\Service\EmailService;
use c975L\PaymentBundle\Entity\StripePayment;
use c975L\PaymentBundle\Service\StripePaymentService;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherOrdered;
use c975L\GiftVoucherBundle\Form\GiftVoucherType;
use c975L\GiftVoucherBundle\Service\GiftVoucherService;

class GiftVoucherController extends Controller
{
//DASHBOARD
    /**
     * @Route("/gift-voucher/dashboard",
     *      name="giftvoucher_dashboard")
     * @Method({"GET", "HEAD"})
     */
    public function dashboardAction(Request $request)
    {
        //Gets the user
        $user = $this->getUser();

        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repositoryOrdered = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherOrdered');

            //Pagination
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $repositoryOrdered->findBy(array('used' => null), array('id' => 'DESC')),
                $request->query->getInt('p', 1),
                15
            );

            //Defines toolbar
            $tools  = $this->renderView('@c975LGiftVoucher/tools.html.twig', array(
                'type' => 'dashboard',
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'product'  => 'giftvoucher',
            ))->getContent();

            //Returns the dashboard
            return $this->render('@c975LGiftVoucher/pages/dashboard.html.twig', array(
                'giftVouchersOrdered' => $pagination,
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DISPLAY
    /**
     * @Route("/gift-voucher/{number}",
     *      name="giftvoucher_display",
     *      requirements={"number": "^[a-zA-Z0-9]{16}$"})
     * @Method({"GET", "HEAD"})
     */
    public function displayAction(Request $request, $number)
    {
        //Gets the manager
        $em = $this->getDoctrine()->getManager();

        //Gets repository
        $repository = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherOrdered');

        //Loads from DB
        $giftVoucher = $repository->findBasedOnNumber($number);

        //Not existing GiftVoucher
        if (!$giftVoucher instanceof GiftVoucherOrdered) {
            throw $this->createNotFoundException();
        }

        //Gets the user
        $user = $this->getUser();

        //Defines display rights
        $display = 'basic';

        //Defines toolbar
        $toolbar = '';
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            $display = 'admin';
            $tools  = $this->renderView('@c975LGiftVoucher/tools.html.twig', array(
                'type' => 'display',
                'giftVoucher' => $giftVoucher,
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'product'  => 'giftvoucher',
            ))->getContent();
        }

        return $this->render('@c975LGiftVoucher/pages/display.html.twig', array(
            'toolbar' => $toolbar,
            'giftVoucher' => $giftVoucher,
            'display' => $display,
        ));
    }

//QRCODE
    /**
     * @Route("/gift-voucher/qrcode/{number}",
     *      name="giftvoucher_qrcode",
     *      requirements={"number": "^[a-zA-Z0-9]{16}$"})
     * @Method({"GET", "HEAD"})
     */
    public function qrcodeAction($number)
    {
        $qrCode = new QrCode();
        $qrCode
            ->setSize(150)
            ->setMargin(10)
            ->setValidateResult(true)
            ->setText($this->generateUrl('giftvoucher_display', array('number' => $number), UrlGeneratorInterface::ABSOLUTE_URL))
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH)
            ->setLabel(sprintf("%s-%s-%s", substr($number, 0, 4), substr($number, 4, 4), substr($number, 8, 4)))
            ->setLabelFontSize(14)
            ->setLabelAlignment('center')
            ;

        return new Response($qrCode->writeString(), 200, array('Content-Type' => $qrCode->getContentType()));
    }

//USE
    /**
     * @Route("/gift-voucher/use/{number}",
     *      name="giftvoucher_use",
     *      requirements={"number": "^[a-zA-Z0-9]{16}$"})
     * @Method({"GET", "HEAD"})
     */
    public function useAction(Request $request, $number)
    {
        //Gets the user
        $user = $this->getUser();

        //Allows use if user has rights
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repository = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherOrdered');

            //Loads from DB
            $giftVoucher = $repository->findBasedOnNumber($number);

            //Not existing gift voucher
            if (!$giftVoucher instanceof GiftVoucherOrdered) {
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
            //Not valid
            } else {
//TODO
            }

            return $this->redirectToRoute('giftvoucher_display', array('number' => $number));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//PURCHASE
    /**
     * @Route("/gift-voucher/purchase/{id}",
     *      name="giftvoucher_purchase_id_redirect",
     *      requirements={
     *          "id": "^[0-9]+$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function purchaseIdRedirectAction(Request $request, $id)
    {
        //Gets the manager
        $em = $this->getDoctrine()->getManager();

        //Gets repository
        $repositoryAvailable = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable');

        //Loads from DB
        $giftVoucherAvailable = $repositoryAvailable->findOneById($id);

        //Not existing gift voucher
        if (!$giftVoucherAvailable instanceof GiftVoucherAvailable) {
            throw $this->createNotFoundException();
        }

        //Redirects to the gift-voucher
        return $this->redirectToRoute('giftvoucher_purchase', array(
            'slug' => $giftVoucherAvailable->getSlug(),
            'id' => $giftVoucherAvailable->getId(),
        ));
    }
    /**
     * @Route("/gift-voucher/purchase/{slug}/{id}",
     *      name="giftvoucher_purchase",
     *      requirements={
     *          "slug": "^[a-zA-Z0-9\-]+$",
     *          "id": "^[0-9]+$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function purchaseAction(Request $request, $slug, $id)
    {
        //Gets the manager
        $em = $this->getDoctrine()->getManager();

        //Gets repository
        $repositoryAvailable = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable');

        //Loads from DB
        $giftVoucherAvailable = $repositoryAvailable->findOneById($id);

        //Not existing gift voucher
        if (!$giftVoucherAvailable instanceof GiftVoucherAvailable) {
            throw $this->createNotFoundException();
        }

        //Wrong slug redirects to right one
        if ($slug != $giftVoucherAvailable->getSlug()) {
            return $this->redirectToRoute('giftvoucher_purchase', array(
                'slug' => $giftVoucherAvailable->getSlug(),
                'id' => $giftVoucherAvailable->getId(),
            ));
        }

        //Defines form
        $giftVoucherOrdered = new GiftVoucherOrdered();
        $giftVoucherOrdered
            ->setObject($giftVoucherAvailable->getObject())
            ->setDescription($giftVoucherAvailable->getDescription())
            ->setAmount($giftVoucherAvailable->getAmount())
            ->setCurrency($giftVoucherAvailable->getCurrency())
            ->setValid(new \DateTime('+ ' . $giftVoucherAvailable->getValid() . ' days'))
            ;

        $form = $this->createForm(GiftVoucherType::class, $giftVoucherOrdered);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Persists data in DB
            $em->persist($giftVoucherOrdered);
            $em->flush();

            //Payment
            $stripeData = array(
                'amount' => $giftVoucherOrdered->getAmount(),
                'currency' => $giftVoucherOrdered->getCurrency(),
                'action' => json_encode(array('validateGiftVoucher' => $giftVoucherOrdered->getId())),
                'description' => $giftVoucherOrdered->getDescription(),
                'userIp' => $request->getClientIp(),
                );
            $stripeService = $this->get(\c975L\PaymentBundle\Service\StripePaymentService::class);
            $stripeService->create($stripeData);

            //Redirects to the payment
            return $this->redirectToRoute('payment_display');
        }

        return $this->render('@c975LGiftVoucher/forms/purchase.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherOrdered,
            'giftVoucherAvailable' => $giftVoucherAvailable,
        ));
    }

//PAYMENT DONE
    /**
     * @Route("/payment-done/{orderId}",
     *      name="payment_done")
     * @Method({"GET", "HEAD"})
     */
    public function paymentDone(Request $request, $orderId)
    {
        //Gets the manager
        $em = $this->getDoctrine()->getManager();

        //Gets Stripe payment
        $stripePayment = $em->getRepository('c975L\PaymentBundle\Entity\StripePayment')
            ->findOneByOrderId($orderId);
        if (!$stripePayment instanceof StripePayment) {
            throw $this->createNotFoundException();
        }

        //StripePayment executed
        if ($stripePayment->getStripeToken() !== null) {
            //Sets stripePayment as finished
            if ($stripePayment->getFinished() !== true) {
                //Validates the GiftVoucher
                $action = (array) json_decode($stripePayment->getAction());

                if (array_key_exists('validateGiftVoucher', $action)) {
                    $repository = $em->getRepository('c975LGiftVoucherBundle:GiftVoucherOrdered');
                    $giftVoucher = $repository->findOneById($action['validateGiftVoucher']);

                    //Gets number
                    $giftVoucherService = $this->get(\c975L\GiftVoucherBundle\Service\GiftVoucherService::class);
                    $numberExists = true;
                    do {
                        $number = $giftVoucherService->getNumber();
                        $numberExists = $repository->findOneBy(array('number' => substr($number, 0, 12), 'secret' => substr($number, 12)));
                    } while ($numberExists !== null);

                    //Updates GiftVoucher
                    $giftVoucher
                        ->setPurchase(new \DateTime())
                        ->setNumber(substr($number, 0, 12))
                        ->setSecret(substr($number, 12))
                        ;

                    //Creates PDF
                    $translator = $this->get('translator');
                    $html = $this->renderView('@c975LGiftVoucher/pages/pdf.html.twig', array(
                        'giftVoucher' => $giftVoucher,
                        'display' => 'pdf',
                    ));
                    $numberFormatted = sprintf("%s-%s-%s", substr($number, 0, 4), substr($number, 4, 4), substr($number, 8, 4));
                    $subject = $translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' "' . $giftVoucher->getObject() . '" (' . $numberFormatted . ')';
                    $filename = $translator->trans('label.gift_voucher', array(), 'giftVoucher') . '-' . $giftVoucher->getNumber() . '.pdf';
                    $pdf = $this->get('knp_snappy.pdf')->getOutputFromHtml($html);

                    //Sends email
                    $emailData = array(
                        'subject' => $subject,
                        'sentFrom' => $this->getParameter('c975_l_email.sentFrom'),
                        'sentTo' => $giftVoucher->getSendToEmail(),
                        'replyTo' => $this->getParameter('c975_l_email.sentFrom'),
                        'body' => $html,
                        'attach' => array($pdf, $filename, 'application/pdf'),
                        'ip' => $request->getClientIp(),
                        );

                    //Sends email
                    $emailService = $this->get(\c975L\EmailBundle\Service\EmailService::class);
                    $emailService->send($emailData, true);

                    //Updates the payment to set it finished
                    $stripePayment->setFinished(true);

                    //Persist in database
                    $em->persist($stripePayment);
                    $em->persist($giftVoucher);
                    $em->flush();

                    //Creates flash
                    $translator = $this->get('translator');
                    $flash = $translator->trans('text.voucher_purchased', array(), 'giftVoucher');
                    $request->getSession()
                        ->getFlashBag()
                        ->add('success', $flash)
                        ;
                }

                //Redirects to the Gift-Voucher
                return $this->redirectToRoute('giftvoucher_display', array(
                    'number' => $giftVoucher->getNumber() . $giftVoucher->getSecret(),
                ));
            //Payment already finished
            } else {
                return $this->redirectToRoute('payment_order', array(
                    'orderId' => $orderId,
                ));
            }
        //StripePayment not executed
        } else {
            $stripeService = $this->get(StripePaymentService::class);
            $stripeService->reUse($stripePayment);

            //Display the payment data
            return $this->render('@c975LPayment/pages/orderNotExecuted.html.twig', array(
                'payment' => $stripePayment,
            ));
        }
    }

//NEW
    /**
     * @Route("/gift-voucher/new",
     *      name="giftvoucher_new")
     * @Method({"GET", "HEAD"})
     */
    public function newAction()
    {
dump('here');die;
        //Gets the user
        $user = $this->getUser();

        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
        }
    }

//EDIT
    /**
     * @Route("/gift-voucher/edit/{id}",
     *      name="giftvoucher_edit")
     * @Method({"GET", "HEAD"})
     */
    public function editAction()
    {
dump('here');die;
        //Gets the user
        $user = $this->getUser();

        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
        }
    }

//DUPLICATE
    /**
     * @Route("/gift-voucher/duplicate/{id}",
     *      name="giftvoucher_duplicate")
     * @Method({"GET", "HEAD"})
     */
    public function duplicateAction()
    {
dump('here');die;
        //Gets the user
        $user = $this->getUser();

        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
        }
    }

//DELETE
    /**
     * @Route("/gift-voucher/delete/{id}",
     *      name="giftvoucher_delete")
     * @Method({"GET", "HEAD"})
     */
    public function deleteAction()
    {
dump('here');die;
        //Gets the user
        $user = $this->getUser();

        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded'))) {
        }
    }

//HELP
    /**
     * @Route("/gift-voucher/help",
     *      name="giftvoucher_help")
     * @Method({"GET", "HEAD"})
     */
    public function helpAction()
    {
dump('here');die;
        //Gets the user
        $user = $this->getUser();

        //Returns the dashboard content
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_page_edit.roleNeeded'))) {
            //Returns the help
            return $this->render('@c975LPageEdit/pages/help.html.twig', array(
                'toolbar' => $this->renderView('@c975LPageEdit/toolbar.html.twig', array(
                    'type' => 'help',
                    'dashboardRoute' => $this->getParameter('c975_l_page_edit.dashboardRoute'),
                    'signoutRoute' => $this->getParameter('c975_l_page_edit.signoutRoute'),
                )),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }
}