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
 * Twig extension to display the Offer button for the GiftVoucherAvailable using `gv_offer_button(id)`
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherOfferButton extends \Twig_Extension
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
                'gv_offer_button',
                array($this, 'offerButton'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * Returns the xhtml code for the Offer button
     * @return string
     */
    public function offerButton(\Twig_Environment $environment, $id, $style = 'btn btn-lg btn-block btn-primary')
    {
        //Defines button
        $giftVoucher = $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findOneById($id);
        return $environment->render('@c975LGiftVoucher/fragments/offerButton.html.twig', array(
                'giftVoucher' => $giftVoucher,
                'style' => $style,
            ));
    }
}