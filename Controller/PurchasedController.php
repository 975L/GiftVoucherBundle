<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * GiftVoucherPurchased Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class PurchasedController extends AbstractController
{
//DISPLAY
    /**
     * Displays the GiftVoucherPurchased
     * @return Response
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/{identifier}",
     *    name="giftvoucher_purchased",
     *    requirements={"identifier": "^([a-zA-Z]{16})$"},
     *    methods={"HEAD", "GET"})
     * @ParamConverter("giftVoucherPurchased",
     *      options={
     *          "repository_method" = "findOneBasedOnIdentifier",
     *          "mapping": {"identifier": "identifier"},
     *          "map_method_signature" = true
     *      })
     */
    public function display(GiftVoucherPurchased $giftVoucherPurchased, AuthorizationCheckerInterface $authChecker, ConfigServiceInterface $configService)
    {
        //Renders the GiftVoucherPurchased
        return $this->render(
            '@c975LGiftVoucher/pages/display.html.twig',
            array(
                'giftVoucher' => $giftVoucherPurchased,
                'display' => $authChecker->isGranted($configService->getParameter('c975LGiftVoucher.roleNeeded')) ? 'admin' : 'basic',
            ));
    }

//USE
    /**
     * Displays the form to mark the GiftVoucherPurchased as used by the merchant
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/use/{identifier}",
     *    name="giftvoucher_use",
     *    requirements={"identifier": "^([a-zA-Z]{16})$"},
     *    methods={"HEAD", "GET"})
     * @ParamConverter("giftVoucherPurchased",
     *      options={
     *          "repository_method" = "findOneBasedOnIdentifier",
     *          "mapping": {"identifier": "identifier"},
     *          "map_method_signature" = true
     *      })
     */
    public function utilisation(GiftVoucherPurchased $giftVoucherPurchased)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-utilisation', $giftVoucherPurchased);

        //Renders the GiftVoucherPurchased
        return $this->render(
            '@c975LGiftVoucher/pages/display.html.twig',
            array(
                'giftVoucher' => $giftVoucherPurchased,
                'display' => 'admin',
            ));
    }

//USE CONFIRMATION
    /**
     * Marks the GiftVoucherPurchased as used by the merchant
     * @return Response
     * @throws NotFoundHttpException
     * @throws AccessDeniedException
     *
     * @Route("/gift-voucher/use/confirm/{identifier}",
     *    name="giftvoucher_use_confirm",
     *    requirements={"identifier": "^([a-zA-Z]{16})$"},
     *    methods={"HEAD", "GET"})
     * @ParamConverter("giftVoucherPurchased",
     *      options={
     *          "repository_method" = "findOneBasedOnIdentifier",
     *          "mapping": {"identifier": "identifier"},
     *          "map_method_signature" = true
     *      })
     */
    public function utilisationConfirm(Request $request, GiftVoucherPurchased $giftVoucherPurchased, GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService)
    {
        $this->denyAccessUnlessGranted('c975LGiftVoucher-utilisation-confirm', $giftVoucherPurchased);

        $now = new DateTime();
        if (null === $giftVoucherPurchased->getValid() || $giftVoucherPurchased->getValid() > $now || $request->get('force') == 'true') {
            //Marks the GiftVoucherPurchased as used
            $giftVoucherPurchasedService->utilisation($giftVoucherPurchased, $now);
        //Out of date not "forced"
        } else {
            //Renders GiftVoucher to allow force use
            return $this->render(
                '@c975LGiftVoucher/pages/display.html.twig',
                array(
                    /**
     * {@inheritdoc}
     */

        }

        //Redirects to GiftVoucher
        return $this->redirectToRoute('giftvoucher_purchased', array('identifier' => $giftVoucherPurchased->getIdentifier() . $giftVoucherPurchased->getSecret()));
    }
}
