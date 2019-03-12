<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Form;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * GiftVoucherFormFactory class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherFormFactory implements GiftVoucherFormFactoryInterface
{
    /**
     * Stores ConfigServiceInterface
     * @var ConfigServiceInterface
     */
    private $configService;

    /**
     * Stores FormFactoryInterface
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(
        ConfigServiceInterface $configService,
        FormFactoryInterface $formFactory
    )
    {
        $this->configService = $configService;
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
                $config = array('gdpr' => $this->configService->getParameter('c975LGiftVoucher.gdpr'));
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
