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

namespace ApplicationTest\unit\Command;

use Application\Command\CommandDbLoadingFixtures;
use ApplicationTest\AbstractMock;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Exception;
use Fixtures\RoleFixtures;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommandDbLoadingFixturesTest - Unit tests for CommandDbLoadingFixtures
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Command
 */
class CommandDbLoadingFixturesTest extends AbstractMock
{
    /** @var Application $app */
    private Application $app;

    /** @var ApplicationTester $tester */
    private ApplicationTester $tester;

    /** @var CommandDbLoadingFixtures $command */
    private CommandDbLoadingFixtures $command;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->serviceManager->get(CommandDbLoadingFixtures::class);
        $this->app = new Application();
        $this->app->add($this->command);
        $this->app->setAutoExit(false);
        $this->tester = new ApplicationTester($this->app);
    }

    /**
     * @testCase - method execute - must be a success
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteSuccess(): void
    {
        $executor = $this->getMockBuilder(ORMExecutor::class)
            ->onlyMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMock();

        $executor->expects($this->once())
            ->method('execute')
            ->willReturn(0);

        $this->command->setExecutor($executor);

        $this->tester->run([
            'command' => 'app:db:loading-fixtures',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbLoadingFixtures app:db:loading-fixtures', $output);
        $statusCode = $this->tester->getStatusCode();
        $this->assertEquals(\Symfony\Component\Console\Command\Command::SUCCESS, $statusCode);
    }

    /**
     * @testCase - method execute - must be a Exception - class not exists - Failure
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteExceptionFailure(): void
    {
        $fixtures = [
            RoleFixtures::class,
            'RoleFixturesTest',
        ];
        $this->command->setFixtures($fixtures);

        $this->tester->run([
            'command' => 'app:db:loading-fixtures',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbLoadingFixtures app:db:loading-fixtures', $output);
        $statusCode = $this->tester->getStatusCode();
        $this->assertEquals(\Symfony\Component\Console\Command\Command::FAILURE, $statusCode);
    }
}
