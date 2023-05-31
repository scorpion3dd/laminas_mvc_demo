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

namespace ApplicationTest\unit\Service;

use Application\Document\Log;
use Application\Service\LogService;
use ApplicationTest\AbstractMock;
use Carbon\Carbon;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Laminas\Log\Logger;
use Laminas\Paginator\Paginator;

/**
 * Class LogServiceTest - Unit tests for LogService
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Service
 */
class LogServiceTest extends AbstractMock
{
    /** @var LogService $logService */
    public LogService $logService;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->prepareDbMongo();
        $this->logService = $this->serviceManager->get(LogService::class);
    }

    /**
     * @testCase - method addLog - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddLog(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);
        $timestamp = Carbon::now();
        $data = [
            'message' => self::LOG_MESSAGE,
            'timestamp' => $timestamp,
            'priority' => Logger::ALERT,
        ];
        $expectedLog = $this->createLog();
        $expectedLog->setTimestamp($timestamp);
        $result = $this->logService->addLog($currentUser, $data);
        $this->assertEquals($expectedLog, $result);
    }

    /**
     * @testCase - method updateLog - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateLog(): void
    {
        $currentUser = $this->createUser();
        $this->setEntityId($currentUser, self::USER_ID);
        $data = [
            'message' => 'example text',
            'timestamp' => Carbon::now(),
            'priority' => Logger::EMERG,
        ];
        $log = $this->createLog();
        $result = $this->logService->updateLog($log, $currentUser, $data);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method removeLog - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRemoveLog(): void
    {
        $log = $this->createLog();
        $result = $this->logService->removeLog($log);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method getLog - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetLog(): void
    {
        $repositoryMock = $this->getMockBuilder(DocumentManager::class)
            ->addMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $log = $this->createLog();
        $this->setEntityId($log, self::LOG_ID);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['id' => self::LOG_ID]))
            ->willReturn($log);

        $this->documentManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Log::class]
            )
            ->willReturn($repositoryMock);

        $result = $this->logService->getLog(self::LOG_ID);
        $this->assertEquals($log, $result);
    }

    /**
     * @testCase - method getLogs - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetLogs(): void
    {
        $repositoryMock = $this->getMockBuilder(DocumentManager::class)
            ->addMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $logs = [];
        $log = $this->createLog();
        $this->setEntityId($log, self::LOG_ID);
        $logs[] = $log;

        $count = 5;
        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
                $this->equalTo($count),
            )
            ->willReturn($logs);

        $this->documentManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Log::class]
            )
            ->willReturn($repositoryMock);

        $result = $this->logService->getLogs();
        $this->assertEquals($logs, $result);
    }

    /**
     * @testCase - method getLogsPaginator - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetLogsPaginator(): void
    {
        $repositoryMock = $this->getMockBuilder(DocumentManager::class)
            ->addMethods(['findAllLogs'])
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $repositoryMock->expects($this->exactly(1))
            ->method('findAllLogs')
            ->willReturn($query);

        $this->documentManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Log::class]
            )
            ->willReturn($repositoryMock);

        $adapter = new DoctrineAdapter(new ORMPaginator($query, false));
        $paginator = new Paginator($adapter);
        $result = $this->logService->getLogsPaginator(1);
        $this->assertEquals($paginator, $result);
    }
}
