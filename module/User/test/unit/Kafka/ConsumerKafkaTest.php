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

use Application\Service\MailSender;
use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Kafka\Consumer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use User\Entity\User;
use User\Enum\Queue;
use User\Kafka\AbstractKafka;
use User\Kafka\ConsumerKafka;

/**
 * Class ConsumerKafkaTest - Unit tests for ConsumerKafka
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Kafka
 */
class ConsumerKafkaTest extends AbstractMock
{
    /** @var ConsumerKafka $consumerKafka */
    public ConsumerKafka $consumerKafka;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->consumerKafka = $this->serviceManager->get(ConsumerKafka::class);
    }

    /**
     * @testCase - method start - Exception
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testStartException(): void
    {
        $logger = $this->serviceManager->get('LoggerGlobal');
        $mailSender = $this->serviceManager->get(MailSender::class);
        $config = $this->getConfig();
        $config['kafka']['connection']['default']['params'] = [
            'host' => AbstractKafka::HOST,
            'port' => AbstractKafka::PORT,
            'brokerVersion' => AbstractKafka::BROKER_VERSION
        ];
        $consumerKafka = new ConsumerKafka($logger, $mailSender, $this->entityManager, $config);
        $consumerKafka->start();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method start - must be a success
     *
     * @return void
     */
    public function testStart(): void
    {
        $consumerMock = $this->getMockBuilder(Consumer::class)
            ->onlyMethods(['start'])
            ->disableOriginalConstructor()
            ->getMock();

        $consumerMock->expects($this->exactly(1))
            ->method('start');

        $this->consumerKafka->setConsumer($consumerMock);

        $this->consumerKafka->start();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method parseMessage - must be a success
     *
     * @return void
     * @throws ReflectionException
     */
    public function testParseMessage(): void
    {
        $topic = Queue::USER_NOTIFICATION;
        $message = 'a:3:{s:6:"offset";i:10;s:4:"size";i:168;s:7:"message";a:6:{s:3:"crc";i:1584643651;s:5:"magic";i:1;s:4:"attr";i:0;s:9:"timestamp";i:-1;s:3:"key";s:16:"email_send_queue";s:5:"value";s:137:"{"payload":{"userFrom":{"id":2,"fullName":"Admin"},"message":{"content":"Your access to recurse changed to - Yes"},"recipientIds":[185]}}";}}';
        $part = 1;
        $userFrom = $this->createUser();
        $this->setEntityId($userFrom, self::USER_ID);
        $users = [];
        $users[] = $userFrom;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['find', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('find')
            ->with($this->equalTo(2))
            ->willReturn($userFrom);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with($this->equalTo(['id' => [185]]))
            ->willReturn($users);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [User::class]
            )
            ->willReturn($repositoryMock);

        $mailSenderMock = $this->getMockBuilder(MailSender::class)
            ->onlyMethods(['sendMail'])
            ->disableOriginalConstructor()
            ->getMock();

        $mailSenderMock->expects(self::exactly(1))
            ->method('sendMail')
            ->with(
                $this->equalTo(self::USER_EMAIL),
                $this->equalTo(self::USER_EMAIL),
                $this->equalTo(Queue::EMAIL_SUBJECT),
                $this->equalTo('Your access to recurse changed to - Yes'),
            )
            ->willReturn(true);

        $this->consumerKafka->setMailSender($mailSenderMock);

        $this->consumerKafka->parseMessage($topic, $message, $part);
        $this->assertTrue(true);
    }
}
