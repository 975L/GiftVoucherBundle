<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Email;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use c975L\EmailBundle\Service\EmailServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\Email\GiftVoucherEmailInterface;
use c975L\GiftVoucherBundle\Service\Pdf\GiftVoucherPdfInterface;
use c975L\GiftVoucherBundle\Service\Tools\GiftVoucherToolsInterface;

/**
 * Services related to GiftVoucher Email
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherEmail implements GiftVoucherEmailInterface
{
    /**
     * Stores container
     * @var ContainerInterface
     */
    private $container;

    /**
     * Stores EmailService
     * @var EmailServiceInterface
     */
    private $emailService;

    /**
     * Stores GiftVoucherPdf
     * @var GiftVoucherPdfInterface
     */
    private $giftVoucherPdf;

    /**
     * Stores GiftVoucherTools
     * @var GiftVoucherToolsInterface
     */
    private $giftVoucherTools;

    /**
     * Stores Translator
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ContainerInterface $container,
        EmailServiceInterface $emailService,
        GiftVoucherPdfInterface $giftVoucherPdf,
        GiftVoucherToolsInterface $giftVoucherTools,
        TranslatorInterface $translator
    )
    {
        $this->container = $container;
        $this->emailService = $emailService;
        $this->giftVoucherPdf = $giftVoucherPdf;
        $this->giftVoucherTools = $giftVoucherTools;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function send(GiftVoucherPurchased $giftVoucherPurchased, string $giftVoucherHtml, string $identifierFormatted)
    {
        //Gets Pdf for GiftVoucherPurchased
        $giftVoucherPdf = $this->giftVoucherPdf->getPdf($giftVoucherHtml, $identifierFormatted);

        //Gets the PDF for Terms of sales
        $tosPdf = $this->giftVoucherPdf->getTosPdf($this->giftVoucherTools->getUrl($this->container->getParameter('c975_l_gift_voucher.tosPdf')));

        //Sends email
        $emailData = array(
            'subject' => $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' "' . $giftVoucherPurchased->getObject() . '" (' . $identifierFormatted . ')',
            'sentFrom' => $this->container->getParameter('c975_l_email.sentFrom'),
            'sentTo' => $giftVoucherPurchased->getSendToEmail(),
            'replyTo' => $this->container->getParameter('c975_l_email.sentFrom'),
            'body' => preg_replace('/<style(.*)<\/style>/s', '', $giftVoucherHtml),
            'attach' => array(
                $giftVoucherPdf,
                $tosPdf,
                ),
            'ip' => $giftVoucherPurchased->getUserIp(),
            );
        $this->emailService->send($emailData, true);
    }
}
