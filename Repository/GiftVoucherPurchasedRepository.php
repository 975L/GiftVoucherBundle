<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Repository;

use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use Doctrine\ORM\EntityRepository;

/**
 * Repository for Event Entity
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherPurchasedRepository extends EntityRepository
{
    /**
     * Finds GiftVoucherPurchased based on Identifier, so including secret code
     * @return mixed
     */
    //
    public function findOneBasedOnIdentifier($identifier)
    {
        $identifier = strtoupper($identifier);

        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->where('v.identifier = :identifier')
            ->andwhere('v.secret = :secret')
            ->setParameter('identifier', substr($identifier, 0, 12))
            ->setParameter('secret', substr($identifier, 12))
            ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Finds GiftVoucherPurchased NOT used
     * @return QueryBuilder
     */
    public function findAll()
    {
        $qb = $this->createQueryBuilder('v');
        $qb->select('v')
            ->where('v.used IS NULL')
            ->andwhere('v.identifier IS NOT NULL')
            ->orderBy('v.id', 'DESC');
            ;

        return $qb;
    }
}