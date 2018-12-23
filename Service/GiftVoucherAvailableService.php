<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Form\GiftVoucherFormFactoryInterface;
use c975L\ServicesBundle\Service\ServiceSlugInterface;
use c975L\ServicesBundle\Service\ServiceToolsInterface;
use Doctrine\ORM\EntityManagerInterface;

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
     * Stores GiftVoucherFormFactoryInterface
     * @var GiftVoucherFormFactoryInterface
     */
    private $giftVoucherFormFactory;

    /**
     * Stores ServiceSlugInterface
     * @var ServiceSlugInterface
     */
    private $serviceSlug;

    /**
     * Stores ServiceToolsInterface
     * @var ServiceToolsInterface
     */
    private $serviceTools;

    public function __construct(
        EntityManagerInterface $em,
        GiftVoucherFormFactoryInterface $giftVoucherFormFactory,
        ServiceSlugInterface $serviceSlug,
        ServiceToolsInterface $serviceTools
    )
    {
        $this->em = $em;
        $this->giftVoucherFormFactory = $giftVoucherFormFactory;
        $this->serviceSlug = $serviceSlug;
        $this->serviceTools = $serviceTools;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(string $name, GiftVoucherAvailable $giftVoucherAvailable)
    {
        return $this->giftVoucherFormFactory->create($name, $giftVoucherAvailable);
    }

    /**
     * {@inheritdoc}
     */
    public function cloneObject(GiftVoucherAvailable $giftVoucherAvailable)
    {
        $giftVoucherAvailableClone = clone $giftVoucherAvailable;
        $giftVoucherAvailableClone
            ->setObject(null)
            ->setSlug(null)
        ;

        return $giftVoucherAvailableClone;
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
        $this->serviceTools->createFlash('giftVoucher', 'text.voucher_deleted');
    }

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
        //Adjust slug in case of not accepted signs that has been added by user
        $uow = $this->em->getUnitOfWork();
        $uow->computeChangeSets();
        if (isset($uow->getEntityChangeSet($giftVoucherAvailable)['slug'])) {
            $giftVoucherAvailable->setSlug($this->serviceSlug->slugify('c975LGiftVoucherBundle:GiftVoucherAvailable', $giftVoucherAvailable->getSlug()));
        }

        //Persists data in DB
        $this->em->persist($giftVoucherAvailable);
        $this->em->flush();

        //Creates flash
        $this->serviceTools->createFlash('giftVoucher', 'text.voucher_created');
    }
}
