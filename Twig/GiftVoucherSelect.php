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

/**
 * Twig extension to display the select form for GiftVoucherAvailable using `gv_select([id])`
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherSelect extends \Twig_Extension
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
            new \Twig_SimpleFunction(
                'gv_select',
                array($this, 'select'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * Returns the xhtml code for the select for GiftVoucherAvailable
     * @return string
     */
    public function select(\Twig_Environment $environment, $id = 0)
    {
        //Defines button
        $giftVouchers = $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findAllAlphabeticalOrder();
        return $environment->render('@c975LGiftVoucher/fragments/select.html.twig', array(
                'giftVouchers' => $giftVouchers,
                'id' => $id,
            ));
    }
}