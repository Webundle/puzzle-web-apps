<?php

namespace Puzzle\LearningBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LearningExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
       
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('parameters.yml');
        
//         $container->setParameter('learning.navigation.title', $config['navigation']['title']);
//         $container->setParameter('learning.navigation.icon', $config['navigation']['icon']);
//         $container->setParameter('learning.navigation.description', $config['navigation']['description']);
//         $container->setParameter('learning.navigation.enable', $config['navigation']['enable']);
//         $container->setParameter('learning.navigation.dependencies', $config['navigation']['dependencies']);
//         $container->setParameter('learning.navigation.admin_menu', $config['navigation']['admin_menu']);
    }
}
