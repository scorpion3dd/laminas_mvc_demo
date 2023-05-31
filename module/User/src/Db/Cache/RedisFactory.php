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

namespace User\Db\Cache;

use Exception;
use Laminas\Log\Logger;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Redis;

/**
 * Class RedisFactory
 * @package User\Db\Cache
 */
class RedisFactory
{
    /** @var array $config */
    private array $config;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     *
     * @return Redis
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null): Redis
    {
        $redis = new Redis();
        if (! empty($options['host'])) {
            $host = $this->getConfigHost($container, $options['host']);
        } else {
            $host = $this->getConfigHost($container);
        }
        try {
            $redis->connect($host);
        } catch (Exception $e) {
            /** @var Logger $logger */
            $logger = $container->get('LoggerGlobal');
            $logger->err('Error: Message - ' . $e->getMessage()
                . ', in file - ' . $e->getFile()
                . ', in line - ' . $e->getLine(), ['host:' . $host]);
        }

        return $redis;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getConfig(ContainerInterface $container): array
    {
        if (empty($this->config)) {
            $this->config = $container->get('Config');
        }

        return $this->config;
    }

    /**
     * @param ContainerInterface $container
     * @param string $host
     *
     * @return string
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getConfigHost(ContainerInterface $container, string $host = ''): string
    {
        if (! empty($host)) {
            return $host;
        }
        $config = $this->getConfig($container);

        return isset($config['redis']['connection']['default']['params']['host'])
            ? $config['redis']['connection']['default']['params']['host'] : '';
    }
}
