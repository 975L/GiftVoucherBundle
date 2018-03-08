<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Twig;

class GiftVoucherIdentifier extends \Twig_Extension
{
    private $service;

    public function __construct(
        \c975L\GiftVoucherBundle\Service\GiftVoucherService $service
        )
    {
        $this->service = $service;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('giftVoucherIdentifier', array($this, 'giftVoucherIdentifier')),
        );
    }

    //Returns Gift Voucher identifier formatted
    public function giftVoucherIdentifier($identifier)
    {
        return $this->service->getIdentifierFormatted($identifier);
    }
}