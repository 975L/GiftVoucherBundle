<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;

/**
 * GiftVoucherPurchased Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class PurchasedController extends Controller
{
//DISPLAY
    /**
     * Displays the GiftVoucherPurchased
     * @return Response
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/{identifier}",
     *      name="giftvoucher_purchased",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
     * @ParamConverter("giftVoucherPurchased",
     *      options={
     *          "repository_method" = "findOneBasedOnIdentifier",
     *          "mapping": {"identifier": "identifier"},
     *          "map_method_signature" = true
     *      })
     */
    public function display(GiftVoucherPurchased $giftVoucherPurchased, AuthorizationCheckerInterface $authChecker)
    {
        //Renders the GiftVoucherPurchased
        return $this->render('@c975LGiftVoucher/pages/display.html.twig', array(
            'giftVoucher' => $giftVoucherPurchased,
            'display' => $authChecker->isGranted($this->getParameter('c975_l_gift_voucher.roleNeeded')) ? 'admin' : 'basic',
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
     *      name="giftvoucher_use",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
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
        return $this->render('@c975LGiftVoucher/pages/display.html.twig', array(
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
     *      name="giftvoucher_use_confirm",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
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

        $now = new \DateTime();
        if (null === $giftVoucherPurchased->getValid() || $giftVoucherPurchased->getValid() > $now || $request->get('force') == 'true') {
            //Marks the GiftVoucherPurchased as used
            $giftVoucherPurchasedService->utilisation($giftVoucherPurchased, $now);
        //Out of date not "forced"
        } else {
            //Renders GiftVoucher to allow force use
            return $this->render('@c975LGiftVoucher/pages/display.html.twig', array(
                'giftVoucher' => $giftVoucherPurchased,
                'display' =>'admin',
                'forceUse' => true,
            ));
        }

        //Redirects to GiftVoucher
        return $this->redirectToRoute('giftvoucher_purchased', array('identifier' => $giftVoucherPurchased->getIdentifier() . $giftVoucherPurchased->getSecret()));
    }
}
