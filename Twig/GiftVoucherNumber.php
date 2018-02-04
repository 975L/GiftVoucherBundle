<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Twig;

class GiftVoucherNumber extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('giftVoucherNumber', array($this, 'giftVoucherNumber')),
        );
    }

    //Returns Gift Voucher number formatted
    public function giftVoucherNumber($number)
    {
        return sprintf("%s-%s-%s", substr($number, 0, 4), substr($number, 4, 4), substr($number, 8, 4));
    }
}