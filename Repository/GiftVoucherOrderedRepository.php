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
use c975L\GiftVoucherBundle\Entity\GiftVoucherOrdered;

class GiftVoucherOrderedRepository extends EntityRepository
{
    //Finds next $number events
    public function findBasedOnNumber($number)
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->where('v.number = :number')
            ->andwhere('v.secret = :secret')
            ->andwhere('v.purchase IS NOT NULL')
            ->setParameter('number', substr($number, 0, 12))
            ->setParameter('secret', substr($number, 12))
            ;

        return $qb->getQuery()->getOneOrNullResult();
    }
}