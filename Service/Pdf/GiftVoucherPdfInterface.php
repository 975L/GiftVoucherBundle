<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Pdf;

/**
 * Interface to be called for DI for GiftVoucher Pdf related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface GiftVoucherPdfInterface
{
    /**
     * Creates PDF for GiftVoucherPurchased
     * @return array
     */
    public function getPdf(string $html, string $identifierFormatted);

    /**
     * Gets the Terms of sales PDF from config value
     * @return array|null
     */
    public function getTosPdf(string $tosPdfUrl);
}
