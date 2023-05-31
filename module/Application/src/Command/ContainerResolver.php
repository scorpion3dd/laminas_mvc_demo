<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Laminas Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2021-2022 scorpion3dd
 */

declare(strict_types=1);

namespace Application\Command;

use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

use function class_exists;
use function file_exists;

// @codeCoverageIgnoreStart
/**
 * Class ContainerResolver
 * @package Application\Command
 */
final class ContainerResolver
{
    /**
     * Try to find container in Laminas application.
     * Supports out of the box Laminas MVC and Mezzio applications.
     *
     * @param string $applicationConfig
     *
     * @return ContainerInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function resolve(string $applicationConfig): ContainerInterface
    {
        if (file_exists('config/container.php')) {
            return self::resolveDefaultContainer();
        }
        if (file_exists($applicationConfig) && class_exists(ServiceManager::class)) {
            return self::resolveMvcContainer($applicationConfig);
        }
        throw new RuntimeException('Cannot detect PSR-11 container');
    }

    /**
     * @throws RuntimeException When file contains not a valid PSR-11 container.
     */
    private static function resolveDefaultContainer(): ContainerInterface
    {
        $container = include 'config/container.php';
        if (! $container instanceof ContainerInterface) {
            throw new RuntimeException('Failed to load PSR-11 container');
        }

        return $container;
    }

    /**
     * @param string $applicationConfig
     * @return ContainerInterface
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private static function resolveMvcContainer(string $applicationConfig): ContainerInterface
    {
        $appConfig = include $applicationConfig;
        if (file_exists('config/development.config.php')) {
            $appConfig = ArrayUtils::merge(
                $appConfig,
                include 'config/development.config.php'
            );
        }
        $smConfig = new ServiceManagerConfig($appConfig['service_manager'] ?? []);
        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $appConfig);
        $serviceManager->get('ModuleManager')->loadModules();

        return $serviceManager;
    }
}
// @codeCoverageIgnoreEnd
