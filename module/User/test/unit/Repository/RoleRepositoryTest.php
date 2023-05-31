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
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use User\Entity\Role;
use User\Repository\RoleRepository;

/**
 * Class RoleRepositoryTest - Unit tests for RoleRepository
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Repository
 */
class RoleRepositoryTest extends AbstractMock
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - method getAllDefaultRoles - must be a success
     *
     * @return void
     */
    public function testGetAllDefaultRoles(): void
    {
        $manager = $this->getMockBuilder(EntityManager::class)
            ->onlyMethods(['createQueryBuilder'])
            ->disableOriginalConstructor()
            ->getMock();

        $class = new ClassMetadata(Role::class);
        $roleRepository = new RoleRepository($manager, $class);

        $result = $roleRepository->getAllDefaultRoles();
        self::assertSame(RoleRepository::DEFAULT_ROLES, $result, 'data is not correct');
    }
}
