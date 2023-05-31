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

namespace UserTest\unit\Repository;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use User\Entity\User;
use User\Repository\UserRepository;

/**
 * Class UserRepositoryTest - Unit tests for UserRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Repository
 */
class UserRepositoryTest extends AbstractMock
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - method findAllUsers - must be a success
     *
     * @return void
     */
    public function testFindAllUsers(): void
    {
        $manager = $this->getMockBuilder(EntityManager::class)
            ->onlyMethods(['createQueryBuilder'])
            ->disableOriginalConstructor()
            ->getMock();

        $class = new ClassMetadata(User::class);
        $userRepository = new UserRepository($manager, $class);

        $repository = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['select', 'from', 'orderBy', 'getQuery'])
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::exactly(1))
            ->method('select')
            ->withConsecutive(
                [self::equalTo('u')],
            )
            ->willReturn($repository);

        $repository->expects(self::once())
            ->method('from')
            ->with(User::class)
            ->willReturn($repository);

        $repository->expects(self::once())
            ->method('orderBy')
            ->withConsecutive(
                [self::equalTo('u.dateCreated')],
                [self::equalTo('ASC')]
            )
            ->willReturn($repository);

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->onlyMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $query->expects($this->exactly(0))
            ->method('getOneOrNullResult')
            ->with($this->equalTo(Query::HYDRATE_ARRAY))
            ->willReturn(null);

        $repository->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $manager->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($repository);

        $result = $userRepository->findAllUsers();
        self::assertSame($query, $result, 'data is not correct');
    }

    /**
     * @testCase - method findUsersAccess - must be a success
     *
     * @return void
     */
    public function testFindUsersAccess(): void
    {
        $access = 1;
        $status = 1;
        $manager = $this->getMockBuilder(EntityManager::class)
            ->onlyMethods(['createQueryBuilder'])
            ->disableOriginalConstructor()
            ->getMock();

        $class = new ClassMetadata(User::class);
        $userRepository = new UserRepository($manager, $class);

        $repository = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods(['select', 'from', 'where', 'andWhere', 'setParameter', 'orderBy', 'getQuery'])
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::exactly(1))
            ->method('select')
            ->withConsecutive(
                [self::equalTo('u')],
            )
            ->willReturn($repository);

        $repository->expects(self::once())
            ->method('from')
            ->with(User::class)
            ->willReturn($repository);

        $repository->expects(self::once())
            ->method('where')
            ->withConsecutive(
                [self::equalTo('u.access = :access')]
            )
            ->willReturn($repository);

        $repository->expects(self::once())
            ->method('andWhere')
            ->withConsecutive(
                [self::equalTo('u.status = :status')]
            )
            ->willReturn($repository);

        $repository->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['access', $access],
                ['status', $status],
            )
            ->willReturn($repository);

        $repository->expects(self::once())
            ->method('orderBy')
            ->withConsecutive(
                [self::equalTo('u.id')],
                [self::equalTo('ASC')]
            )
            ->willReturn($repository);

        $query = $this->getMockBuilder(AbstractQuery::class)
            ->onlyMethods(['getOneOrNullResult'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $query->expects($this->exactly(0))
            ->method('getOneOrNullResult')
            ->with($this->equalTo(Query::HYDRATE_ARRAY))
            ->willReturn(null);

        $repository->expects(self::once())
            ->method('getQuery')
            ->willReturn($query);

        $manager->expects(self::once())
            ->method('createQueryBuilder')
            ->willReturn($repository);

        $result = $userRepository->findUsersAccess($access, $status);
        self::assertSame($query, $result, 'data is not correct');
    }
}
