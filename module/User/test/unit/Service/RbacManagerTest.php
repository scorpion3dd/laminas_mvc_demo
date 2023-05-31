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
use ReflectionException;
use User\Entity\Role;
use User\Entity\User;
use User\Service\AuthAdapter;
use User\Service\RbacManager;
use Laminas\Permissions\Rbac\Rbac;

/**
 * Class RbacManagerTest - Unit tests for RbacManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class RbacManagerTest extends AbstractMock
{
    /** @var RbacManager $rbacManager */
    public RbacManager $rbacManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->rbacManager = $this->serviceManager->get(RbacManager::class);
    }

    /**
     * @testCase - method isGranted - must be a success
     * empty rbac - init - empty user - empty identity - result false
     *
     * @return void
     * @throws Exception
     */
    public function testIsGrantedEmptyIdentity(): void
    {
        $this->sleep();
        $permissionName = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $params = [];
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

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

        $result = $this->rbacManager->isGranted(null, $permissionName, $params);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method isGranted - Exception
     * empty rbac - init - empty user - identity - empty user - Exception
     * There is no user with such identity
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testIsGrantedException(): void
    {
        $this->sleep();
        $permissionName = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $params = [];
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ]
            )
            ->willReturn($user, null);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

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
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->prepareSessionContainer();
        $adapter = new AuthAdapter($this->entityManager, $this->sessionContainer);
        $adapter->setEmail($identity);
        $adapter->setPassword(User::PASSWORD_ADMIN);

        $this->rbacManager->getAuthService()->setAdapter($adapter);
        $this->rbacManager->getAuthService()->authenticate();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There is no user with such identity');
        $this->expectExceptionCode(0);
        $this->rbacManager->isGranted(null, $permissionName, $params);
    }

    /**
     * @testCase - method isGranted - must be a success
     * empty rbac - init - empty user - identity - isGranted true - empty params - result true
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Exception
     */
    public function testIsGrantedEmptyParams(): void
    {
        $this->sleep();
        $permissionName = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $params = [];
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(2))
            ->method('findOneBy')
            ->withConsecutive(
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ]
            )
            ->willReturn($user);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

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
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->prepareSessionContainer();
        $adapter = new AuthAdapter($this->entityManager, $this->sessionContainer);
        $adapter->setEmail($identity);
        $adapter->setPassword(User::PASSWORD_ADMIN);

        $this->rbacManager->getAuthService()->setAdapter($adapter);
        $this->rbacManager->getAuthService()->authenticate();

        $result = $this->rbacManager->isGranted(null, $permissionName, $params);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isGranted - must be a success
     * empty rbac - init - empty user - identity - isGranted true - assert false - result false
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Exception
     */
    public function testIsGrantedAssertFalse(): void
    {
        $this->sleep();
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);
        $permissionName = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $params = [
            'user' => $user
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(3))
            ->method('findOneBy')
            ->withConsecutive(
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ]
            )
            ->willReturn($user);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission();
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(4))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->prepareSessionContainer();
        $adapter = new AuthAdapter($this->entityManager, $this->sessionContainer);
        $adapter->setEmail($identity);
        $adapter->setPassword(User::PASSWORD_ADMIN);

        $this->rbacManager->getAuthService()->setAdapter($adapter);
        $this->rbacManager->getAuthService()->authenticate();

        $result = $this->rbacManager->isGranted(null, $permissionName, $params);
        $this->assertFalse($result);
    }

    /**
     * @testCase - method isGranted - must be a success
     * empty rbac - init - empty user - identity - isGranted true - assert true - result true
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Exception
     */
    public function testIsGrantedAssertTrue(): void
    {
        $this->sleep();
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);
        $permissionName = self::USER_PERMISSION_PROFILE_OWN_VIEW;
        $params = [
            'user' => $user
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(3))
            ->method('findOneBy')
            ->withConsecutive(
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ]
            )
            ->willReturn($user);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission(self::USER_PERMISSION_PROFILE_OWN_VIEW);
        $role->setPermissions($permission);
        $roles[] = $role;

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(4))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->prepareSessionContainer();
        $adapter = new AuthAdapter($this->entityManager, $this->sessionContainer);
        $adapter->setEmail($identity);
        $adapter->setPassword(User::PASSWORD_ADMIN);

        $this->rbacManager->getAuthService()->setAdapter($adapter);
        $this->rbacManager->getAuthService()->authenticate();

        $result = $this->rbacManager->isGranted(null, $permissionName, $params);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method isGranted - must be a success
     * empty rbac - init - empty user - identity - isGranted false - parentRole isGranted true - result true
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Exception
     */
    public function testIsGrantedParentRoleIsGrantedTrue(): void
    {
        $this->sleep();
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $roles = [];
        $role = $this->createRole();
        $permission = $this->createPermission(self::USER_PERMISSION_PROFILE_OWN_VIEW);
        $role->setPermissions($permission);
        $parentRole = $this->createRole();
        $role->setParentRole($parentRole);
        $roles[] = $role;

        $user->addRole($role);

        $permissionName = self::USER_PERMISSION_PROFILE_ANY_VIEW;
        $params = [
            'user' => $user
        ];

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy', 'findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(4))
            ->method('findOneBy')
            ->withConsecutive(
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ],
                [
                    self::equalTo(['email' => $identity])
                ]
            )
            ->willReturn($user);

        $repositoryMock->expects($this->exactly(1))
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC']),
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(5))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
                [Role::class],
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->prepareSessionContainer();
        $adapter = new AuthAdapter($this->entityManager, $this->sessionContainer);
        $adapter->setEmail($identity);
        $adapter->setPassword(User::PASSWORD_ADMIN);

        $this->rbacManager->getAuthService()->setAdapter($adapter);
        $this->rbacManager->getAuthService()->authenticate();

        $result = $this->rbacManager->isGranted(null, $permissionName, $params);
        $this->assertTrue($result);
    }

    /**
     * @testCase - method init - return empty
     *
     * @return void
     */
    public function testInitReturnEmpty(): void
    {
        $rbac = new Rbac();
        $this->rbacManager->setRbac($rbac);
        $this->rbacManager->init();
        $this->assertTrue(true);
    }
}
