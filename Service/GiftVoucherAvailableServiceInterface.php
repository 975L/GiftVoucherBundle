<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use Symfony\Component\Form\Form;

/**
 * Interface to be called for DI for GiftVoucherAvailable Main related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface GiftVoucherAvailableServiceInterface
{
    /**
     * Clones the object
     * @return GiftVoucherAvailable
     */
    public function cloneObject(GiftVoucherAvailable $giftVoucherAvailable);

    /**
     * Shortcut to call GiftVoucherFormFactory to create Form
     * @return Form
     */
    public function createForm(string $name, GiftVoucherAvailable $giftVoucherAvailable);

    /**
     * Deletes the GiftVoucherAvailable
     */
    public function delete(GiftVoucherAvailable $giftVoucherAvailable);

    /**
     * Gets all the GiftVoucherAvailable
     * @return array
     */
    public function getAll();

    /**
     * Registers the GiftVoucherAvailable
     */
    public function register(GiftVoucherAvailable $giftVoucherAvailable);
}
