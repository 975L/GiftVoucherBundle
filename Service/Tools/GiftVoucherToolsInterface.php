<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Tools;

/**
 * Interface to be called for DI for GiftVoucher Tools related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface GiftVoucherToolsInterface
{
    /**
     * Creates flash message
     */
    public function createFlash(string $object);

    /**
     * Gets the url
     * @return string|null
     */
    public function getUrl(string $data);

    /**
     * Gets url from a Route
     * @return array
     */
    public function getUrlFromRoute(string $route);
}