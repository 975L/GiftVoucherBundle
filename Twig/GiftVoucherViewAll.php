<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Twig;

use Doctrine\ORM\EntityManagerInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Twig extension to display all the GiftVoucherAvailable as a list (object, description, offer button) using `gv_view_all([$number, $orderField])`
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherViewAll extends Twig_Extension
{
    /**
     * Stores EntityManager
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            new Twig_SimpleFunction(
                'gv_view_all',
                array($this, 'viewAll'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * Returns the xhtml code to view all the GiftVoucherAvailable
     * @return string
     */
    public function viewAll(Twig_Environment $environment, $number = null, $order = 'object')
    {
        //Defines button
        $giftVouchers = $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findAllAlphabeticalOrder($number, $order);
        return $environment->render('@c975LGiftVoucher/fragments/viewAll.html.twig', array(
                'giftVouchers' => $giftVouchers,
            ));
    }
}
