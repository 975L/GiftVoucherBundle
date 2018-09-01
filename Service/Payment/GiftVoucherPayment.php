<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Payment;

use Symfony\Component\Translation\TranslatorInterface;
use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\PaymentBundle\Service\PaymentServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\Payment\GiftVoucherPaymentInterface;

/**
 * Services related to GiftVoucher Payment
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherPayment implements GiftVoucherPaymentInterface
{
    /**
     * Stores ConfigServiceInterface
     * @var ConfigServiceInterface
     */
    private $configService;

    /**
     * Stores PaymentService
     * @var PaymentServiceInterface
     */
    private $paymentService;

    /**
     * Stores Translator
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ConfigServiceInterface $configService,
        PaymentServiceInterface $paymentService,
        TranslatorInterface $translator
    )
    {
        $this->configService = $configService;
        $this->paymentService = $paymentService;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function payment(GiftVoucherPurchased $giftVoucherPurchased, $user)
    {
        $paymentData = array(
            'amount' => $giftVoucherPurchased->getAmount(),
            'currency' => $giftVoucherPurchased->getCurrency(),
            'action' => json_encode(array('validateGiftVoucher' => $giftVoucherPurchased->getId())),
            'description' => $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' - ' . $giftVoucherPurchased->getObject(),
            'userId' => null !== $user ? $user->getId() : null,
            'userIp' => $giftVoucherPurchased->getUserIp(),
            'live' => $this->configService->getParameter('c975LGiftVoucher.live'),
            'returnRoute' => 'giftvoucher_payment_done',
            'vat' => $this->configService->getParameter('c975LGiftVoucher.vat'),
            );

        $this->paymentService->create($paymentData);
    }
}
