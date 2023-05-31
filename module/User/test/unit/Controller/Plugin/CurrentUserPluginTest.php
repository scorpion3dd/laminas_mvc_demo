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

namespace UserTest\unit\Controller\Plugin;

use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\Plugin\CurrentUserPlugin;
use User\Entity\User;
use Laminas\Authentication\AuthenticationService;

/**
 * Class CurrentUserPluginTest - Unit tests for CurrentUserPlugin
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller\Plugin
 */
class CurrentUserPluginTest extends AbstractMock
{
    /** @var CurrentUserPlugin $currentUserPlugin */
    protected CurrentUserPlugin $currentUserPlugin;

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
        $this->currentUserPlugin = new CurrentUserPlugin($this->entityManager, $authService, $logger);
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
        $this->currentUserPlugin->setUser($user);
        $result = $this->currentUserPlugin->__invoke();
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

        $this->currentUserPlugin->setUser(null);
        $this->currentUserPlugin->setAuthService($authenticationServiceMock);
        $result = $this->currentUserPlugin->__invoke();
        $this->assertEquals($user, $result);
    }

    /**
     * @testCase - method __invoke - Exception
     * Not found user with such email
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

        $this->currentUserPlugin->setUser(null);
        $this->currentUserPlugin->setAuthService($authenticationServiceMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not found user with such email');
        $this->expectExceptionCode(0);
        $this->currentUserPlugin->__invoke();
    }
}
