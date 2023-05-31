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

namespace UserTest\unit\Service;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\Permission;
use User\Entity\Role;
use User\Service\PermissionManager;

/**
 * Class PermissionManagerTest - Unit tests for PermissionManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class PermissionManagerTest extends AbstractMock
{
    /** @var PermissionManager $permissionManager */
    public PermissionManager $permissionManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->permissionManager = $this->serviceManager->get(PermissionManager::class);
    }

    /**
     * @testCase - method addPermission - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddPermission(): void
    {
        $data = [
            'name' => self::USER_PERMISSION_PROFILE_ANY_VIEW,
            'description' => self::USER_PERMISSION_DESCRIPTION
        ];
        $existingPermission = null;
        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingPermission);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->permissionManager->addPermission($data);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method updatePermission - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdatePermission(): void
    {
        $data = [
            'name' => self::USER_PERMISSION_PROFILE_ANY_VIEW,
            'description' => self::USER_PERMISSION_DESCRIPTION
        ];
        $existingPermission = null;
        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingPermission);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->permissionManager->updatePermission($permission, $data);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method deletePermission - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testDeletePermission(): void
    {
        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->permissionManager->deletePermission($permission);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method createDefaultPermissionsIfNotExist - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testCreateDefaultPermissionsIfNotExist(): void
    {
        $existingPermission = null;
        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy', 'findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($existingPermission);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->permissionManager->createDefaultPermissionsIfNotExist();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method createDefaultPermissionsIfNotExist - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testCreateDefaultPermissionsIfNotExistReturn(): void
    {
        $existingPermission = $this->createPermission();

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($existingPermission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->permissionManager->createDefaultPermissionsIfNotExist();
        $this->assertTrue(true);
    }
}
