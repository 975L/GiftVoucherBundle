<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Service\GiftVoucherAvailableServiceInterface;
use c975L\GiftVoucherBundle\Service\Slug\GiftVoucherSlugInterface;
use c975L\GiftVoucherBundle\Service\Tools\GiftVoucherToolsInterface;

/**
 * Interface to be called for DI for GiftVoucherAvailable Main related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherAvailableService implements GiftVoucherAvailableServiceInterface
{
    /**
     * Stores EntityManager
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Stores GiftVoucherSlug
     * @var GiftVoucherSlugInterface
     */
    private $giftVoucherSlug;

    /**
     * Stores GiftVoucherTools
     * @var GiftVoucherToolsInterface
     */
    private $giftVoucherTools;

    public function __construct(
        EntityManagerInterface $em,
        GiftVoucherSlugInterface $giftVoucherSlug,
        GiftVoucherToolsInterface $giftVoucherTools
    )
    {
        $this->em = $em;
        $this->giftVoucherSlug = $giftVoucherSlug;
        $this->giftVoucherTools = $giftVoucherTools;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(GiftVoucherAvailable $giftVoucherAvailable)
    {
        $giftVoucherAvailable->setSuppressed(true);

        //Persists data in DB
        $this->em->remove($giftVoucherAvailable);
        $this->em->flush();

        //Creates flash
        $this->giftVoucherTools->createFlash('voucher_deleted');    }

    /**
     * Gets all the GiftVoucherAvailable
     * @return array
     */
    public function getAll()
    {
        return $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findNotSuppressed()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function register(GiftVoucherAvailable $giftVoucherAvailable)
    {
        //Adjust slug in case of modified by user and not accepted signs
        $giftVoucherAvailable->setSlug($this->giftVoucherSlug->slugify($giftVoucherAvailable->getSlug()));

        //Persists data in DB
        $this->em->persist($giftVoucherAvailable);
        $this->em->flush();

        //Creates flash
        $this->giftVoucherTools->createFlash('voucher_created');
    }
}
