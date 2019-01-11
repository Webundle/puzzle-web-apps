<?php

namespace Puzzle\LearningBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('learning');
		
//         $rootNode
// 	        ->children()
// 	           ->arrayNode('navigation')
// 	               ->children()
//     	               ->scalarNode('title')->defaultValue('Learning')->end()
//     	               ->scalarNode('icon')->defaultValue('mic')->end()
//     	               ->scalarNode('description')->defaultValue('Learning')->end()
//     	               ->booleanNode('enable')->defaultfalse()->end()
//     	               ->scalarNode('dependencies')->defaultValue('user,media')->end()
//     	               ->scalarNode('admin_menu')->defaultValue("LearningBundle::admin_menu.html.twig")->end()
// 	               ->end()
// 	           ->end()
// 	        ->end()
//         ;
        
        return $treeBuilder;
    }
}
