<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Twig;

use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Twig extension to display the formatted GiftVoucherPurchased identifier using `|gv_identifier`
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherIdentifier extends AbstractExtension
{
    /**
     * Stores purchasedService
     * @var GiftVoucherPurchasedServiceInterface
     */
    private $giftVoucherPurchasedService;

    public function __construct(GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService)
    {
        $this->giftVoucherPurchasedService = $giftVoucherPurchasedService;
    }

    public function getFilters()
    {
        return array(
            new TwigFilter('gv_identifier', array($this, 'identifier')),
        );
    }

    /**
     * Returns GiftVoucherPurchased identifier formatted
     * @return string
     */
    public function identifier($identifier)
    {
        return $this->giftVoucherPurchasedService->getIdentifierFormatted($identifier);
    }
}
