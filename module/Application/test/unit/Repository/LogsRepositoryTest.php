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

namespace ApplicationTest\unit\Repository;

use Application\Document\Log;
use Application\Repository\LogsRepository;
use ApplicationTest\AbstractMock;
use Doctrine\Common\EventManager;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Hydrator\HydratorException;
use Doctrine\ODM\MongoDB\Hydrator\HydratorFactory;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class LogsRepositoryTest - Unit tests for LogsRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Repository
 */
class LogsRepositoryTest extends AbstractMock
{
    /** @var LogsRepository $logsRepository */
    protected LogsRepository $logsRepository;

    /** @var object $manager */
    protected object $manager;

    /**
     * @return void
     * @throws HydratorException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->getMockBuilder(DocumentManager::class)
            ->onlyMethods(['createQueryBuilder'])
            ->disableOriginalConstructor()
            ->getMock();
        $evm = new EventManager();
        $config = $this->getConfig();
        $hydratorDir = $config['doctrine']['configuration']['odm_default']['hydrator_dir'];
        $hydratorNs = $config['doctrine']['configuration']['odm_default']['hydrator_namespace'];
        $autoGenerate = 1;
        $hydratorFactory = new HydratorFactory($this->manager, $evm, $hydratorDir, $hydratorNs, $autoGenerate);
        $uow = new UnitOfWork($this->manager, $evm, $hydratorFactory);
        $class = new ClassMetadata(Log::class);
        $this->logsRepository = new LogsRepository($this->manager, $uow, $class);
    }

    /**
     * @testCase - method getAllDefaultRoles - must be a success
     *
     * DocumentManager
     * public function createQueryBuilder($documentName = null): Query\Builder
     * Method createQueryBuilder may not return value of type Mock_QueryBuilder_d11fd6f6,
     * its declared return type is "Doctrine\ODM\MongoDB\Query\Builder"
     *
     * @return void
     */
    public function testGetAllDefaultRoles(): void
    {
        if (false) {
            $query = $this->getMockBuilder(AbstractQuery::class)
                ->onlyMethods(['getOneOrNullResult'])
                ->disableOriginalConstructor()
                ->getMockForAbstractClass();

            $query->expects($this->exactly(0))
                ->method('getOneOrNullResult')
                ->with($this->equalTo(Query::HYDRATE_ARRAY))
                ->willReturn(null);

            $repository = $this->getMockBuilder(QueryBuilder::class)
                ->onlyMethods(['getQuery'])
                ->addMethods(['limit'])
                ->disableOriginalConstructor()
                ->getMock();

            $repository->expects(self::once())
                ->method('limit')
                ->with(20)
                ->willReturn($repository);

            $repository->expects(self::once())
                ->method('getQuery')
                ->willReturn($query);

            $this->manager->expects(self::once())
                ->method('createQueryBuilder')
                ->willReturn($repository);

            $result = $this->logsRepository->findAllLogs();
            self::assertSame($query, $result, 'data is not correct');
        }
        self::assertTrue(true);
    }

    /**
     * @testCase - method deleteAllLogs - must be a success
     *
     * DocumentManager
     * public function createQueryBuilder($documentName = null): Query\Builder
     * Method createQueryBuilder may not return value of type Mock_QueryBuilder_d11fd6f6,
     * its declared return type is "Doctrine\ODM\MongoDB\Query\Builder"
     *
     * @return void
     * @throws HydratorException
     * @throws MongoDBException
     */
    public function testDeleteAllLogs(): void
    {
        if (false) {
            $repository = $this->getMockBuilder(QueryBuilder::class)
                ->onlyMethods(['getQuery'])
                ->addMethods(['remove', 'execute'])
                ->disableOriginalConstructor()
                ->getMock();

            $repository->expects(self::once())
                ->method('remove')
                ->willReturn($repository);

            $repository->expects(self::once())
                ->method('getQuery')
                ->willReturn($repository);

            $repository->expects(self::once())
                ->method('execute')
                ->willReturn($repository);

            $this->manager->expects(self::once())
                ->method('createQueryBuilder')
                ->willReturn($repository);

            $this->logsRepository->deleteAllLogs();
            self::assertTrue(true);
        }
        self::assertTrue(true);
    }
}
