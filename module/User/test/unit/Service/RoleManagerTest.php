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
use FixturesIntegration\RoleFixtures;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Redis;
use ReflectionException;
use User\Entity\Permission;
use User\Entity\Role;
use User\Repository\RoleRepository;
use User\Service\RoleManager;

/**
 * Class RoleManagerTest - Unit tests for RoleManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class RoleManagerTest extends AbstractMock
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
     * @testCase - method addRole - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAddRole(): void
    {
        $parentRole = $this->createRole();
        $this->setEntityId($parentRole, self::ROLE_ID + 1);
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
            ->onlyMethods(['findBy'])
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

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(3))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->roleManager->addRole($data);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method addRole - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateRole(): void
    {
        $parentRole = $this->createRole();
        $this->setEntityId($parentRole, self::ROLE_ID + 1);
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
            ->onlyMethods(['findBy'])
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

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(3))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->roleManager->updateRole($role, $data);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method deleteRole - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testDeleteRole(): void
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

        $this->roleManager->deleteRole($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method createDefaultRolesIfNotExist - must be a success
     * empty parent
     *
     * @return void
     * @throws Exception
     */
    public function testCreateDefaultRolesIfNotExistEmptyParent(): void
    {
        $existingRole = null;
        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $permissions = [];
        $permissions[] = $permission;

        $info = [];
        $info['permissions'] = [
            'user.manage',
            'role.manage',
            'permission.manage',
            'profile.any.view',
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy', 'findOneBy'])
            ->addMethods(['findByName', 'getAllDefaultRoles'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($existingRole);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $repositoryMock->expects($this->exactly(2))
            ->method('findByName')
            ->withConsecutive(
                [$this->equalTo($info['permissions'])],
                [$this->equalTo(['profile.own.view'])]
            )
            ->willReturn($permissions);

        $repositoryMock->expects($this->exactly(1))
            ->method('getAllDefaultRoles')
            ->willReturn(RoleRepository::DEFAULT_ROLES);

        $this->entityManager->expects($this->exactly(4))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->roleManager->createDefaultRolesIfNotExist();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method createDefaultRolesIfNotExist - must be a success
     * not empty parent - setParentRole
     *
     * @return void
     * @throws Exception
     */
    public function testCreateDefaultRolesIfNotExistSetParentRole(): void
    {
        $existingRole = null;
        $roles = [];
        $parentRole = $this->createRole();
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $permissions = [];
        $permissions[] = $permission;

        $info = [];
        $info['permissions'] = [
            'user.manage',
            'role.manage',
            'permission.manage',
            'profile.any.view',
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy', 'findOneBy'])
            ->addMethods(['findByName', 'getAllDefaultRoles', 'findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($existingRole);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $repositoryMock->expects($this->exactly(2))
            ->method('findByName')
            ->withConsecutive(
                [$this->equalTo($info['permissions'])],
                [$this->equalTo(['profile.own.view'])]
            )
            ->willReturn($permissions);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo(self::USER_ROLE_NAME_ADMINISTRATOR),
            )
            ->willReturn($parentRole);

        $defaultRoles = RoleRepository::DEFAULT_ROLES;
        $defaultRoles['Guest']['parent'] = self::USER_ROLE_NAME_ADMINISTRATOR;
        $repositoryMock->expects($this->exactly(1))
            ->method('getAllDefaultRoles')
            ->willReturn($defaultRoles);

        $this->entityManager->expects($this->exactly(4))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class],
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->roleManager->createDefaultRolesIfNotExist();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method createDefaultRolesIfNotExist - return
     *
     * @return void
     * @throws Exception
     */
    public function testCreateDefaultRolesIfNotExistReturn(): void
    {
        $existingRole = $this->createRole();
        $permission = $this->createPermission();
        $existingRole->setPermissions($permission);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with(
                $this->equalTo([]),
            )
            ->willReturn($existingRole);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);

        $this->roleManager->createDefaultRolesIfNotExist();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method getEffectivePermissions - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetEffectivePermissions(): void
    {
        $effectivePermissions = [
            'profile.any.view' => 'inherited'
        ];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $permission = $this->createPermission();
        $this->setEntityId($permission, self::PERMISSION_ID);
        $role->setPermissions($permission);
        $parentRole = $this->createRole(self::USER_ROLE_NAME_GUEST);
        $role->setParentRole($parentRole);

        $result = $this->roleManager->getEffectivePermissions($role);
        $this->assertEquals($effectivePermissions, $result);
    }

    /**
     * @testCase - method updateRolePermissions - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testUpdateRolePermissions(): void
    {
        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $info = [];
        $info['permissions'] = [
            'user.manage' => 0,
            'role.manage' => 0,
            'permission.manage' => 1,
            'profile.any.view' => 0,
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->withConsecutive(
                [$this->equalTo('permission.manage')]
            )
            ->willReturn($permission);

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
                [Role::class],
            )
            ->willReturn($repositoryMock);

        $this->roleManager->updateRolePermissions($role, $info);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method rolesGetFromQueueRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolesGetFromQueueRedis(): void
    {
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $rolesRedises = [];
        $rolesRedises[] = serialize($role);

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['lRange'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('lRange')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
                $this->equalTo(0),
                $this->equalTo(-1),
            )
            ->willReturn($rolesRedises);

        $this->roleManager->setRedis($redisMock);

        $result = $this->roleManager->rolesGetFromQueueRedis();
        $this->assertEquals($roles, $result);
    }

    /**
     * @testCase - method rolesPushToQueueRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolesPushToQueueRedis(): void
    {
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['rPush', 'expire'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('rPush')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
                $this->equalTo(serialize($role)),
            );

        $redisMock->expects($this->exactly(1))
            ->method('expire')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES_TTL),
            );

        $this->roleManager->setRedis($redisMock);

        $this->roleManager->rolesPushToQueueRedis($roles);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method rolePushToQueueRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolePushToQueueRedis(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['rPush', 'expire'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('rPush')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
                $this->equalTo(serialize($role)),
            );

        $redisMock->expects($this->exactly(1))
            ->method('expire')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES_TTL),
            );

        $this->roleManager->setRedis($redisMock);

        $this->roleManager->rolePushToQueueRedis($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method roleQueueLength - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleQueueLength(): void
    {
        $len = 12;

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['lLen'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('lLen')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
            )
            ->willReturn($len);

        $this->roleManager->setRedis($redisMock);

        $result = $this->roleManager->roleQueueLength();
        $this->assertEquals($len, $result);
    }

    /**
     * @testCase - method roleSetRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleSetRedis(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['set'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('set')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_ROLE . $role->getId()),
                $this->equalTo(serialize($role)),
                $this->equalTo(['EX' => RoleFixtures::REDIS_SETS_ROLES_TTL]),
            );

        $this->roleManager->setRedis($redisMock);

        $this->roleManager->roleSetRedis($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method roleGetRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleGetRedis(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roleStr = serialize($role);

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('get')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_ROLE . self::ROLE_ID),
            )
            ->willReturn($roleStr);

        $this->roleManager->setRedis($redisMock);

        $result = $this->roleManager->roleGetRedis(self::ROLE_ID);
        $this->assertEquals($role, $result);
    }

    /**
     * @testCase - method roleGetRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleGetRedisEmptyRole(): void
    {
        $roleStr = '';

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('get')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_ROLE . self::ROLE_ID),
            )
            ->willReturn($roleStr);

        $this->roleManager->setRedis($redisMock);

        $result = $this->roleManager->roleGetRedis(self::ROLE_ID);
        $this->assertEquals(null, $result);
    }

    /**
     * @testCase - method roleCheckRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleCheckRedis(): void
    {
        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['exists'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('exists')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_ROLE . self::ROLE_ID),
            )
            ->willReturn(true);

        $this->roleManager->setRedis($redisMock);

        $result = $this->roleManager->roleCheckRedis(self::ROLE_ID);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method roleAddInSetRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleAddInSetRedis(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['zAdd'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('zAdd');

        $this->roleManager->setRedis($redisMock);

        $this->roleManager->roleAddInSetRedis($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - method rolesGetByScoreRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRolesGetByScoreRedis(): void
    {
        self::markTestSkipped(self::class . ' skipped testRolesGetByScoreRedis');
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['zRangeByScore'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('zRangeByScore');

        $this->roleManager->setRedis($redisMock);

        $result = $this->roleManager->rolesGetByScoreRedis();
        $this->assertEquals($roles, $result);
    }

    /**
     * @testCase - method roleDelRedis - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testRoleDelRedis(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);

        $redisMock = $this->getMockBuilder(Redis::class)
            ->onlyMethods(['zRem', 'del'])
            ->disableOriginalConstructor()
            ->getMock();

        $redisMock->expects($this->exactly(1))
            ->method('zRem')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_ROLE_SET),
                $this->equalTo((string)$role->getId()),
            );

        $redisMock->expects($this->exactly(1))
            ->method('del')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_ROLE . $role->getId()),
            );

        $this->roleManager->setRedis($redisMock);

        $this->roleManager->roleDelRedis($role);
        $this->assertTrue(true);
    }

    /**
     * @testCase - test Redis lRange
     *
     * @return void
     * @throws ReflectionException
     */
    public function testRedislRange(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $rolesRedises = [];
        $rolesRedises[] = serialize($role);

        $stub = $this->createStub(Redis::class);
        $stub->method('lRange')
            ->with(
                $this->equalTo(RoleFixtures::REDIS_SETS_ROLES),
                $this->equalTo(0),
                $this->equalTo(-1),
            )
            ->willReturn($rolesRedises);

        $result = $stub->lRange(RoleFixtures::REDIS_SETS_ROLES, 0, -1);
        $this->assertSame($rolesRedises, $result);
    }

    /**
     * @testCase - method getRedis - must be a success
     *
     * @return void
     * @throws ReflectionException
     */
    public function testgetRedis(): void
    {
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $this->roleManager->roleDelRedis($role);
        $this->assertTrue(true);
    }
}
