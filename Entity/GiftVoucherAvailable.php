<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Entity;

use DateInterval;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity GiftVoucherAvailable (linked to DB table `gift_voucher_available`)
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 *
 * @ORM\Table(name="gift_voucher_available")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="c975L\GiftVoucherBundle\Repository\GiftVoucherAvailableRepository")
 */
class GiftVoucherAvailable
{
    /**
     * GiftVoucherAvailable unique id
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * If the GiftVoucherAvailable is marked as suppressed
     * @var bool
     *
     * @ORM\Column(name="suppressed", type="boolean")
     */
    protected $suppressed;

    /**
     * Object for the GiftVoucherAvailable
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=128, nullable=true)
     */
    protected $object;

    /**
     * Slug for the GiftVoucherAvailable
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=128, nullable=true)
     */
    protected $slug;

    /**
     * Description for the GiftVoucherAvailable
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65000, nullable=true)
     */
    protected $description;

    /**
     * Interval for which the GiftVoucherAvailable will be valid
     * @var DateInterval
     *
     * @ORM\Column(name="valid", type="dateinterval", nullable=true)
     */
    protected $valid;

    /**
     * Amount in cents for the GiftVoucherAvailable
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    protected $amount;

    /**
     * Currency for the GiftVoucherAvailable
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=true)
     */
    protected $currency;

    /**
     * Get id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set suppressed
     * @param bool
     * @return GiftVoucherAvailable
     */
    public function setSuppressed(?bool $suppressed)
    {
        $this->suppressed = $suppressed;

        return $this;
    }

    /**
     * Get suppressed
     * @return bool
     */
    public function getSuppressed(): ?bool
    {
        return $this->suppressed;
    }

    /**
     * Set object
     * @param string
     * @return GiftVoucherAvailable
     */
    public function setObject(?string $object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     * @return string
     */
    public function getObject(): ?string
    {
        return $this->object;
    }

    /**
     * Set slug
     * @param string
     * @return GiftVoucherAvailable
     */
    public function setSlug(?string $slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     * @return string
     */
    public function getSlug():?string
    {
        return $this->slug;
    }

    /**
     * Set description
     * @param string
     * @return GiftVoucherAvailable
     */
    public function setDescription(?string $description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set valid
     * @param DateInterval
     * @return GiftVoucherAvailable
     */
    public function setValid(?DateInterval $valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     * @return DateInterval
     */
    public function getValid(): ?DateInterval
    {
        return $this->valid;
    }

    /**
     * Set amount
     * @param int
     * @return GiftVoucherAvailable
     */
    public function setAmount(?int $amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     * @return int
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * Set currency
     * @param string
     * @return GiftVoucherAvailable
     */
    public function setCurrency(?string $currency)
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    /**
     * Get currency
     * @return string
     */
    public function getCurrency(): ?string
    {
        return strtoupper($this->currency);
    }
}
