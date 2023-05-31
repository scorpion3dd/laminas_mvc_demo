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

use Laminas\Log\Logger;

/**
 * Class AbstractKafka
 * @package User\Kafka
 */
abstract class AbstractKafka
{
    public const HOST = 'localhost';
    public const PORT = '9092';
    public const BROKER_VERSION = '1.0.0';

    /** @var array $config */
    protected array $config;

    /** @var Logger $logger */
    protected Logger $logger;

    /**
     * AbstractKafka constructor
     * @param Logger $logger
     * @param array $config
     */
    public function __construct(
        Logger $logger,
        array $config
    ) {
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        return isset($this->config['kafka']['connection']['default']['params'])
            ? $this->config['kafka']['connection']['default']['params']
            : [];
    }
}
