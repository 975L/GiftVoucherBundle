<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="gift_voucher_ordered")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="c975L\GiftVoucherBundle\Repository\GiftVoucherOrderedRepository")
 */
class GiftVoucherOrdered
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
     * @var string
     *
     * @ORM\Column(name="number", type="string")
     */
    protected $number;

    /**
     * @var string
     *
     * @ORM\Column(name="secret", type="string")
     */
    protected $secret;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", nullable=true)
     */
    protected $object;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="offered_by", type="string", nullable=true)
     */
    protected $offeredBy;

    /**
     * @var string
     *
     * @ORM\Column(name="offered_to", type="string", nullable=true)
     */
    protected $offeredTo;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", nullable=true)
     */
    protected $message;

    /**
     * @var string
     *
     * @ORM\Column(name="send_to_email", type="string", nullable=true)
     */
    protected $sendToEmail;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="purchase", type="datetime", nullable=true)
     */
    protected $purchase;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid", type="datetime", nullable=true)
     */
    protected $valid;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    protected $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", nullable=true)
     */
    protected $currency;

    /**
     * @ORM\Column(name="used", type="datetime")
     */
    protected $used;

    /**
     * @var string
     *
     * @ORM\Column(name="user_ip", type="string", nullable=true)
     */
    protected $userIp;


    /**
     * Set id
     *
     * @param integer $id
     */
    public function setId()
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set number
     *
     * @param string $number
     *
     * @return GiftVoucherOrdered
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set secret
     *
     * @param string $secret
     *
     * @return GiftVoucherOrdered
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set object
     *
     * @param string $object
     *
     * @return GiftVoucherOrdered
     */
    public function setObject($object)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return string
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return GiftVoucherOrdered
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set offeredBy
     *
     * @param string $offeredBy
     *
     * @return GiftVoucherOrdered
     */
    public function setOfferedBy($offeredBy)
    {
        $this->offeredBy = $offeredBy;

        return $this;
    }

    /**
     * Get offeredBy
     *
     * @return string
     */
    public function getOfferedBy()
    {
        return $this->offeredBy;
    }

    /**
     * Set offeredTo
     *
     * @param string $offeredTo
     *
     * @return GiftVoucherOrdered
     */
    public function setOfferedTo($offeredTo)
    {
        $this->offeredTo = $offeredTo;

        return $this;
    }

    /**
     * Get offeredTo
     *
     * @return string
     */
    public function getOfferedTo()
    {
        return $this->offeredTo;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return GiftVoucherOrdered
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sendToEmail
     *
     * @param string $sendToEmail
     *
     * @return GiftVoucherOrdered
     */
    public function setSendToEmail($sendToEmail)
    {
        $this->sendToEmail = $sendToEmail;

        return $this;
    }

    /**
     * Get sendToEmail
     *
     * @return string
     */
    public function getSendToEmail()
    {
        return $this->sendToEmail;
    }

    /**
     * Set purchase
     *
     * @param datetime $purchase
     *
     * @return GiftVoucherOrdered
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;

        return $this;
    }

    /**
     * Get purchase
     *
     * @return datetime
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * Set valid
     *
     * @param datetime $valid
     *
     * @return GiftVoucherOrdered
     */
    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    /**
     * Get valid
     *
     * @return datetime
     */
    public function getValid()
    {
        return $this->valid;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return GiftVoucherOrdered
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return GiftVoucherOrdered
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set used
     *
     * @param datetime $used
     *
     * @return GiftVoucherOrdered
     */
    public function setUsed($used)
    {
        $this->used = $used;

        return $this;
    }

    /**
     * Get used
     *
     * @return datetime
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * Set userIp
     *
     * @param string $userIp
     *
     * @return GiftVoucherOrdered
     */
    public function setUserIp($userIp)
    {
        $this->userIp = $userIp;

        return $this;
    }

    /**
     * Get userIp
     *
     * @return string
     */
    public function getUserIp()
    {
        return $this->userIp;
    }
}