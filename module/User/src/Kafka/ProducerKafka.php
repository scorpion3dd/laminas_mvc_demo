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

namespace User\Kafka;

use Exception;
use Kafka\Producer;
use Kafka\ProducerConfig;
use Monolog\Handler\StreamHandler;
use User\Entity\User;
use User\Enum\Queue;
use Laminas\Log\Logger;

/**
 * Class ProducerKafka
 * @package User\Kafka
 */
class ProducerKafka extends AbstractKafka
{
    /** @var Producer|null $producer */
    private ?Producer $producer;

    /**
     * ProducerKafka constructor
     * @param array $config
     * @param Logger $logger
     */
    public function __construct(array $config, Logger $logger)
    {
        parent::__construct($logger, $config);
    }

    /**
     * @param string $message
     * @param string $serviceName
     * @param User $userFrom
     * @param User $userTo
     *
     * @return void
     */
    public function send(string $message, string $serviceName, User $userFrom, User $userTo): void
    {
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
        $this->producer = $this->getProducer();
        if (empty($this->producer)) {
            $this->logger->err($serviceName . ' producer is null, not send message');
        } else {
            /** @phpstan-ignore-next-line */
            $result = $this->producer->send($params);
            $this->logger->info($serviceName . ' producer->send');
            $this->logger->info($serviceName . ' topic = ' . Queue::USER_NOTIFICATION);
            $this->logger->info($serviceName . ' message = ' . $message);
            $this->logger->info($serviceName . ' payload = ' . $payloadStr);
            $this->logger->info($serviceName . ' params = ' . serialize($params));
            $this->logger->info($serviceName . ' result = ' . serialize($result));
        }
    }

    /**
     * @return void
     */
    private function buildProducer(): void
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
            $config = ProducerConfig::getInstance();
            $config->setMetadataRefreshIntervalMs(10000);
            $config->setMetadataBrokerList($host . ':' . $port);
            $config->setBrokerVersion($brokerVersion);
            $config->setRequiredAck(1);
            $config->setIsAsyn(false);
            $config->setProduceInterval(500);
            $filelog = isset($this->config['kafka']['filelog']) ? $this->config['kafka']['filelog'] : '';
            $logfile = __DIR__ . '/../../../../data/logs/' . $filelog;
            if (file_exists($logfile)) {
                $logger = new \Monolog\Logger('loggerKafka');
                $logger->pushHandler(new StreamHandler($logfile));
                $producer = new Producer();
                // @codeCoverageIgnoreStart
                $producer->setLogger($logger);
                $this->producer = $producer;
                // @codeCoverageIgnoreEnd
            }
        } catch (Exception $e) {
            $this->logger->log(Logger::ERR, $e->getMessage());
            $this->producer = null;
        }
    }

    /**
     * @return Producer|null
     */
    public function getProducer(): ?Producer
    {
        if (empty($this->producer)) {
            $this->buildProducer();
        }
        return $this->producer;
    }

    /**
     * @param Producer $producer
     *
     * @return $this
     */
    public function setProducer(Producer $producer): self
    {
        $this->producer = $producer;

        return $this;
    }
}
