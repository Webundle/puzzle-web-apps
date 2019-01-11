<?php

namespace Puzzle\AdminBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AdminExtension extends Extension
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
        
        $container->setParameter('admin', $config);
        $container->setParameter('admin.navigation', $config['navigation']);
        $container->setParameter('admin.website', $config['website']);
        
        $container->setParameter('admin.time_format', $config['time_format']);
        $container->setParameter('admin.date_format', $config['date_format']);
        $container->setParameter('admin.email', $config['website']['email']);
        
        $container->setParameter('admin.modules_available', $config['modules_available']);
    }
}
