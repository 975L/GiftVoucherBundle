<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use c975L\GiftVoucherBundle\Entity\StripeGiftVoucher;

class GiftVoucherService
{
    //Defines the number of the Gift-Voucher, including the secret code
    public function getNumber()
    {
        //Defines data
        $signsRemoved = array('o', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $signsReplacing = array('t', 'y', 'f', 'r', 'k', 'h', 'x', 'p', 'l', 'm', 'a');
        $number = strtoupper(substr(str_replace($signsRemoved, $signsReplacing, md5(time())), 12, 16));

        return $number;
    }
}