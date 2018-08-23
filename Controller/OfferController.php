<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Form\GiftVoucherPurchasedType;
use c975L\GiftVoucherBundle\Service\OfferServiceInterface;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use c975L\GiftVoucherBundle\Service\Payment\GiftVoucherPaymentInterface;
use c975L\GiftVoucherBundle\Service\Slug\GiftVoucherSlugInterface;
use c975L\GiftVoucherBundle\Service\Tools\GiftVoucherToolsInterface;

/**
 * Offer Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class OfferController extends Controller
{
    /**
     * Stores Purchased Service
     * @var GiftVoucherPurchasedServiceInterface
     */
    private $giftVoucherPurchasedService;

    /**
     * Stores GiftVoucherPayment Service
     * @var GiftVoucherPaymentInterface
     */
    private $giftVoucherPayment;

    /**
     * Stores GiftVoucherSlug Service
     * @var GiftVoucherSlugInterface
     */
    private $giftVoucherSlug;

    /**
     * Stores GiftVoucherTools Service
     * @var GiftVoucherToolsInterface
     */
    private $giftVoucherTools;

    public function __construct(
        GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService,
        GiftVoucherPaymentInterface $giftVoucherPayment,
        GiftVoucherSlugInterface $giftVoucherSlug,
        GiftVoucherToolsInterface $giftVoucherTools
    )
    {
        $this->giftVoucherPurchasedService = $giftVoucherPurchasedService;
        $this->giftVoucherPayment = $giftVoucherPayment;
        $this->giftVoucherSlug = $giftVoucherSlug;
        $this->giftVoucherTools = $giftVoucherTools;
    }

//OFFER ALL
    /**
     * Displays all the GiftVoucherAvailable that can be offered
     * @return Response
     *
     * @Route("/gift-voucher/offer",
     *      name="giftvoucher_offer_all")
     * @Method({"GET", "HEAD", "POST"})
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
     *      name="giftvoucher_offer_id_redirect",
     *      requirements={
     *          "id": "^([0-9])+$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
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
     *      name="giftvoucher_offer",
     *      requirements={
     *          "slug": "^([a-zA-Z0-9\-]+)$",
     *          "id": "^([0-9]+)$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     * @ParamConverter("giftVoucherAvailable", options={"mapping": {"id": "id"}})
     */
    public function offer(Request $request, GiftVoucherAvailable $giftVoucherAvailable, $slug)
    {
        //Redirects to good slug
        $redirectUrl = $this->giftVoucherSlug->match('giftvoucher_offer', $giftVoucherAvailable, $slug);
        if (null !== $redirectUrl) {
            return new RedirectResponse($redirectUrl);
        }

        //Defines form
        $giftVoucherPurchased = $this->giftVoucherPurchasedService->create($giftVoucherAvailable);
        $giftVoucherConfig = array(
            'gdpr' => $this->getParameter('c975_l_gift_voucher.gdpr'),
        );
        $form = $this->createForm(GiftVoucherPurchasedType::class, $giftVoucherPurchased, array('giftVoucherConfig' => $giftVoucherConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the GitftVoucherPurchased
            $this->giftVoucherPurchasedService->register($giftVoucherPurchased, $this->getParameter('c975_l_gift_voucher.live'));

            //Redirects to the payment
            $this->giftVoucherPayment->payment($giftVoucherPurchased, $this->getUser());
            return $this->redirectToRoute('payment_form');
        }

        //Renders the offer form
        return $this->render('@c975LGiftVoucher/forms/offer.html.twig', array(
            'form' => $form->createView(),
            'giftVoucher' => $giftVoucherPurchased,
            'giftVoucherAvailable' => $giftVoucherAvailable,
            'live' => $this->getParameter('c975_l_gift_voucher.live'),
            'tosUrl' => $this->giftVoucherTools->getUrl($this->getParameter('c975_l_gift_voucher.tosUrl')),
        ));
    }
}
