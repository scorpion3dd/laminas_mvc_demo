<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Zend Framework 3 Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2020-2021 scorpion3dd
 */

declare(strict_types=1);

namespace UserTest\unit\Kafka;

use ApplicationTest\AbstractMock;
use Kafka\Producer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use User\Enum\Queue;
use User\Kafka\AbstractKafka;
use User\Kafka\ProducerKafka;

/**
 * Class ProducerKafkaTest - Unit tests for ProducerKafka
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Kafka
 */
class ProducerKafkaTest extends AbstractMock
{
    /** @var ProducerKafka $producerKafka */
    public ProducerKafka $producerKafka;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - method send - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testSend(): void
    {
        $message = 'Your status set to - No';
        $serviceName = 'addUser';
        $userFrom = $this->createUser();
        $this->setEntityId($userFrom, self::USER_ID);
        $userTo = $this->createUser();
        $this->setEntityId($userTo, self::USER_ID + 1);

        $payload = [
            'payload' => [
                'userFrom' => [
                    'id' => $userFrom->getId(),
                    'fullName' => $userFrom->getFullName()
                ],
                'message' => [
                    'content' => $message
                ],
                'recipientIds' => [$userTo->getId()]
            ]
        ];
        $payloadStr = json_encode($payload);
        $params = [
            [
                'topic' => Queue::USER_NOTIFICATION,
                'value' => $payloadStr,
                'key' => Queue::EMAIL_SEND_QUEUE,
            ]
        ];

        $producerMock = $this->getMockBuilder(Producer::class)
            ->onlyMethods(['send'])
            ->disableOriginalConstructor()
            ->getMock();

        $producerMock->expects($this->exactly(1))
            ->method('send')
            ->with(
                $this->equalTo($params),
            );

        $this->getProducerKafka()->setProducer($producerMock);

        $this->getProducerKafka()->send($message, $serviceName, $userFrom, $userTo);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method send - must be a success
     * empty producer
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testSendEmptyProducer(): void
    {
        $message = 'Your status set to - No';
        $serviceName = 'addUser';
        $userFrom = $this->createUser();
        $this->setEntityId($userFrom, self::USER_ID);
        $userTo = $this->createUser();
        $this->setEntityId($userTo, self::USER_ID + 1);
        $config = $this->getConfig();
        $config['kafka']['connection']['default']['params'] = [
            'host' => AbstractKafka::HOST,
            'port' => AbstractKafka::PORT,
            'brokerVersion' => AbstractKafka::BROKER_VERSION
        ];
        $this->setConfig($config);
        $this->getProducerKafka()->send($message, $serviceName, $userFrom, $userTo);
        $this->assertTrue(true);
    }

    /**
     * @return ProducerKafka
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getProducerKafka(): ProducerKafka
    {
        if (empty($this->producerKafka)) {
            $this->buildProducerKafka();
        }

        return $this->producerKafka;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function buildProducerKafka(): void
    {
        $logger = $this->serviceManager->get('LoggerGlobal');
        $this->producerKafka = new ProducerKafka($this->config, $logger);
    }
}
