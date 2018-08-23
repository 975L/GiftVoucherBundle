<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Pdf;

use Knp\Snappy\Pdf;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use c975L\Email\Service\EmailService;
use c975L\GiftVoucherBundle\Service\Pdf\GiftVoucherPdfInterface;

/**
 * Services related to GiftVoucher Pdf
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherPdf implements GiftVoucherPdfInterface
{
    /**
     * Stores Container
     * @var ContainerInterface
     */
    private $container;

    /**
     * Stores knpSnappyPdf
     * @var Pdf
     */
    private $knpSnappyPdf;

    /**
     * Stores Translator
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        ContainerInterface $container,
        Pdf $knpSnappyPdf,
        TranslatorInterface $translator
    )
    {
        $this->container = $container;
        $this->knpSnappyPdf = $knpSnappyPdf;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getPdf(string $html, string $identifierFormatted)
    {
        $filenameGiftVoucher = $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . '-' . $identifierFormatted . '.pdf';
        $giftVoucherPdf = $this->knpSnappyPdf->getOutputFromHtml($html);

        return array($giftVoucherPdf, $filenameGiftVoucher, 'application/pdf');
    }

    /**
     * {@inheritdoc}
     */
    public function getTosPdf(string $tosPdfUrl)
    {
        if (null !== $tosPdfUrl) {
            $tosPdf = file_get_contents($tosPdfUrl);
            $filenameTos = $this->translator->trans('label.terms_of_sales_filename', array(), 'giftVoucher') . '.pdf';
            return array($tosPdf, $filenameTos, 'application/pdf');
        }

        return null;
    }
}
