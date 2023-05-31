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
use User\Service\AuthManager;
use Laminas\Authentication\Result;

/**
 * Class AuthManagerTest - Unit tests for AuthManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class AuthManagerTest extends AbstractMock
{
    /** @var AuthManager $authManager */
    public AuthManager $authManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - method login - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testLogin(): void
    {
        $identity = self::USER_EMAIL;
        $messages = ['Authenticated successfully.'];
        $resultAuthenticate = new Result(Result::SUCCESS, $identity, $messages);
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $email = self::USER_EMAIL;
        $password = User::PASSWORD_ADMIN;
        $rememberMe = 1;

        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $result = $this->authManager->login($email, $password, $rememberMe);
        $this->assertEquals($resultAuthenticate, $result);
    }

    /**
     * @testCase - method login - Exception
     * Already logged in
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testLoginException(): void
    {
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();
        $email = self::USER_EMAIL;
        $password = User::PASSWORD_ADMIN;
        $rememberMe = 1;
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Already logged in');
        $this->expectExceptionCode(0);
        $this->authManager->login($email, $password, $rememberMe);
    }

    /**
     * @testCase - method logout - Exception
     * The user is not logged in
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLogoutException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The user is not logged in');
        $this->expectExceptionCode(0);

        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->authManager->logout();
    }

    /**
     * @testCase - method logout - must be a success
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     * @throws Exception
     */
    public function testLogout(): void
    {
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();

        $this->authManager->logout();
        $this->assertTrue(true);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * AuthManager::AUTH_REQUIRED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testFilterAccessAuthRequired(): void
    {
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $result = $this->authManager->filterAccess('', '');
        $this->assertEquals(AuthManager::AUTH_REQUIRED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * AuthManager::ACCESS_DENIED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testFilterAccessAuthDenied(): void
    {
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();
        $result = $this->authManager->filterAccess('', '');
        $this->assertEquals(AuthManager::ACCESS_DENIED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * AuthManager::ACCESS_GRANTED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessAuthGranted(): void
    {
        $this->getConfigFromService();
        $this->config['access_filter']['options']['mode'] = AuthManager::MODE_PERMISSIVE;
        $this->setConfigToService();
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $result = $this->authManager->filterAccess('', '');
        $this->assertEquals(AuthManager::ACCESS_GRANTED, $result);
    }

    /**
     * @testCase - method filterAccess - Exception
     * Invalid access filter mode (expected either restrictive or permissive mode
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessException(): void
    {
        $this->getConfigFromService();
        $this->config['access_filter']['options']['mode'] = 'restrictive-permissive';
        $this->setConfigToService();
        $this->authManager = $this->serviceManager->get(AuthManager::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid access filter mode (expected either restrictive or permissive mode');
        $this->expectExceptionCode(0);
        $this->authManager->filterAccess('', '');
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers allow=ALLOW_STAR - AuthManager::ACCESS_GRANTED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowStar(): void
    {
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_STAR);
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::ACCESS_GRANTED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers allow=ALLOW_AT - AuthManager::AUTH_REQUIRED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowAt(): void
    {
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_AT);
        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::AUTH_REQUIRED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers authService->hasIdentity allow=ALLOW_AT - AuthManager::ACCESS_GRANTED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowAtHasIdentity(): void
    {
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_AT);

        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();

        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::ACCESS_GRANTED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers authService->hasIdentity allow=ALLOW_AT - AuthManager::ACCESS_DENIED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowAtOtherHasIdentityDenied(): void
    {
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_AT . 'view');

        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();

        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::ACCESS_DENIED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers authService->hasIdentity allow=ALLOW_AT - AuthManager::ACCESS_GRANTED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowAtOtherHasIdentityGranted(): void
    {
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_AT . User::EMAIL_ADMIN);

        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();

        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::ACCESS_GRANTED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers authService->hasIdentity allow=ACCESS_DENIED - AuthManager::ACCESS_GRANTED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowPlusOtherHasIdentityDenied(): void
    {
        $this->sleep();
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_PLUS . User::EMAIL_ADMIN);

        $this->authManager = $this->serviceManager->get(AuthManager::class);

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

        $this->authManager->getAuthService()->setAdapter($adapter);
        $this->authManager->getAuthService()->authenticate();

        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::ACCESS_DENIED, $result);
    }

    /**
     * @testCase - method filterAccess - must be a success
     * config access_filter controllers authService->hasIdentity allow=ALLOW_PLUS - AuthManager::ACCESS_GRANTED
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowPlusOtherHasIdentityGranted(): void
    {
        $this->sleep();
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, AuthManager::ALLOW_PLUS . 'profile.any.view');

        $this->authManager = $this->serviceManager->get(AuthManager::class);

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

        $this->authManager->getAuthService()->setAdapter($adapter);
        $this->authManager->getAuthService()->authenticate();

        $result = $this->authManager->filterAccess($controllerName, $actionName);
        $this->assertEquals(AuthManager::ACCESS_GRANTED, $result);
    }

    /**
     * @testCase - method filterAccess - Exception
     * config access_filter controllers authService->hasIdentity allow="=" - Exception
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testFilterAccessControllersAllowException(): void
    {
        $this->sleep();
        $controllerName = 'User';
        $actionName = 'Index';
        $this->setConfigeControllers($controllerName, $actionName, '=profile.any.view');

        $this->authManager = $this->serviceManager->get(AuthManager::class);
        $this->setAuthenticate();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unexpected value for "allow" - expected ' .
            'either "?", "@", "@identity" or "+permission"');
        $this->expectExceptionCode(0);

        $this->authManager->filterAccess($controllerName, $actionName);
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @param string $allow
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function setConfigeControllers(string $controllerName, string $actionName, string $allow): void
    {
        $this->getConfigFromService();
        $items = [];
        $items[] = [
            'actions' => [$actionName],
            'allow' => $allow
        ];
        $this->config['access_filter']['controllers'][$controllerName] = $items;
        $this->setConfigToService();
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function setAuthenticate(): void
    {
        $identity = User::EMAIL_ADMIN;
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => $identity]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $this->prepareSessionContainer();
        $adapter = new AuthAdapter($this->entityManager, $this->sessionContainer);
        $adapter->setEmail($identity);
        $adapter->setPassword(User::PASSWORD_ADMIN);

        $this->authManager->getAuthService()->setAdapter($adapter);
        $this->authManager->getAuthService()->authenticate();
    }
}
