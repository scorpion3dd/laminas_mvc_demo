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
use User\Controller\AuthController;
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Service\AuthManager;
use User\Service\UserManager;

/**
 * Class AuthControllerFactory
 * This is the factory for AuthController
 * @package User\Controller\Factory
 */
class AuthControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return AuthController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthController
    {
        $authManager = $container->get(AuthManager::class);
        $userManager = $container->get(UserManager::class);
        $logger = $container->get('LoggerGlobal');

        return new AuthController($authManager, $userManager, $logger);
    }
}
