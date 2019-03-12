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
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension to display the Link for the GiftVoucherAvailable using `gv_offer_link(id)`
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherOfferLink extends AbstractExtension
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
            new TwigFunction(
                'gv_offer_link',
                array($this, 'offerLink'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    /**
     * Returns the xhtml code for the Offer link
     * @return string
     */
    public function offerLink(Environment $environment, $id)
    {
        //Defines link
        $giftVoucher = $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findOneById($id);
        return $environment->render('@c975LGiftVoucher/fragments/offerLink.html.twig', array(
                'giftVoucher' => $giftVoucher,
            ));
    }
}
