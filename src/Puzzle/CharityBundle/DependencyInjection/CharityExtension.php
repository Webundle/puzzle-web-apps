<?php

namespace Puzzle\CharityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CharityExtension extends Extension
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
        
        $container->setParameter('charity.title', $config['title']);
        $container->setParameter('charity.icon', $config['icon']);
        $container->setParameter('charity.description', $config['description']);
        $container->setParameter('charity.enable', $config['enable']);
        $container->setParameter('charity.dependencies', $config['dependencies']);
        $container->setParameter('charity.admin_menu', $config['admin_menu']);
    }
}
