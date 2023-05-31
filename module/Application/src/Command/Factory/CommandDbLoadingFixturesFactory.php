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

use Application\Command\CommandDbLoadingFixtures;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class CommandDbLoadingFixturesFactory
 * This is the factory for CommandDbLoadingFixtures
 * @package Application\Command\Factory
 */
class CommandDbLoadingFixturesFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return CommandDbLoadingFixtures
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CommandDbLoadingFixtures
    {
        $logger = $container->get('LoggerGlobal');
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $documentManager = $container->get('doctrine.documentmanager.odm_default');

        return new CommandDbLoadingFixtures($logger, $entityManager, $documentManager);
    }
}
