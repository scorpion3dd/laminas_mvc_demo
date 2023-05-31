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
use User\Controller\RoleController;
use User\Service\RoleManager;

/**
 * Class RoleControllerFactory
 * This is the factory for RoleController
 * @package User\Controller\Factory
 */
class RoleControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return RoleController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RoleController
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $roleManager = $container->get(RoleManager::class);
        $logger = $container->get('LoggerGlobal');

        return new RoleController($entityManager, $roleManager, $logger);
    }
}
