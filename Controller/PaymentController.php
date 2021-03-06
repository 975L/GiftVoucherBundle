<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use c975L\PaymentBundle\Entity\Payment;
use c975L\PaymentBundle\Service\PaymentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * GiftVoucherPurchased Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class PaymentController extends AbstractController
{
//PAYMENT DONE
    /**
     * Return Route after having done payment
     * @return Redirect
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/payment-done/{orderId}",
     *    name="giftvoucher_payment_done",
     *    methods={"HEAD", "GET"})
     */
    public function paymentDone(GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService, PaymentServiceInterface $paymentService, Payment $payment)
    {
        $giftVoucherIdentifier = $giftVoucherPurchasedService->validate($payment);

        //Redirects to the GiftVoucherPurchased
        if (false !== $giftVoucherIdentifier) {
            return $this->redirectToRoute(
                'giftvoucher_purchased',
                array(
                    'identifier' => $giftVoucherIdentifier,
                ));
        }

        //Payment has been done but GiftVoucher was not validated
        $paymentService->error($payment);

        return $this->redirectToRoute(
            'payment_display',
            array(
                'orderId' => $payment->getOrderId(),
            ));
    }
}
