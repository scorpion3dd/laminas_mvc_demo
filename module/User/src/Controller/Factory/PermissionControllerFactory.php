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

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Controller\PermissionController;
use User\Service\PermissionManager;

/**
 * Class PermissionControllerFactory
 * This is the factory for PermissionController
 * @package User\Controller\Factory
 */
class PermissionControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return PermissionController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): PermissionController
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $permissionManager = $container->get(PermissionManager::class);
        $logger = $container->get('LoggerGlobal');

        return new PermissionController($entityManager, $permissionManager, $logger);
    }
}
