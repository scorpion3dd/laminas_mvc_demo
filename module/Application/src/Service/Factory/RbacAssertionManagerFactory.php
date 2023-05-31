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
use Application\Service\RbacAssertionManager;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\Authentication\AuthenticationService;

/**
 * Class RbacAssertionManagerFactory
 * This is the factory for RbacAssertionManager
 * @package Application\Service\Factory
 */
class RbacAssertionManagerFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     *
     * @param array|null $options
     * @return RbacAssertionManager
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RbacAssertionManager
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(AuthenticationService::class);

        return new RbacAssertionManager($container, $entityManager, $authService);
    }
}
