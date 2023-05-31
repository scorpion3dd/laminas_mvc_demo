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

namespace ApplicationTest\unit\Service;

use Application\Service\NavManager;
use ApplicationTest\AbstractMock;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Service\RbacManager;

/**
 * Class NavManagerTest - Unit tests for NavManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Service
 */
class NavManagerTest extends AbstractMock
{
    /** @var NavManager $navManager */
    public NavManager $navManager;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->navManager = $this->serviceManager->get(NavManager::class);
    }

    /**
     * @testCase - method getMenuItems - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testGetMenuItems(): void
    {
        $expected = include(
            __DIR__ . '/../data/Service/NavManager/GetMenuItems.php'
        );
        $result = $this->navManager->getMenuItems();
        $this->assertEquals($expected, $result);
    }

    /**
     * @testCase - method getMenuItems - must be a success
     * else
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetMenuItemsElse(): void
    {
        $this->setAuth();

        $rbacManagerMock = $this->getMockBuilder(RbacManager::class)
            ->onlyMethods(['isGranted'])
            ->disableOriginalConstructor()
            ->getMock();

        $rbacManagerMock->expects($this->exactly(4))
            ->method('isGranted')
            ->withConsecutive(
                [
                    $this->equalTo(null),
                    $this->equalTo(NavManager::PERMISSION_USER_MANAGE)
                ],
                [
                    $this->equalTo(null),
                    $this->equalTo(NavManager::PERMISSION_MANAGE)
                ],
                [
                    $this->equalTo(null),
                    $this->equalTo(NavManager::PERMISSION_ROLE_MANAGE)
                ],
                [
                    $this->equalTo(null),
                    $this->equalTo(NavManager::PERMISSION_USER_MANAGE)
                ],
            )
            ->willReturn(true);

        $this->navManager->setRbacManager($rbacManagerMock);

        $expected = include(
            __DIR__ . '/../data/Service/NavManager/GetMenuItemsElse.php'
        );
        $result = $this->navManager->getMenuItems();
        $this->assertEquals($expected, $result);
    }
}
