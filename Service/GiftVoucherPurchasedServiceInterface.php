<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use Symfony\Component\Form\Form;
use c975L\PaymentBundle\Entity\Payment;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;

/**
 * Interface to be called for DI for GiftVoucherPurchased Main related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface GiftVoucherPurchasedServiceInterface
{
    /**
     * Defines the GitVoucherPurchased from GiftVoucherAvailable
     * @return GiftVoucherPurchased
     */
    public function create(GiftVoucherAvailable $giftVoucherAvailable);

    /**
     * Shortcut to call GiftVoucherFormFactory to create Form
     * @return Form
     */
    public function createForm(string $name, GiftVoucherPurchased $giftVoucherPurchased);

    /**
     * Defines the identifier of the GiftVoucherPurchased, including the secret code (only capital letters except "o" to avoid confusion with 0)
     * @return string
     */
    public function defineIdentifier();

    /**
     * Gets all the GiftVoucherPurchased
     * @return array
     */
    public function getAll();

    /**
     * Gets xhtml code for GiftVoucherPurchased
     * @return string
     */
    public function getHtml(GiftVoucherPurchased $giftVoucherPurchased);

    /**
     * Returns the formatted identifier to be displayed, without secret code
     * @returns string
     */
    public function getIdentifierFormatted(string $identifier);

    /**
     * Registers the GiftVoucherPurchased
     */
    public function register(GiftVoucherPurchased $giftVoucherPurchased, bool $test);

    /**
     * Marks the GiftVoucherPurchased as used
     */
    public function utilisation(GiftVoucherPurchased $giftVoucherPurchased, \DateTime $now);

    /**
     * Validates the GiftVoucherPurchased after its payment
     * @return string|false
     */
    public function validate(Payment $payment);
}
