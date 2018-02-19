<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Repository;

use Doctrine\ORM\EntityRepository;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;

class GiftVoucherAvailableRepository extends EntityRepository
{
    //Finds all GiftVoucher available
    public function findAvailable()
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->where('v.suppressed is NULL')
            ->orderBy('v.id', 'DESC');
            ;

        return $qb;
    }
}