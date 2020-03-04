<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Email;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\EmailBundle\Service\EmailServiceInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\ServicesBundle\Service\ServicePdfInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Services related to GiftVoucher Email
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherEmail implements GiftVoucherEmailInterface
{
    /**
     * Stores ConfigServiceInterface
     * @var ConfigServiceInterface
     */
    private $configService;

    /**
     * Stores EmailServiceInterface
     * @var EmailServiceInterface
     */
    private $emailService;

    /**
     * Stores ServicePdfInterface
     * @var ServicePdfInterface
     */
    private $servicePdf;

    /**
     * Stores TranslatorInterface
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ConfigServiceInterface $configService,
        EmailServiceInterface $emailService,
        ServicePdfInterface $servicePdf,
        TranslatorInterface $translator
    ) {
        $this->configService = $configService;
        $this->emailService = $emailService;
        $this->servicePdf = $servicePdf;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function send(GiftVoucherPurchased $giftVoucherPurchased, string $giftVoucherHtml, string $identifierFormatted)
    {
        //Gets Pdf for GiftVoucherPurchased
        $filenameGiftVoucher = $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . '-' . $identifierFormatted . '.pdf';
        $giftVoucherPdf = $this->servicePdf->html2Pdf($filenameGiftVoucher, $giftVoucherHtml);

        //Gets the PDF for Terms of sales
        $tosPdf = $this->servicePdf->getPdfFilePath('label.terms_of_sales_filename', $this->configService->getParameter('c975LGiftVoucher.tosPdf'));

        //Sends email
        $emailData = array(
            'subject' => $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' "' . $giftVoucherPurchased->getObject() . '" (' . $identifierFormatted . ')',
            'sentFrom' => $this->configService->getParameter('c975LEmail.sentFrom'),
            'sentTo' => $giftVoucherPurchased->getSendToEmail(),
            'replyTo' => $this->configService->getParameter('c975LEmail.sentFrom'),
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
