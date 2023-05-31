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

namespace Application\Service\Factory;

use Interop\Container\ContainerInterface;
use Application\Service\NavManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\RbacManager;
use Laminas\Authentication\AuthenticationService;

/**
 * Class NavManagerFactory
 * This is the factory for NavManager
 * @package Application\Service\Factory
 */
class NavManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return NavManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): NavManager
    {
        $authService = $container->get(AuthenticationService::class);
        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');
        $rbacManager = $container->get(RbacManager::class);

        return new NavManager($container, $authService, $urlHelper, $rbacManager);
    }
}
