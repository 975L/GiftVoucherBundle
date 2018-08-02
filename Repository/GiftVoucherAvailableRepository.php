<?php
/*
 * (c) 2018: 975L <contact@975l.com>
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
    //Finds all GiftVouchers available ordered by alphabetical order
    public function findAllAlphabeticalOrder($number = null, $order = 'object')
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->where('v.suppressed is NULL')
            ->orderBy('v.' . strtolower($order), 'ASC');
            ;
        if ($number !== null) {
            $qb->setMaxResults($number);
        }

        return $qb->getQuery()->getResult();
    }

    //Finds all GiftVouchers available
    public function findAllAvailable()
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->where('v.suppressed is NULL')
            ->orderBy('v.id', 'DESC');
            ;

        return $qb;
    }
}
