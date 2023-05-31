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

namespace UserTest\unit\Service;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\Permission;
use User\Entity\Role;
use User\Repository\RoleRepository;
use User\Service\RoleManager;

/**
 * Class RoleManagerNegativeTest - Unit negative tests for RoleManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class RoleManagerNegativeTest extends AbstractMock
{
    /** @var RoleManager $roleManager */
    public RoleManager $roleManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->roleManager = $this->serviceManager->get(RoleManager::class);
    }

    /**
     * @testCase - method addRole - Exception
     * Role with such name already exists
     *
     * @return void
     * @throws Exception
     */
    public function testAddRoleException(): void
    {
        $data = [
            'name' => self::USER_ROLE_NAME_ADMINISTRATOR
        ];
        $existingRole = $this->createRole();

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingRole);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Role with such name already exists');
        $this->expectExceptionCode(0);
        $this->roleManager->addRole($data);
    }

    /**
     * @testCase - method addRole - Exception
     * empty parentRole - Exception - Role to inherit not found
     *
     * @return void
     * @throws Exception
     */
    public function testAddRoleExceptionEmptyParentRole(): void
    {
        $parentRole = null;
        $existingRole = null;
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);
        $role->setPermissions($permission);
        $roles[] = $role;
        $data = [
            'name' => self::USER_PERMISSION_PROFILE_ANY_VIEW,
            'description' => self::USER_PERMISSION_DESCRIPTION,
            'inherit_roles' => [$role->getId()]
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName', 'findOneById'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingRole);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneById')
            ->with(
                $this->equalTo(self::ROLE_ID),
            )
            ->willReturn($parentRole);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Role to inherit not found');
        $this->expectExceptionCode(0);
        $this->roleManager->addRole($data);
    }

    /**
     * @testCase - method updateRole - Exception
     * Another role with such name already exists
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateRoleException(): void
    {
        $data = [
            'name' => self::USER_ROLE_NAME_ADMINISTRATOR
        ];
        $existingRole = $this->createRole();
        $role = $this->createRole();

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingRole);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Another role with such name already exists');
        $this->expectExceptionCode(0);
        $this->roleManager->updateRole($role, $data);
    }

    /**
     * @testCase - method updateRole - Exception
     * empty parentRole - Exception - Role to inherit not found
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateRoleExceptionEmptyParentRole(): void
    {
        $parentRole = null;
        $existingRole = null;
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);
        $role->setPermissions($permission);
        $roles[] = $role;
        $data = [
            'name' => self::USER_PERMISSION_PROFILE_ANY_VIEW,
            'description' => self::USER_PERMISSION_DESCRIPTION,
            'inherit_roles' => [$role->getId()]
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName', 'findOneById'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingRole);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneById')
            ->with(
                $this->equalTo(self::ROLE_ID),
            )
            ->willReturn($parentRole);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Role to inherit not found');
        $this->expectExceptionCode(0);
        $this->roleManager->updateRole($role, $data);
    }

    /**
     * @testCase - method createDefaultRolesIfNotExistException - Exception
     * empty parentRole - Exception - Parent role ... doesn\'t exist
     *
     * @return void
     * @throws Exception
     */
    public function testCreateDefaultRolesIfNotExistException(): void
    {
        $existingRole = null;
        $permission = $this->createPermission();
        $permissions = [];
        $permissions[] = $permission;
        $info = [];
        $info['permissions'] = [
            'user.manage',
            'role.manage',
            'permission.manage',
            'profile.any.view'
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->addMethods(['findOneByName', 'findByName', 'getAllDefaultRoles'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo(self::USER_ROLE_NAME_ADMINISTRATOR),
            )
            ->willReturn(null);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($existingRole);

        $repositoryMock->expects($this->exactly(1))
            ->method('findByName')
            ->withConsecutive(
                [$this->equalTo($info['permissions'])]
            )
            ->willReturn($permissions);

        $defaultRoles = RoleRepository::DEFAULT_ROLES;
        $defaultRoles['Guest']['parent'] = self::USER_ROLE_NAME_ADMINISTRATOR;
        $repositoryMock->expects($this->exactly(1))
            ->method('getAllDefaultRoles')
            ->willReturn($defaultRoles);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
                [Permission::class]
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Parent role ' . self::USER_ROLE_NAME_ADMINISTRATOR . ' doesn\'t exist');
        $this->expectExceptionCode(0);
        $this->roleManager->createDefaultRolesIfNotExist();
    }

    /**
     * @testCase - method updateRolePermissions - Exception
     * Permission with such name doesn\'t exist
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateRolePermissionsException(): void
    {
        $permission = null;
        $role = $this->createRole();
        $info = [];
        $info['permissions'] = [
            'user.manage' => 0,
            'role.manage' => 0,
            'permission.manage' => 1,
            'profile.any.view' => 0,
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->withConsecutive(
                [$this->equalTo('permission.manage')]
            )
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class]
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Permission with such name doesn\'t exist');
        $this->expectExceptionCode(0);
        $this->roleManager->updateRolePermissions($role, $info);
    }
}
