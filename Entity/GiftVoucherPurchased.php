<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entity GiftVoucherPurchased (linked to DB table `gift_voucher_purchased`)
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 *
 * @ORM\Table(name="gift_voucher_purchased")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="c975L\GiftVoucherBundle\Repository\GiftVoucherPurchasedRepository")
 */
class GiftVoucherPurchased
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Unique identifier for GiftVoucherPurchased
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=12)
     */
    protected $identifier;

    /**
     * Secret code for GiftVoucherPurchased
     * @var string
     *
     * @ORM\Column(name="secret", type="string", length=4)
     */
    protected $secret;

    /**
     * Object for the GiftVoucherPurchased (copy of the object of GiftVoucherAvailable in cas it changes between purchase and use)
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=128, nullable=true)
     */
    protected $object;

    /**
     * Description for the GiftVoucherPurchased (copy of the description of GiftVoucherAvailable in cas it changes between purchase and use)
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65000, nullable=true)
     */
    protected $description;

    /**
     * Name of the offering person
     * @var string
     *
     * @ORM\Column(name="offered_by", type="string", length=128, nullable=true)
     */
    protected $offeredBy;

    /**
     * Name of the receiving offer person
     * @var string
     *
     * @ORM\Column(name="offered_to", type="string", length=128, nullable=true)
     */
    protected $offeredTo;

    /**
     * Message left by offering person to receiving one
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=65000, nullable=true)
     */
    protected $message;

    /**
     * Email address the GiftVoucherPurchased will be sent to
     * @var string
     *
     * @ORM\Column(name="send_to_email", type="string", length=128, nullable=true)
     */
    protected $sendToEmail;

    /**
     * DateTime of purchase
     * @var DateTime
     *
     * @ORM\Column(name="purchase", type="datetime", nullable=true)
     */
    protected $purchase;

    /**
     * Final DateTime for validity
     * @var DateTime
     *
     * @ORM\Column(name="valid", type="datetime", nullable=true)
     */
    protected $valid;

    /**
     * Amount in cents of the the GiftVoucherPurchased
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    protected $amount;

    /**
     * Currency of the GiftVoucherPurchased
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=3, nullable=true)
     */
    protected $currency;

    /**
     * Payment order id
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=48, nullable=true)
     */
    protected $orderId;

    /**
     * DateTime the GiftVoucherPurchased has been used
     * @var DateTime
     *
     * @ORM\Column(name="used", type="datetime")
     */
    protected $used;

    /**
     * User IP address
     * @var string
     *
     * @ORM\Column(name="user_ip", type="string", length=48, nullable=true)
     */
    protected $userIp;

    /**
     * Get id
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set identifier
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setIdentifier(?string $identifier)
    {
        $this->identifier = strtoupper($identifier);

        return $this;
    }

    /**
     * Get identifier
     * @return string
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * Set secret
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setSecret(?string $secret)
    {
        $this->secret = strtoupper($secret);

        return $this;
    }

    /**
     * Get secret
     * @return string
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * Set object
     * @param string
     * @return GiftVoucherPurchased
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
    public function getObject():?string
    {
        return $this->object;
    }

    /**
     * Set description
     * @param string
     * @return GiftVoucherPurchased
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
     * Set offeredBy
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setOfferedBy(?string $offeredBy)
    {
        $this->offeredBy = $offeredBy;

        return $this;
    }

    /**
     * Get offeredBy
     * @return string
     */
    public function getOfferedBy(): ?string
    {
        return $this->offeredBy;
    }

    /**
     * Set offeredTo
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setOfferedTo(?string $offeredTo)
    {
        $this->offeredTo = $offeredTo;

        return $this;
    }

    /**
     * Get offeredTo
     * @return string
     */
    public function getOfferedTo(): ?string
    {
        return $this->offeredTo;
    }

    /**
     * Set message
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setMessage(?string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set sendToEmail
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setSendToEmail(?string $sendToEmail)
    {
        $this->sendToEmail = $sendToEmail;

        return $this;
    }

    /**
     * Get sendToEmail
     * @return string
     */
    public function getSendToEmail(): ?string
    {
        return $this->sendToEmail;
    }

    /**
     * Set purchase
     * @param DateTime
     * @return GiftVoucherPurchased
     */
    public function setPurchase(?DateTime $purchase)
    {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * Get purchase
     * @return DateTime
     */
    public function getPurchase(): ?DateTime
    {
        return $this->purchase;
    }

    /**
     * Set valid
     * @param DateTime
     * @return GiftVoucherPurchased
     */
    public function setValid(?DateTime $valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     * @return DateTime
     */
    public function getValid(): ?DateTime
    {
        return $this->valid;
    }

    /**
     * Set amount
     * @param int
     * @return GiftVoucherPurchased
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
     * @return GiftVoucherPurchased
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

    /**
     * Set orderId
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setOrderId(?string $orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     * @return string
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * Set used
     * @param DateTime
     * @return GiftVoucherPurchased
     */
    public function setUsed(?DateTime $used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     * @return DateTime
     */
    public function getUsed(): ?DateTime
    {
        return $this->used;
    }

    /**
     * Set userIp
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setUserIp(?string $userIp)
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * Get userIp
     * @return string
     */
    public function getUserIp(): ?string
    {
        return $this->userIp;
    }
}
