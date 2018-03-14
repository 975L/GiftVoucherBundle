<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Twig;

class GiftVoucherSamples extends \Twig_Extension
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
                'gv_samples',
                array($this, 'select'),
                array(
                    'needs_environment' => true,
                    'is_safe' => array('html'),
                )
            ),
        );
    }

    public function select(\Twig_Environment $environment, $number = null, $order = 'object')
    {
        //Gets repository
        $repository = $this->em->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable');

        //Loads from DB
        $giftVouchers = $repository->findAllAlphabeticalOrder($number, $order);

        //Defines button
        return $environment->render('@c975LGiftVoucher/fragments/samples.html.twig', array(
                'giftVouchers' => $giftVouchers,
            ));
    }
}