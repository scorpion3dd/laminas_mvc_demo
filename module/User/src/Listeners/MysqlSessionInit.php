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

namespace User\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Events;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\Log\Logger;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container;

/**
 * MySQL Session Init Event Subscriber
 * @package User\Listeners
 */
class MysqlSessionInit implements EventSubscriber
{
    /** @var ServiceManager $serviceManager */
    protected ServiceManager $serviceManager;

    /** @var string $name */
    protected string $name;

    /** @var Logger $logger */
    private Logger $logger;

    /** @var Container $sessionContainer */
    private Container $sessionContainer;

    /**
     * MysqlSessionInit constructor
     * @param Logger $logger
     * @param ServiceManager $serviceManager
     * @param string $name
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(Logger $logger, ServiceManager $serviceManager, string $name = '')
    {
        $this->serviceManager = $serviceManager;
        $this->logger = $logger;
        $this->name = $name;
        $this->sessionContainer = $serviceManager->get('Demo_Auth');
    }

    /**
     * @param ConnectionEventArgs $args
     *
     * @return void
     */
    public function postConnect(ConnectionEventArgs $args): void
    {
        try {
            $userId = 0;
            if (isset($this->sessionContainer->user_id)) {
                $userId = $this->sessionContainer->user_id;
            }
            if ($userId) {
                $args->getConnection()->executeUpdate(
                    'SET @SESSION.user_id = :user_id',
                    ['user_id' => $userId]
                );
            }
            $this->logger->log(Logger::INFO, sprintf('{%s} DB Connected.', $this->name));
        } catch (Exception $exception) {
            $this->logger->log(Logger::ERR, sprintf('{%s} Error: %s', $this->name, $exception->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    public function getSubscribedEvents()
    {
        return [Events::postConnect];
    }

    /**
     * @param Container $sessionContainer
     */
    public function setSessionContainer(mixed $sessionContainer): void
    {
        $this->sessionContainer = $sessionContainer;
    }
}
