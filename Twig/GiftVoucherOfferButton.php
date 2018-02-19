<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Twig;

class GiftVoucherOfferButton extends \Twig_Extension
{
    private $em;

    public function __construct(
        \Doctrine\ORM\EntityManagerInterface $em
        )
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'gift_voucher_offer_button',
                array($this, 'offerButton'),
                array('needs_environment' => true)
            ),
        );
    }

    public function offerButton(\Twig_Environment $environment, $id)
    {
        //Gets repository
        $repository = $this->em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable');

        //Loads from DB
        $giftVoucher = $repository->findOneById($id);

        //Defines button
        return $environment->render('@c975LGiftVoucher/fragments/offerButton.html.twig', array(
                'giftVoucher' => $giftVoucher,
            ));
    }
}