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
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;

/**
 * Interface to be called for DI for GiftVoucherFormFactoryInterface related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface GiftVoucherFormFactoryInterface
{
    /**
     * Returns the defined form
     * @return Form
     */
    public function create(string $name, $giftVoucher);
}
