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

namespace User\Kafka;

use Application\Service\MailSender;
use Doctrine\ORM\EntityManager;
use Exception;
use Kafka\Consumer;
use Kafka\ConsumerConfig;
use Monolog\Handler\StreamHandler;
use User\Entity\User;
use User\Enum\Queue;
use Laminas\Log\Logger;

/**
 * Class ConsumerKafka
 * @package User\Kafka
 */
class ConsumerKafka extends AbstractKafka
{
    /** @var Consumer|null $consumer */
    private ?Consumer $consumer;

    /** @var MailSender $mailSender */
    private MailSender $mailSender;

    /** @var EntityManager $entityManager */
    protected EntityManager $entityManager;

    /**
     * ConsumerKafka constructor
     * @param Logger $logger
     * @param MailSender $mailSender
     * @param EntityManager $entityManager
     * @param array $config
     */
    public function __construct(
        Logger $logger,
        MailSender $mailSender,
        EntityManager $entityManager,
        array $config
    ) {
        parent::__construct($logger, $config);
        $this->mailSender = $mailSender;
        $this->entityManager = $entityManager;
    }

    /**
     * Example:
     * $message = 'a:3:{s:6:"offset";i:10;s:4:"size";i:168;s:7:"message";a:6:{s:3:"crc";i:1584643651;s:5:"magic";i:1;s:4:"attr";i:0;s:9:"timestamp";i:-1;s:3:"key";s:16:"email_send_queue";s:5:"value";s:137:"{"payload":{"userFrom":{"id":2,"fullName":"Admin"},"message":{"content":"Your access to recurse changed to - Yes"},"recipientIds":[185]}}";}}';
     * $this->parseMessage('user_notification', $message, 0);
     *
     * @return void
     */
    public function start(): void
    {
        $this->logger->info('consumer->start before');
        $this->consumer = $this->getConsumer();
        if (empty($this->consumer)) {
            // @codeCoverageIgnoreStart
            $this->logger->err('consumer is null, not start read message');
            // @codeCoverageIgnoreEnd
        } else {
            try {
                $this->consumer->start(function ($topic, $part, $message) {
                    // @codeCoverageIgnoreStart
                    $this->parseMessage($topic, serialize($message), (int)$part);
                    // @codeCoverageIgnoreEnd
                });
            } catch (Exception $e) {
                $this->logger->log(Logger::ERR, $e->getMessage());
                $this->logger->err('consumer is not, not start read message');
            }
        }
    }

    /**
     * @param string $topic
     * @param string $message
     * @param int $part
     *
     * @return void
     */
    public function parseMessage(string $topic, string $message, int $part): void
    {
        $this->logger->info('consumer->start in parseMessage');
        $this->logger->info('part = ' . $part);
        $this->logger->info('topic = ' . $topic);
        $this->logger->info('message = ' . $message);
        if ($topic == Queue::USER_NOTIFICATION) {
            $params = unserialize($message);
            $key = isset($params['message']['key']) ? $params['message']['key'] : '';
            $this->logger->info('key = ' . $key);
            $value = isset($params['message']['value']) ? json_decode($params['message']['value'], true) : [];
            $this->logger->info('value = ' . json_encode($params['message']['value']));
            if ($key == Queue::EMAIL_SEND_QUEUE) {
                $recipientIds = isset($value['payload']['recipientIds']) ? $value['payload']['recipientIds'] : [];
                $content = isset($value['payload']['message']['content']) ? $value['payload']['message']['content'] : '';
                $this->logger->info('content = ' . $content);
                $userFromId = isset($value['payload']['userFrom']['id']) ? $value['payload']['userFrom']['id'] : 0;
                $this->executeMessage((int)$userFromId, $recipientIds, $content);
            }
        }
    }

    /**
     * @param int $userFromId
     * @param array $recipientIds
     * @param string $content
     *
     * @return void
     */
    private function executeMessage(int $userFromId, array $recipientIds, string $content): void
    {
        $this->logger->info('userFromId = ' . $userFromId);
        /** @var User $userFrom */
        $userFrom = $this->entityManager->getRepository(User::class)->find($userFromId);
        $this->logger->info('userFromEmail = ' . $userFrom->getEmail());

        $users = $this->entityManager->getRepository(User::class)->findBy(['id' => $recipientIds]);
        /** @var User $userTo */
        foreach ($users as $userTo) {
            $this->logger->info('userToId = ' . $userTo->getId());
            $this->logger->info('userToEmail = ' . $userTo->getEmail());
            $this->logger->info('subject = ' . Queue::EMAIL_SUBJECT);
            if ($this->getMailSender()->sendMail(
                $userFrom->getEmail(),
                $userTo->getEmail(),
                Queue::EMAIL_SUBJECT,
                $content
            )) {
                $this->logger->info('mailSender sendMail true');
            }
        }
    }

    /**
     * @return void
     */
    private function buildConsumer(): void
    {
        try {
            $host = self::HOST;
            $port = self::PORT;
            $brokerVersion = self::BROKER_VERSION;
            $params = $this->getParams();
            if (count($params) > 0) {
                $host = $params['host'];
                $port = $params['port'];
                $brokerVersion = $params['brokerVersion'];
            }
            $config = ConsumerConfig::getInstance();
            $config->setMetadataRefreshIntervalMs(10);
            $config->setMetadataBrokerList($host . ':' . $port);
            $config->setGroupId('test');
            $config->setBrokerVersion($brokerVersion);
            $config->setTopics([Queue::USER_NOTIFICATION]);
            $config->setOffsetReset('earliest');
            $consumer = new Consumer();
            $filelog = isset($this->config['kafka']['filelog']) ? $this->config['kafka']['filelog'] : '';
            $logfile = __DIR__ . '/../../../../data/logs/' . $filelog;
            if (file_exists($logfile)) {
                $logger = new \Monolog\Logger('loggerKafka');
                $logger->pushHandler(new StreamHandler($logfile));
                $consumer->setLogger($logger);
            }
            $this->consumer = $consumer;
        // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, $e->getMessage());
            $this->consumer = null;
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * @return Consumer|null
     */
    public function getConsumer(): ?Consumer
    {
        if (empty($this->consumer)) {
            $this->buildConsumer();
        }

        return $this->consumer;
    }

    /**
     * @param Consumer $consumer
     *
     * @return $this
     */
    public function setConsumer(Consumer $consumer): self
    {
        $this->consumer = $consumer;

        return $this;
    }

    /**
     * @return MailSender
     */
    public function getMailSender(): MailSender
    {
        return $this->mailSender;
    }

    /**
     * @param MailSender $mailSender
     */
    public function setMailSender(MailSender $mailSender): void
    {
        $this->mailSender = $mailSender;
    }
}
