<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Entity;

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
     * @ORM\Column(name="identifier", type="string")
     */
    protected $identifier;

    /**
     * Secret code for GiftVoucherPurchased
     * @var string
     *
     * @ORM\Column(name="secret", type="string")
     */
    protected $secret;

    /**
     * Object for the GiftVoucherPurchased (copy of the object of GiftVoucherAvailable in cas it changes between purchase and use)
     * @var string
     *
     * @ORM\Column(name="object", type="string", nullable=true)
     */
    protected $object;

    /**
     * Description for the GiftVoucherPurchased (copy of the description of GiftVoucherAvailable in cas it changes between purchase and use)
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * Name of the offering person
     * @var string
     *
     * @ORM\Column(name="offered_by", type="string", nullable=true)
     */
    protected $offeredBy;

    /**
     * Name of the receiving offer person
     * @var string
     *
     * @ORM\Column(name="offered_to", type="string", nullable=true)
     */
    protected $offeredTo;

    /**
     * Message left by offering person to receiving one
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    protected $message;

    /**
     * Email address the GiftVoucherPurchased will be sent to
     * @var string
     *
     * @ORM\Column(name="send_to_email", type="string", nullable=true)
     */
    protected $sendToEmail;

    /**
     * DateTime of purchase
     * @var \DateTime
     *
     * @ORM\Column(name="purchase", type="datetime", nullable=true)
     */
    protected $purchase;

    /**
     * Final DateTime for validity
     * @var \DateTime
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
     * @ORM\Column(name="currency", type="string", nullable=true)
     */
    protected $currency;

    /**
     * Payment order id
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", nullable=true)
     */
    protected $orderId;

    /**
     * DateTime the GiftVoucherPurchased has been used
     * @var \DateTime
     *
     * @ORM\Column(name="used", type="datetime")
     */
    protected $used;

    /**
     * Ip address of the user
     * @var string
     *
     * @ORM\Column(name="user_ip", type="string", nullable=true)
     */
    protected $userIp;

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set identifier
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = strtoupper($identifier);

        return $this;
    }

    /**
     * Get identifier
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set secret
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setSecret($secret)
    {
        $this->secret = strtoupper($secret);

        return $this;
    }

    /**
     * Get secret
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set object
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set description
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set offeredBy
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setOfferedBy($offeredBy)
    {
        $this->offeredBy = $offeredBy;

        return $this;
    }

    /**
     * Get offeredBy
     * @return string
     */
    public function getOfferedBy()
    {
        return $this->offeredBy;
    }

    /**
     * Set offeredTo
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setOfferedTo($offeredTo)
    {
        $this->offeredTo = $offeredTo;

        return $this;
    }

    /**
     * Get offeredTo
     * @return string
     */
    public function getOfferedTo()
    {
        return $this->offeredTo;
    }

    /**
     * Set message
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sendToEmail
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setSendToEmail($sendToEmail)
    {
        $this->sendToEmail = $sendToEmail;

        return $this;
    }

    /**
     * Get sendToEmail
     * @return string
     */
    public function getSendToEmail()
    {
        return $this->sendToEmail;
    }

    /**
     * Set purchase
     * @param \DateTime
     * @return GiftVoucherPurchased
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * Get purchase
     * @return \DateTime
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * Set valid
     * @param \DateTime
     * @return GiftVoucherPurchased
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     * @return \DateTime
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * Set amount
     * @param int
     * @return GiftVoucherPurchased
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setCurrency($currency)
    {
        $this->currency = strtoupper($currency);

        return $this;
    }

    /**
     * Get currency
     * @return string
     */
    public function getCurrency()
    {
        return strtoupper($this->currency);
    }

    /**
     * Set orderId
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set used
     * @param \DateTime
     * @return GiftVoucherPurchased
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     * @return \DateTime
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Set userIp
     * @param string
     * @return GiftVoucherPurchased
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * Get userIp
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }
}