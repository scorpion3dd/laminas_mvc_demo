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

namespace Application\Controller\Factory;

use Application\Service\LogService;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Application\Controller\LogsController;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class LogsControllerFactory
 * This is the factory for LogsController
 * @package Application\Controller\Factory
 */
class LogsControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return LogsController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): LogsController
    {
        $logger = $container->get('LoggerGlobal');
        $logService = $container->get(LogService::class);

        return new LogsController($logger, $logService);
    }
}
