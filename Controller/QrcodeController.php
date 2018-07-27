<?php
/*
 * (c) 2017: 975L <contact@975l.com>
 * (c) 2017: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Controller;

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use Endroid\QrCode\QrCode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use c975L\GiftVoucherBundle\Entity\GiftVoucherPurchased;
use c975L\GiftVoucherBundle\Service\GiftVoucherService;

class QrcodeController extends Controller
{
//QRCODE
    /**
     * @Route("/gift-voucher/qrcode/{identifier}",
     *      name="giftvoucher_qrcode",
     *      requirements={
     *          "identifier": "^([a-zA-Z]{16})$"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function qrcode(GiftVoucherService $giftVoucherService,$identifier)
    {
        //Gets the GiftVoucher
        $giftVoucher = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased')
            ->findOneBasedOnIdentifier($identifier);

        //Not existing GiftVoucher
        if (!$giftVoucher instanceof GiftVoucherPurchased) {
            throw $this->createNotFoundException();
        }

        //Gets the formatted identifier
        $identifierFormatted = $giftVoucherService->getIdentifierFormatted($giftVoucher->getIdentifier());

        //Returns QrCode
        $qrCode = new QrCode();
        $qrCode->setSize(150);
        $qrCode->setMargin(10);
        $qrCode->setValidateResult(true);
        $qrCode->setText($this->generateUrl('giftvoucher_display', array('identifier' => $identifier), UrlGeneratorInterface::ABSOLUTE_URL));
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH);
        $qrCode->setLabel($identifierFormatted);
        $qrCode->setLabelFontSize(11);
        $qrCode->setLabelAlignment('center');

        return new QrCodeResponse($qrCode);
    }
}