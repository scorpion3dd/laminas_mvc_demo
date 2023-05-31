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

namespace User\Service\Factory;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\RbacManager;
use Laminas\Authentication\AuthenticationService;

/**
 * This is the factory class for RbacManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies)
 * @package User\Service\Factory
 */
class RbacManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return RbacManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RbacManager
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(AuthenticationService::class);
        $cache = $container->get('FilesystemCache');
        $assertionManagers = [];
        $config = $container->get('Config');
        if (isset($config['rbac_manager']['assertions'])) {
            foreach ($config['rbac_manager']['assertions'] as $serviceName) {
                $assertionManagers[$serviceName] = $container->get($serviceName);
            }
        }
        $logger = $container->get('LoggerGlobal');

        return new RbacManager($logger, $entityManager, $authService, $cache, $assertionManagers);
    }
}
