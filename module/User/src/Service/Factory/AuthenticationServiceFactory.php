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
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Storage\Session as SessionStorage;
use User\Service\AuthAdapter;

/**
 * The factory responsible for creating of authentication service
 * @package User\Service\Factory
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return AuthenticationService
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthenticationService
    {
        $sessionManager = $container->get(SessionManager::class);
        $authStorage = new SessionStorage('Demo_Auth', 'session', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        return new AuthenticationService($authStorage, $authAdapter);
    }
}
