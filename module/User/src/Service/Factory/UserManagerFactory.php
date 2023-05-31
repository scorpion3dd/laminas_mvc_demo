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
use User\Service\UserManager;
use User\Service\RoleManager;
use User\Service\PermissionManager;

/**
 * This is the factory class for UserManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies)
 * @package User\Service\Factory
 */
class UserManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return UserManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UserManager
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $roleManager = $container->get(RoleManager::class);
        $permissionManager = $container->get(PermissionManager::class);
        $viewRenderer = $container->get('ViewRenderer');
        $config = $container->get('Config');
        $logger = $container->get('LoggerGlobal');

        return new UserManager($logger, $entityManager, $roleManager, $permissionManager, $viewRenderer, $config);
    }
}
