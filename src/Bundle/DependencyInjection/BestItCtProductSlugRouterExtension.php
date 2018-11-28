<?php

namespace BestIt\CtProductSlugRouterBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Loads the config for the bundle.
 * @author lange <lange@bestit-online.de>
 * @package BestIt\CtProductSlugRouterBundle\DependencyInjection
 */
class BestItCtProductSlugRouterExtension extends Extension
{
    /**
     * Loads the bundle config.
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $container->setParameter('best_it.ct_product_slug_router.controller', $config['controller']);
        $container->setParameter('best_it.ct_product_slug_router.priority', $config['priority']);
        $container->setParameter('best_it.ct_product_slug_router.route', $config['route']);

        if (@$config['repository']) {
            $container->setAlias('best_it.ct_product_slug_router.product_repository', $config['repository']);
        }
    }
}
