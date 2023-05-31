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
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\Session\SessionManager;
use User\Service\AuthManager;
use User\Service\RbacManager;

/**
 * This is the factory class for AuthManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies)
 * @package User\Service\Factory
 */
class AuthManagerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return AuthManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthManager
    {
        $authenticationService = $container->get(AuthenticationService::class);
        $sessionManager = $container->get(SessionManager::class);
        $rbacManager = $container->get(RbacManager::class);
        // Get contents of 'access_filter' config key (the AuthManager service will use this data
        // to determine whether to allow currently logged in user to execute the controller action or not
        $configAll = $container->get('Config');
        $config = [];
        $config1 = isset($configAll['access_filter']) ? $configAll['access_filter'] : [];
        $config2 = isset($configAll['session_config']) ? $configAll['session_config'] : [];
        $config = array_merge($config, $config1, $config2);
        $logger = $container->get('LoggerGlobal');

        return new AuthManager($authenticationService, $sessionManager, $config, $rbacManager, $logger);
    }
}
