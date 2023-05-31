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

namespace ApplicationTest\unit\Service;

use Application\Service\RbacAssertionManager;
use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\User;
use Laminas\Permissions\Rbac\Rbac;

/**
 * Class RbacAssertionManagerTest - Unit tests for RbacAssertionManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Service
 */
class RbacAssertionManagerTest extends AbstractMock
{
    /** @var RbacAssertionManager $rbacAssertionManager */
    public RbacAssertionManager $rbacAssertionManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->rbacAssertionManager = $this->serviceManager->get(RbacAssertionManager::class);
    }

    /**
     * @testCase - method assert - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testAssert(): void
    {
        $user = $this->createUser();
        $this->setEntityId($user, self::USER_ID);
        $identity = null;

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
                [User::class]
            )
            ->willReturn($repositoryMock);

        $rbac = new Rbac();
        $permission = 'profile.own.view';
        $params = [
            'user' => $user
        ];
        $result = $this->rbacAssertionManager->assert($rbac, $permission, $params);
        $this->assertTrue($result);
    }
}
