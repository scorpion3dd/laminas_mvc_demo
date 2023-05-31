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
use User\Service\AuthAdapter;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * This is the factory class for AuthAdapter service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies)
 * @package User\Service\Factory
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return AuthAdapter
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AuthAdapter
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $sessionContainer = $container->get('Demo_Auth');

        return new AuthAdapter($entityManager, $sessionContainer);
    }
}
