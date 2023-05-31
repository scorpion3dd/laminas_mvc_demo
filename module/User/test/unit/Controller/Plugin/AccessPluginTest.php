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
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\Plugin\AccessPlugin;
use User\Service\RbacManager;

/**
 * Class AccessPluginTest - Unit tests for AccessPlugin
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Controller\Plugin
 */
class AccessPluginTest extends AbstractMock
{
    /** @var AccessPlugin $accessPlugin */
    protected AccessPlugin $accessPlugin;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $rbacManager = $this->serviceManager->get(RbacManager::class);
        $this->accessPlugin = new AccessPlugin($rbacManager);
    }

    /**
     * @testCase - method __invoke - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $permission = 'profile.any.view';
        $rbacManagerMock = $this->getMockBuilder(RbacManager::class)
            ->onlyMethods(['isGranted'])
            ->disableOriginalConstructor()
            ->getMock();

        $rbacManagerMock->expects($this->exactly(1))
            ->method('isGranted')
            ->with(
                $this->equalTo(null),
                $this->equalTo($permission),
                $this->equalTo([]),
            )
            ->willReturn(true);

        $this->accessPlugin->setRbacManager($rbacManagerMock);
        $result = $this->accessPlugin->__invoke($permission);
        $this->assertTrue($result);
    }
}
