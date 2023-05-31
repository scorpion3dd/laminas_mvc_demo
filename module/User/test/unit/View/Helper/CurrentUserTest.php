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

namespace UserTest\unit\View\Helper;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\User;
use User\View\Helper\CurrentUser;
use Laminas\Authentication\AuthenticationService;

/**
 * Class CurrentUserTest - Unit tests for CurrentUser
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\View\Helper
 */
class CurrentUserTest extends AbstractMock
{
    /** @var CurrentUser $currentUser */
    protected CurrentUser $currentUser;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $logger = $this->serviceManager->get('LoggerGlobal');
        $authService = $this->serviceManager->get(AuthenticationService::class);
        $this->currentUser = new CurrentUser($this->entityManager, $authService, $logger);
    }

    /**
     * @testCase - method __invoke - must be a success
     * user not null
     *
     * @return void
     * @throws Exception
     */
    public function testInvokeUserNotNull(): void
    {
        $user = $this->createUser();
        $this->currentUser->setUser($user);
        $result = $this->currentUser->__invoke();
        $this->assertEquals($user, $result);
    }

    /**
     * @testCase - method __invoke - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $user = $this->createUser();
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn($user);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $authenticationServiceMock = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['hasIdentity', 'getIdentity'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationServiceMock->expects($this->exactly(1))
            ->method('hasIdentity')
            ->willReturn(true);

        $authenticationServiceMock->expects($this->exactly(1))
            ->method('getIdentity')
            ->willReturn(self::USER_EMAIL);

        $this->currentUser->setUser(null);
        $this->currentUser->setAuthService($authenticationServiceMock);
        $result = $this->currentUser->__invoke();
        $this->assertEquals($user, $result);
    }

    /**
     * @testCase - method __invoke - Exception
     * Not found user with such ID
     *
     * @return void
     * @throws Exception
     */
    public function testInvokeException(): void
    {
        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findOneBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneBy')
            ->with($this->equalTo(['email' => self::USER_EMAIL]))
            ->willReturn(null);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [User::class],
            )
            ->willReturn($repositoryMock);

        $authenticationServiceMock = $this->getMockBuilder(AuthenticationService::class)
            ->onlyMethods(['hasIdentity', 'getIdentity'])
            ->disableOriginalConstructor()
            ->getMock();

        $authenticationServiceMock->expects($this->exactly(1))
            ->method('hasIdentity')
            ->willReturn(true);

        $authenticationServiceMock->expects($this->exactly(1))
            ->method('getIdentity')
            ->willReturn(self::USER_EMAIL);

        $this->currentUser->setUser(null);
        $this->currentUser->setAuthService($authenticationServiceMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not found user with such ID');
        $this->expectExceptionCode(0);
        $this->currentUser->__invoke();
    }
}
