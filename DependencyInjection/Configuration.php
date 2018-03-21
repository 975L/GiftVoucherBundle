<?php
/*
 * (c) 2018: 975l <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\GiftVoucherBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('c975_l_gift_voucher');

        $rootNode
            ->children()
                ->scalarNode('live')
                    ->defaultFalse()
                ->end()
                ->scalarNode('roleNeeded')
                    ->defaultValue('ROLE_ADMIN')
                ->end()
                ->scalarNode('defaultCurrency')
                    ->defaultValue('EUR')
                ->end()
                ->arrayNode('proposedCurrencies')
                    ->prototype('scalar')->end()
                    ->defaultValue(array())
                ->end()
                ->floatNode('vat')
                    ->defaultNull()
                ->end()
                ->scalarNode('tosUrl')
                ->end()
                ->scalarNode('tosPdf')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
