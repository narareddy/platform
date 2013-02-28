<?php

namespace Oro\Bundle\SearchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OroSearchExtension extends Extension
{
    /**
     * Load configuration
     *
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('oro_search.log_queries', $config['log_queries']);

        $entitiesConfig = $config['entities_config'];
        if (!count($entitiesConfig)) {
            if (count($config['config_paths'])) {
                foreach ($config['config_paths'] as $configPath) {
                    $entitiesConfig += Yaml::parse($configPath);
                }
            } else {
                foreach ($container->getParameter('kernel.bundles') as $bundle) {
                    $reflection = new \ReflectionClass($bundle);
                    if (is_file($file = dirname($reflection->getFilename()).'/Resources/config/search.yml')) {
                        $entitiesConfig += Yaml::parse(realpath($file));
                    }
                }
            }
        }
        $container->setParameter('oro_search.entities_config', $entitiesConfig);

        $loader->load('engine/' . $config['engine'] . '.yml');

        if ($config['engine'] == 'orm' && count($config['engine_orm'])) {
            $this->remapParameters(
                $config,
                $container,
                array(
                     'engine_orm' => 'oro_search.engine_orm'
                )
            );
        }

        $container->setParameter('oro_search.realtime_update', $config['realtime_update']);

        $loader->load('services.yml');
    }

    /**
     * Remap parameters form to container params
     *
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array                                                   $map
     */
    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (array_key_exists($name, $config)) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias()
    {
        return 'oro_search';
    }
}
