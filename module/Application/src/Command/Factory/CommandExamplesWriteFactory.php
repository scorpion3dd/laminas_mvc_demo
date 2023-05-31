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

use Application\Command\CommandExamplesWrite;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class CommandExamplesWriteFactory
 * This is the factory for CommandExamplesWrite
 * @package Application\Command\Factory
 */
class CommandExamplesWriteFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return CommandExamplesWrite
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CommandExamplesWrite
    {
        $logger = $container->get('LoggerGlobal');

        return new CommandExamplesWrite($logger);
    }
}
