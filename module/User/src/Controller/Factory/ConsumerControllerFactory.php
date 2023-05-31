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

namespace User\Controller\Factory;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\ConsumerController;
use User\Kafka\ConsumerKafka;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Class ConsumerControllerFactory
 * This is the factory for ConsumerController
 * @package User\Controller\Factory
 */
class ConsumerControllerFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return ConsumerController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConsumerController
    {
        $consumerKafka = $container->get(ConsumerKafka::class);

        return new ConsumerController($consumerKafka);
    }
}
