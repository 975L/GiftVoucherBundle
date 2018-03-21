<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class c975LGiftVoucherExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );
        $loader->load('services.yml');

        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration($configuration, $configs);

        $container->setParameter('c975_l_gift_voucher.live', $processedConfig['live']);
        $container->setParameter('c975_l_gift_voucher.roleNeeded', $processedConfig['roleNeeded']);
        $container->setParameter('c975_l_gift_voucher.defaultCurrency', strtoupper($processedConfig['defaultCurrency']));
        $container->setParameter('c975_l_gift_voucher.proposedCurrencies', $processedConfig['proposedCurrencies']);
        $container->setParameter('c975_l_gift_voucher.tosUrl', $processedConfig['tosUrl']);
        $container->setParameter('c975_l_gift_voucher.tosPdf', $processedConfig['tosPdf']);
    }
}