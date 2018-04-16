<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\Service;

use Cocur\Slugify\Slugify;

class GiftVoucherService
{
    //Defines the identifier of the Gift-Voucher, including the secret code
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

    //Slugify
    public function slugify($text)
    {
        $slugify = new Slugify();
        return $slugify->slugify($text);
    }
}