<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use c975L\GiftVoucherBundle\Service\Payment\GiftVoucherPaymentInterface;
use c975L\ServicesBundle\Service\ServiceSlugInterface;
use c975L\ServicesBundle\Service\ServiceToolsInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Offer Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class OfferController extends AbstractController
{
    /**
     * Stores GiftVoucherPurchasedServiceInterface
     * @var GiftVoucherPurchasedServiceInterface
     */
    private $giftVoucherPurchasedService;
    /**
     * Stores GiftVoucherPaymentInterface
     * @var GiftVoucherPaymentInterface
     */
    private $giftVoucherPayment;
    /**
     * Stores ServiceSlugInterface
     * @var ServiceSlugInterface
     */
    private $serviceSlug;
    /**
     * Stores ServiceToolsInterface
     * @var ServiceToolsInterface
     */
    private $serviceTools;

    public function __construct(
        GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService,
        GiftVoucherPaymentInterface $giftVoucherPayment,
        ServiceSlugInterface $serviceSlug,
        ServiceToolsInterface $serviceTools
    )
    {
        $this->giftVoucherPurchasedService = $giftVoucherPurchasedService;
        $this->giftVoucherPayment = $giftVoucherPayment;
        $this->serviceSlug = $serviceSlug;
        $this->serviceTools = $serviceTools;
    }

//OFFER ALL
    /**
     * Displays all the GiftVoucherAvailable that can be offered
     * @return Response
     *
     * @Route("/gift-voucher/offer",
     *    name="giftvoucher_offer_all",
     *    methods={"HEAD", "GET", "POST"})
     */
    public function offerAll(Request $request)
    {
        //Renders the page
        $giftVouchers = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findNotSuppressed();
        return $this->render('@c975LGiftVoucher/pages/offerAll.html.twig', array(
            'giftVouchers' => $giftVouchers,
        ));
    }

//OFFER
    /**
     * Redirects to the Route containing slug to display the specific GiftVoucherAvailable offer
     * @return Redirect
     *
     * @Route("/gift-voucher/offer/{id}",
     *    name="giftvoucher_offer_id_redirect",
     *    requirements={"id": "^([0-9])+$"},
     *    methods={"HEAD", "GET", "POST"})
     */
    public function offerIdRedirect(Request $request, GiftVoucherAvailable $giftVoucherAvailable)
    {
        //Redirects to the GiftVoucher
        return $this->redirectToRoute('giftvoucher_offer', array(
            'slug' => $giftVoucherAvailable->getSlug(),
            'id' => $giftVoucherAvailable->getId(),
        ));
    }
    /**
     * Displays the specific GiftVoucherAvailable offer
     * @return Response
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/offer/{slug}/{id}",
     *    name="giftvoucher_offer",
     *    requirements={
     *        "slug": "^([a-zA-Z0-9\-]+)$",
     *        "id": "^([0-9]+)$"
     *    },
     *    methods={"HEAD", "GET", "POST"})
     * @ParamConverter("giftVoucherAvailable", options={"mapping": {"id": "id"}})
     */
    public function offer(Request $request, GiftVoucherAvailable $giftVoucherAvailable, ConfigServiceInterface $configService, $slug)
    {
        //Redirects to good slug
        $redirectUrl = $this->serviceSlug->match('giftvoucher_offer', $giftVoucherAvailable, $slug);
        if (null !== $redirectUrl) {
            return new RedirectResponse($redirectUrl);
        }

        //Defines form
        $giftVoucherPurchased = $this->giftVoucherPurchasedService->create($giftVoucherAvailable);
        $form = $this->giftVoucherPurchasedService->createForm('offer', $giftVoucherPurchased);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the GitftVoucherPurchased
            $this->giftVoucherPurchasedService->register($giftVoucherPurchased, $configService->getParameter('c975LGiftVoucher.live'));

            //Redirects to the payment
            $this->giftVoucherPayment->payment($giftVoucherPurchased, $this->getUser());
            return $this->redirectToRoute('payment_form');
        }

        //Renders the offer form
        return $this->render('@c975LGiftVoucher/forms/offer.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherPurchased,
            'giftVoucherAvailable' => $giftVoucherAvailable,
            'live' => $configService->getParameter('c975LGiftVoucher.live'),
            'tosUrl' => $this->serviceTools->getUrl($configService->getParameter('c975LGiftVoucher.tosUrl')),
        ));
    }
}
