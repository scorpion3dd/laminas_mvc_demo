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

namespace User\Kafka\Factory;

use Application\Service\MailSender;
use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Kafka\ConsumerKafka;

/**
 * This is the factory class for ConsumerKafka. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies)
 * @package User\Kafka\Factory
 */
class ConsumerKafkaFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return ConsumerKafka
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConsumerKafka
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $config = $container->get('Config');
        $logger = $container->get('LoggerGlobal');
        $mailSender = $container->get(MailSender::class);

        return new ConsumerKafka($logger, $mailSender, $entityManager, $config);
    }
}
