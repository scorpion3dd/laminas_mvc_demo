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

namespace User\View\Helper\Factory;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\View\Helper\CurrentUser;
use Laminas\Authentication\AuthenticationService;

/**
 * Class CurrentUserFactory
 * This is the factory for CurrentUser
 * @package User\View\Helper\Factory
 */
class CurrentUserFactory
{
    /**
     * @param ContainerInterface $container
     *
     * @return CurrentUser
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): CurrentUser
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(AuthenticationService::class);
        $logger = $container->get('LoggerGlobal');

        return new CurrentUser($entityManager, $authService, $logger);
    }
}
