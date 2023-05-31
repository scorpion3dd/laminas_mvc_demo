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

namespace Application\Command\Factory;

use Application\Command\CommandDbMigrations;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class CommandDbMigrationsFactory
 * This is the factory for CommandDbMigrations
 * @package Application\Command\Factory
 */
class CommandDbMigrationsFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return CommandDbMigrations
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CommandDbMigrations
    {
        $logger = $container->get('LoggerGlobal');

        return new CommandDbMigrations($logger);
    }
}
