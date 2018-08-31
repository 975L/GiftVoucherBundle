<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Form\GiftVoucherAvailableType;
use c975L\GiftVoucherBundle\Form\GiftVoucherPurchasedType;
use c975L\GiftVoucherBundle\Form\GiftVoucherFormFactoryInterface;

/**
 * GiftVoucherFormFactory class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherFormFactory implements GiftVoucherFormFactoryInterface
{
    /**
     * Stores container
     * @var ContainerInterface
     */
    private $container;

    /**
     * Stores FormFactoryInterface
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(
        ContainerInterface $container,
        FormFactoryInterface $formFactory
    )
    {
        $this->container = $container;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name, $giftVoucher)
    {
        switch ($name) {
            case 'create':
            case 'modify':
            case 'duplicate':
            case 'delete':
                $config = array('action' => $name);
                $class = GiftVoucherAvailableType::class;
                break;
            case 'offer':
                $config = array('gdpr' => $this->container->getParameter('c975_l_gift_voucher.gdpr'));
                $class = GiftVoucherPurchasedType::class;
                break;
            default:
                $config = array();
                $class = '';
                break;
        }

        return $this->formFactory->create($class, $giftVoucher, array('config' => $config));
    }
}
