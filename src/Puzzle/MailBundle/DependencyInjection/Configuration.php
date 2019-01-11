<?php

namespace Puzzle\MailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mail');
		
//         $rootNode
// 	        ->children()
// 		        ->scalarNode('title')->defaultValue('Mail')->end()
// 		        ->scalarNode('icon')->defaultValue('email')->end()
// 		        ->scalarNode('description')->defaultValue('Mail')->end()
// 		        ->booleanNode('enable')->defaultfalse()->end()
// 		        ->scalarNode('dependencies')->defaultValue('user,media')->end()
// 		        ->scalarNode('admin_menu')->defaultValue("MailBundle::admin_menu.html.twig")->end()
// 	        ->end()
//         ;
        
        return $treeBuilder;
    }
}
