<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service\Tools;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use c975L\GiftVoucherBundle\Service\Tools\GiftVoucherToolsInterface;

/**
 * Services related to GiftVoucher Tools
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class GiftVoucherTools implements GiftVoucherToolsInterface
{
    /**
     * Stores current Request
     * @var RequestStack
     */
    private $request;

    /**
     * Stores Router
     * @var RouterInterface
     */
    private $router;

    /**
     * Stores Translator
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        RouterInterface $router
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function createFlash(string $object, $url = null)
    {
        $style = 'success';
        $options = array();

        switch ($object) {
            //GiftVoucherAvailable created
            case 'voucher_created':
                $flash = 'text.voucher_created';
                break;
            //GiftVoucherAvailable deleted
            case 'voucher_deleted':
                $flash = 'text.voucher_deleted';
                break;
            //GiftVoucherPurchased purchased
            case 'voucher_purchased':
                $flash = 'text.voucher_purchased';
                break;
            //GiftVoucherPurchased marked as used
            case 'voucher_used':
                $flash = 'text.voucher_used';
                break;
            default:
                break;
        }

        if(isset($flash)) {
            $this->request->getSession()
                ->getFlashBag()
                ->add($style, $this->translator->trans($flash, $options, 'giftVoucher'))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl(string $data)
    {
        $url = null;

        //Calculates the url if a Route is provided
        if (false !== strpos($data, ',')) {
            $routeData = $this->getUrlFromRoute($data);
            $url = $this->router->generate($routeData['route'], $routeData['params'], UrlGeneratorInterface::ABSOLUTE_URL);
        //An url has been provided
        } elseif (false !== strpos($data, 'http')) {
            $url = $data;
        }

        return $url;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlFromRoute(string $route)
    {
        //Gets Route
        $routeValue = trim(substr($route, 0, strpos($route, ',')), "\'\"");

        //Gets parameters
        $params = trim(substr($route, strpos($route, '{')), "{}");
        $params = str_replace(array('"', "'"), '', $params);
        $params = explode(',', $params);
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
}
