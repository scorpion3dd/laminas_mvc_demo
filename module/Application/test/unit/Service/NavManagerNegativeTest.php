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
 * Class NavManagerNegativeTest - Unit negative tests for NavManager
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Service
 */
class NavManagerNegativeTest extends AbstractMock
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
     * @testCase - method getMenuItems - Exception
     * else - Exception 1
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGetMenuItemsElseException1(): void
    {
        $this->setAuth();

        $rbacManagerMock = $this->getMockBuilder(RbacManager::class)
            ->onlyMethods(['isGranted'])
            ->disableOriginalConstructor()
            ->getMock();

        $rbacManagerMock->expects($this->exactly(4))
            ->method('isGranted')
            ->willThrowException(new Exception());

        $this->navManager->setRbacManager($rbacManagerMock);

        $this->navManager->getMenuItems();
        $this->assertTrue(true);
    }
}
