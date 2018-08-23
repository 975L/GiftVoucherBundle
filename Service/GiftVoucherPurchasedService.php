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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig_Environment;
use c975L\PaymentBundle\Entity\Payment;
use c975L\EmailBundle\Service\EmailServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherAvailable;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use c975L\GiftVoucherBundle\Service\Email\GiftVoucherEmailInterface;
use c975L\GiftVoucherBundle\Service\Pdf\GiftVoucherPdfInterface;
use c975L\GiftVoucherBundle\Service\Tools\GiftVoucherToolsInterface;

/**
 * Main services related to GiftVoucherPurchased
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class GiftVoucherPurchasedService implements GiftVoucherPurchasedServiceInterface
{
    /**
     * Stores EntityManager
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Stores GiftVoucherEmail
     * @var GiftVoucherEmailInterface
     */
    private $giftVoucherEmail;

    /**
     * Stores GiftVoucherTools
     * @var GiftVoucherToolsInterface
     */
    private $giftVoucherTools;

    /**
     * Stores current Request
     * @var RequestStack
     */
    private $request;

    /**
     * Stores templating
     * @var Twig_Environment
     */
    private $templating;

    public function __construct(
        EntityManagerInterface $em,
        GiftVoucherEmailInterface $giftVoucherEmail,
        GiftVoucherToolsInterface $giftVoucherTools,
        RequestStack $requestStack,
        Twig_Environment $templating
    )
    {
        $this->em = $em;
        $this->giftVoucherEmail = $giftVoucherEmail;
        $this->giftVoucherTools = $giftVoucherTools;
        $this->request = $requestStack->getCurrentRequest();
        $this->templating = $templating;
    }

    /**
     * {@inheritdoc}
     */
    public function create(GiftVoucherAvailable $giftVoucherAvailable)
    {
        $giftVoucherPurchased = new GiftVoucherPurchased();
        $validDate = new \DateTime();
        $giftVoucherPurchased
            ->setObject($giftVoucherAvailable->getObject())
            ->setDescription($giftVoucherAvailable->getDescription())
            ->setAmount($giftVoucherAvailable->getAmount())
            ->setCurrency($giftVoucherAvailable->getCurrency())
            ->setValid($validDate->add($giftVoucherAvailable->getValid()))
            ->setUserIp($this->request->getClientIp())
        ;

        return $giftVoucherPurchased;
    }

    /**
     * {@inheritdoc}
     */
    public function defineIdentifier()
    {
        $signsRemoved = array('o', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $signsReplacing = array('t', 'y', 'f', 'r', 'k', 'h', 'x', 'p', 'l', 'm', 'a');
        $identifier = strtoupper(substr(str_replace($signsRemoved, $signsReplacing, md5(time())), 12, 16));

        return $identifier;
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->em
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')
            ->findAll()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml(GiftVoucherPurchased $giftVoucherPurchased)
    {
        return $this->templating->render('@c975LGiftVoucher/pages/display.html.twig', array(
            'giftVoucher' => $giftVoucherPurchased,
            'display' => 'pdf',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifierFormatted(string $identifier)
    {
        return sprintf("%s-%s-%s", substr($identifier, 0, 4), substr($identifier, 4, 4), substr($identifier, 8, 4));
    }

    /**
     * {@inheritdoc}
     */
    public function register(GiftVoucherPurchased $giftVoucherPurchased, bool $test)
    {
        //Adds test data
        if (false === $test) {
            $giftVoucherPurchased->setObject('(TEST) ' . $giftVoucherPurchased->getObject());
        }

        //Persists data in DB
        $this->em->persist($giftVoucherPurchased);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function utilisation(GiftVoucherPurchased $giftVoucherPurchased, \DateTime $now)
    {
        $giftVoucherPurchased->setUsed($now);

        //Persists data in DB
        $this->em->persist($giftVoucherPurchased);
        $this->em->flush();

        //Creates flash
        $this->giftVoucherTools->createFlash('voucher_used');
    }

    /**
     * {@inheritdoc}
     */
    public function validate(Payment $payment)
    {
        $action = (array) json_decode($payment->getAction());

        if (array_key_exists('validateGiftVoucher', $action)) {
            $repository = $this->em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased');
            $giftVoucherPurchased = $repository->findOneById($action['validateGiftVoucher']);

            //Gets identifier
            $identifierExists = true;
            do {
                $identifier = $this->defineIdentifier();
                $identifierExists = $repository->findOneBy(array(
                    'identifier' => substr($identifier, 0, 12),
                    'secret' => substr($identifier, 12))
                );
            } while (null !== $identifierExists);

            //Updates giftVoucherPurchased
            $giftVoucherPurchased
                ->setPurchase(new \DateTime())
                ->setIdentifier(substr($identifier, 0, 12))
                ->setSecret(substr($identifier, 12))
                ->setOrderId($payment->getOrderId())
                ;
            $this->em->persist($giftVoucherPurchased);

            //Set payment as finished
            $payment->setFinished(true);
            $this->em->persist($payment);
            $this->em->flush();

            //Sends email
            $this->giftVoucherEmail->send($giftVoucherPurchased, $this->getHtml($giftVoucherPurchased), $this->getIdentifierFormatted($giftVoucherPurchased->getIdentifier()));

            //Creates flash
            $this->giftVoucherTools->createFlash('voucher_purchased');

            return $giftVoucherPurchased->getIdentifier() . $giftVoucherPurchased->getSecret();
        }

        return false;
    }
}
