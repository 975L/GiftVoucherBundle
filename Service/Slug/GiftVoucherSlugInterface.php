<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Slug;

use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;

/**
 * Interface to be called for DI for GiftVoucher Slug related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface GiftVoucherSlugInterface
{
    /**
     * Checks if slug already exists
     * @return bool
     */
    public function exists(string $slug);

    /**
     * Checks if url provided slug match GiftVoucherAvailable slug or will provide redirect url
     * @return string|null
     */
    public function match(string $route, GiftVoucherAvailable $eventObject, string $slug);

    /**
     * Slugify function - https://github.com/cocur/slugify
     * @return string
     */
    public function slugify(string $text);
}