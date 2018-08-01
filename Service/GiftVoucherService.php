<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Cocur\Slugify\Slugify;
use c975L\Email\Service\EmailService;

class GiftVoucherService
{
    private $container;
    private $em;
    private $emailService;
    private $knpSnappyPdf;
    private $paymentService;
    private $request;
    private $router;
    private $templating;
    private $translator;

    public function __construct(
        \Symfony\Component\DependencyInjection\ContainerInterface $container,
        \Doctrine\ORM\EntityManagerInterface $em,
        \c975L\EmailBundle\Service\EmailService $emailService,
        \Knp\Snappy\Pdf $knpSnappyPdf,
        \c975L\PaymentBundle\Service\PaymentService $paymentService,
        \Symfony\Component\HttpFoundation\RequestStack $requestStack,
        \Symfony\Component\Routing\Generator\UrlGeneratorInterface $router,
        \Twig_Environment $templating,
        \Symfony\Component\Translation\TranslatorInterface $translator
        )
    {
        $this->container = $container;
        $this->em = $em;
        $this->emailService = $emailService;
        $this->knpSnappyPdf = $knpSnappyPdf;
        $this->paymentService = $paymentService;
        $this->request = $requestStack->getCurrentRequest();
        $this->router = $router;
        $this->templating = $templating;
        $this->translator = $translator;
    }

    //Defines the identifier of the GiftVoucher, including the secret code
    public function getIdentifier()
    {
        //Defines data, only letters except "o" to avoid confusion with 0
        $signsRemoved = array('o', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $signsReplacing = array('t', 'y', 'f', 'r', 'k', 'h', 'x', 'p', 'l', 'm', 'a');
        $identifier = strtoupper(substr(str_replace($signsRemoved, $signsReplacing, md5(time())), 12, 16));

        return $identifier;
    }

    //Formats the identifier to be displayed
    public function getIdentifierFormatted($identifier)
    {
        return sprintf("%s-%s-%s", substr($identifier, 0, 4), substr($identifier, 4, 4), substr($identifier, 8, 4));
    }

    //Creates HTML of purchased GiftVoucher
    public function getHtml($giftVoucher)
    {
        return $this->templating->render('@c975LGiftVoucher/pages/display.html.twig', array(
            'giftVoucher' => $giftVoucher,
            'display' => 'pdf',
        ));
    }

    //Creates PDF of purchased GiftVoucher
    public function getPdf($html, $identifier)
    {
        $filenameGiftVoucher = $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . '-' . $this->getIdentifierFormatted($identifier) . '.pdf';
        $giftVoucherPdf = $this->knpSnappyPdf->getOutputFromHtml($html);

        return array($giftVoucherPdf, $filenameGiftVoucher, 'application/pdf');
    }

    //Gets the Terms of sales PDF
    public function getTosPdf()
    {
        $tosPdfUrl = $this->getUrl($this->container->getParameter('c975_l_gift_voucher.tosPdf'));

        //Gets the content of TermsOfSales PDF
        if ($tosPdfUrl !== null) {
            $tosPdf = file_get_contents($tosPdfUrl);
            $filenameTos = $this->translator->trans('label.terms_of_sales_filename', array(), 'giftVoucher') . '.pdf';
            return array($tosPdf, $filenameTos, 'application/pdf');
        }

        return null;
    }

    //Gets the Terms of sales url
    public function getTosUrl()
    {
        return $this->getUrl($this->container->getParameter('c975_l_gift_voucher.tosUrl'));
    }

    //Defines the url
    public function getUrl($data)
    {
        //Calculates the url if a Route is provided
        if (false !== strpos($data, ',')) {
            $routeData = $this->getUrlFromRoute($data);
            $url = $this->router->generate($routeData['route'], $routeData['params'], UrlGeneratorInterface::ABSOLUTE_URL);
        //An url has been provided
        } elseif (false !== strpos($data, 'http')) {
            $url = $data;
        //Not valid data
        } else {
            $url = null;
        }

        return $url;
    }

    //Gets url from a Route
    public function getUrlFromRoute($route)
    {
        //Gets Route
        $routeValue = trim(substr($route, 0, strpos($route, ',')), "\'\"");

        //Gets parameters
        $params = trim(substr($route, strpos($route, '{')), "{}");
        $params = str_replace(array('"', "'"), '', $params);
        $params = explode(',', $params);

        //Caculates url
        $paramsArray = array();
        foreach($params as $value) {
            $parameter = explode(':', $value);
            $paramsArray[trim($parameter[0])] = trim($parameter[1]);
        }

        return array(
            'route' => $routeValue,
            'params' => $paramsArray
        );
    }

    //Defines payment for GiftVoucher purchased
    public function payment($giftVoucherPurchased, $userId)
    {
        $paymentData = array(
            'amount' => $giftVoucherPurchased->getAmount(),
            'currency' => $giftVoucherPurchased->getCurrency(),
            'action' => json_encode(array('validateGiftVoucher' => $giftVoucherPurchased->getId())),
            'description' => $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' - ' . $giftVoucherPurchased->getObject(),
            'userId' => $userId,
            'userIp' => $this->request->getClientIp(),
            'live' => $this->container->getParameter('c975_l_gift_voucher.live'),
            'returnRoute' => 'giftvoucher_payment_done',
            'vat' => $this->container->getParameter('c975_l_gift_voucher.vat'),
            );
        $this->paymentService->create($paymentData);
    }

    //Sends email for GiftVoucher purchased
    public function sendEmail($giftVoucher)
    {
        //Gets data for GiftVoucher
        $giftVoucherHtml = $this->getHtml($giftVoucher);
        $giftVoucherPdf = $this->getPdf($giftVoucherHtml, $giftVoucher->getIdentifier());

        //Gets the PDF of Terms of sales
        $tosPdf = $this->getTosPdf();

        //Sends email
        $emailData = array(
            'subject' => $this->translator->trans('label.gift_voucher', array(), 'giftVoucher') . ' "' . $giftVoucher->getObject() . '" (' . $this->getIdentifierFormatted($giftVoucher->getIdentifier()) . ')',
            'sentFrom' => $this->container->getParameter('c975_l_email.sentFrom'),
            'sentTo' => $giftVoucher->getSendToEmail(),
            'replyTo' => $this->container->getParameter('c975_l_email.sentFrom'),
            'body' => preg_replace('/<style(.*)<\/style>/s', '', $giftVoucherHtml),
            'attach' => array(
                $giftVoucherPdf,
                $tosPdf,
                ),
            'ip' => $this->request->getClientIp(),
            );
        $this->emailService->send($emailData, true);
    }

    //Slugify
    public function slugify($text)
    {
        $slugify = new Slugify();
        return $slugify->slugify($text);
    }

    //Validates the GiftVoucher after its payment
    public function validate($payment)
    {
        $action = (array) json_decode($payment->getAction());

        if (array_key_exists('validateGiftVoucher', $action)) {
            $repository = $this->em->getRepository('c975LGiftVoucherBundle:GiftVoucherPurchased');
            $giftVoucher = $repository->findOneById($action['validateGiftVoucher']);

            //Gets identifier
            $identifierExists = true;
            do {
                $identifier = $this->getIdentifier();
                $identifierExists = $repository->findOneBy(array(
                    'identifier' => substr($identifier, 0, 12),
                    'secret' => substr($identifier, 12))
                );
            } while ($identifierExists !== null);

            //Updates GiftVoucher
            $giftVoucher
                ->setPurchase(new \DateTime())
                ->setIdentifier(substr($identifier, 0, 12))
                ->setSecret(substr($identifier, 12))
                ->setorderId($payment->getOrderId())
                ;
            $this->em->persist($giftVoucher);

            //Set payment as finished
            $payment->setFinished(true);
            $this->em->persist($payment);
            $this->em->flush();

            //Sends email
            $this->sendEmail($giftVoucher);

            //Creates flash
            $flash = $this->translator->trans('text.voucher_purchased', array(), 'giftVoucher');
            $this->request->getSession()
                ->getFlashBag()
                ->add('success', $flash)
                ;

            return $giftVoucher->getIdentifier() . $giftVoucher->getSecret();
        }

        return false;
    }
}
