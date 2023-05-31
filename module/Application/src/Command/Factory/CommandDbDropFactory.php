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

use Application\Command\CommandDbDrop;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class CommandDbDropFactory
 * This is the factory for CommandDbDrop
 * @package Application\Command\Factory
 */
class CommandDbDropFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return CommandDbDrop
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CommandDbDrop
    {
        $logger = $container->get('LoggerGlobal');
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $documentManager = $container->get('doctrine.documentmanager.odm_default');
        $redis = $container->get('User\Db\Cache\Redis');

        return new CommandDbDrop($logger, $entityManager, $documentManager, $redis);
    }
}
