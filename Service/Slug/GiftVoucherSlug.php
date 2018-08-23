<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Slug;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Cocur\Slugify\Slugify;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Service\Slug\GiftVoucherSlugInterface;

/**
 * Services related to GiftVoucher Slug
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class GiftVoucherSlug implements GiftVoucherSlugInterface
{
    /**
     * Stores EntityManager
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Stores Router
     * @var RouterInterface
     */
    private $router;

    public function __construct(EntityManagerInterface $em, RouterInterface $router)
    {
        $this->em = $em;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function exists(string $slug)
    {
        //Gets the events
        $giftVouchers = $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherAvailable')
            ->findAll()
        ;

        foreach ($giftVouchers as $giftVoucher) {
            if ($giftVoucher->getSlug() == $slug) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function match(string $route, GiftVoucherAvailable $giftVoucherAvailable, string $slug)
    {
        if ($slug !== $giftVoucherAvailable->getSlug()) {
            return
                $this->router->generate($route, array(
                    'slug' => $giftVoucherAvailable->getSlug(),
                    'id' => $giftVoucherAvailable->getId(),
            ));
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function slugify(string $text)
    {
        $slugify = new Slugify();
        $slug = $slugify->slugify($text);

        //Checks unicity of slug
        $finalSlug = $slug;
        $slugExists = true;
        $i = 1;
        do {
            $slugExists = $this->exists($finalSlug);
            if ($slugExists) {
                $finalSlug = $slug . '-' . $i++;
            }
        } while (false !== $slugExists);

        return $finalSlug;
    }
}
