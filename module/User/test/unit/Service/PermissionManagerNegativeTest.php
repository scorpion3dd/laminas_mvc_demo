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
use User\Service\PermissionManager;

/**
 * Class PermissionManagerNegativeTest - Unit negative tests for PermissionManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Service
 */
class PermissionManagerNegativeTest extends AbstractMock
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
     * @testCase - method addPermission - Exception
     * Permission with such name already exists
     *
     * @return void
     * @throws Exception
     */
    public function testAddPermissionException(): void
    {
        $data = [
            'name' => self::USER_PERMISSION_PROFILE_ANY_VIEW
        ];
        $existingPermission = $this->createPermission();

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingPermission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Permission with such name already exists');
        $this->expectExceptionCode(0);
        $this->permissionManager->addPermission($data);
    }

    /**
     * @testCase - method updatePermission - Exception
     * Another permission with such name already exists
     *
     * @return void
     * @throws Exception
     */
    public function testUpdatePermissionException(): void
    {
        $data = [
            'name' => self::USER_PERMISSION_PROFILE_ANY_VIEW
        ];
        $existingPermission = $this->createPermission();
        $permission = $this->createPermission(self::USER_PERMISSION_PROFILE_OWN_VIEW);

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->addMethods(['findOneByName'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->exactly(1))
            ->method('findOneByName')
            ->with(
                $this->equalTo($data['name']),
            )
            ->willReturn($existingPermission);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Permission::class],
            )
            ->willReturn($repositoryMock);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Another permission with such name already exists');
        $this->expectExceptionCode(0);
        $this->permissionManager->updatePermission($permission, $data);
    }
}
