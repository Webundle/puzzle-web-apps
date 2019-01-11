<?php

namespace Puzzle\AdminBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('admin');
		
        $rootNode
	        ->children()
    	        ->arrayNode('website')
        	        ->addDefaultsIfNotSet()
        	        ->children()
            	        ->scalarNode('title')->defaultNull()->end()
            	        ->scalarNode('description')->defaultNull()->end()
            	        ->scalarNode('email')->defaultNull()->end()
        	        ->end()
    	        ->end()
	        	->scalarNode('time_format')->end()
	        	->scalarNode('date_format')->end()
		        ->scalarNode('modules_available')->end()
		        ->arrayNode('navigation')
		          ->addDefaultsIfNotSet()
    		        ->children()
        		        ->arrayNode('nodes')
            		        ->useAttributeAsKey('name')
            		        ->prototype('array')
                		        ->children()
                		            ->scalarNode('label')->isRequired()->end()
                		            ->scalarNode('description')->defaultNull()->end()
                    		        ->scalarNode('translation_domain')->defaultNull()->end()
                    		        ->scalarNode('translation_locale')->defaultNull()->end()
                    		        ->arrayNode('translation_parameters')
                        		        ->treatNullLike([])
                        		        ->prototype('scalar')->end()
                    		        ->end()
                    		        ->scalarNode('path')->defaultNull()->end()
                    		        ->arrayNode('attr')
                    		          ->prototype('scalar')->end()
                    		        ->end()
                    		        ->scalarNode('parent')->defaultNull()->end()
                    		        ->arrayNode('sub_paths')
                    		          ->prototype('scalar')->end()
                    		        ->end()
                    		        ->arrayNode('user_roles')
                    		          ->prototype('scalar')->end()
                    		        ->end()
                		        ->end()
            		        ->end()
        		        ->end()
    		        ->end()
		        ->end()
	        ->end()
        ;
        
        return $treeBuilder;
    }
}
