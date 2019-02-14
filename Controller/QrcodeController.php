<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\GiftVoucherPurchasedServiceInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Response\QrCodeResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Qrcode Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class QrcodeController extends AbstractController
{
//DISPLAY

    /**
     * Builds and displays the Qrcode
     * @return ImagePng
     * @throws NotFoundHttpException
     *
     * @Route("/gift-voucher/qrcode/{identifier}",
     *    name="giftvoucher_qrcode",
     *    requirements={"identifier": "^([a-zA-Z]{16})$"},
     *    methods={"HEAD", "GET"})
     */
    public function qrcode(GiftVoucherPurchasedServiceInterface $giftVoucherPurchasedService, $identifier)
    {
        //Gets the GiftVoucher
        $giftVoucherPurchased = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')
            ->findOneBasedOnIdentifier($identifier);

        //Not existing GiftVoucher
        if (!$giftVoucherPurchased instanceof GiftVoucherPurchased) {
            throw $this->createNotFoundException();
        }

        //Returns QrCode
        $qrCode = new QrCode();
        $qrCode->setSize(150);
        $qrCode->setMargin(10);
        $qrCode->setValidateResult(true);
        $qrCode->setText($this->generateUrl('giftvoucher_purchased', array('identifier' => $giftVoucherPurchased->getIdentifier() . $giftVoucherPurchased->getSecret()), UrlGeneratorInterface::ABSOLUTE_URL));
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setLabel($giftVoucherPurchasedService->getIdentifierFormatted($giftVoucherPurchased->getIdentifier()));
        $qrCode->setLabelFontSize(11);
        $qrCode->setLabelAlignment('center');

        return new QrCodeResponse($qrCode);
    }
}
