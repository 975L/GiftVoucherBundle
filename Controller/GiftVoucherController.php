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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
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

            //Gets GiftVouchers Purchased
            if ($request->query->get('v') === null || $request->query->get('v') == '') {
                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')->findPurchased(),
                    $request->query->getInt('p', 1),
                    15
                );
            //Gets GiftVouchers Available
            } elseif ($request->query->get('v') == 'available') {
                $paginator  = $this->get('knp_paginator');
                $pagination = $paginator->paginate(
                    $em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')->findAllAvailable(),
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