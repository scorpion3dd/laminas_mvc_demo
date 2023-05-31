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

use Application\Command\CommandDbMigrations;
use ApplicationTest\AbstractMock;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommandDbMigrationsTest - Unit tests for CommandDbMigrations
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Command
 */
class CommandDbMigrationsTest extends AbstractMock
{
    /** @var Application $app */
    private Application $app;

    /** @var ApplicationTester $tester */
    private ApplicationTester $tester;

    /** @var CommandDbMigrations $command */
    private CommandDbMigrations $command;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->serviceManager->get(CommandDbMigrations::class);
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
        $cli = $this->getMockBuilder(Application::class)
            ->onlyMethods(['run'])
            ->disableOriginalConstructor()
            ->getMock();

        $input = new ArrayInput([
            'command' => 'migrations:migrate',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $cli->expects($this->exactly(1))
            ->method('run')
            ->with($this->equalTo($input))
            ->willReturn(0);

        $this->command->setCli($cli);
        $this->command->setPath('/../../../../config/autoload_test/local.php');
        $this->tester->run([
            'command' => 'app:db:migrations-migrate-integration',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbMigrations app:db:migrations-migrate-integration', $output);
        $statusCode = $this->tester->getStatusCode();
        $this->assertEquals(\Symfony\Component\Console\Command\Command::SUCCESS, $statusCode);
    }

    /**
     * @testCase - method execute - must be a success
     * path - autoload_test
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteSuccess2(): void
    {
        $cli = $this->getMockBuilder(Application::class)
            ->onlyMethods(['run'])
            ->disableOriginalConstructor()
            ->getMock();

        $input = new ArrayInput([
            'command' => 'migrations:migrate',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $cli->expects($this->exactly(1))
            ->method('run')
            ->with($this->equalTo($input))
            ->willReturn(0);

        $this->command->setAppEnv('');
        $this->command->setCli($cli);
        $this->tester->run([
            'command' => 'app:db:migrations-migrate-integration',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbMigrations app:db:migrations-migrate-integration', $output);
        $statusCode = $this->tester->getStatusCode();
        $this->assertEquals(\Symfony\Component\Console\Command\Command::SUCCESS, $statusCode);
    }

    /**
     * @testCase - method execute - must be a success
     * path - autoload
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteSuccess3(): void
    {
        $cli = $this->getMockBuilder(Application::class)
            ->onlyMethods(['run'])
            ->disableOriginalConstructor()
            ->getMock();

        $input = new ArrayInput([
            'command' => 'migrations:migrate',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $cli->expects($this->exactly(1))
            ->method('run')
            ->with($this->equalTo($input))
            ->willReturn(0);

        $this->command->setAppEnv('unitTests');
        $this->command->setCli($cli);
        $this->tester->run([
            'command' => 'app:db:migrations-migrate-integration',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbMigrations app:db:migrations-migrate-integration', $output);
        $statusCode = $this->tester->getStatusCode();
        $this->assertEquals(\Symfony\Component\Console\Command\Command::SUCCESS, $statusCode);
    }

    /**
     * @testCase - method execute - must be a Exception - Path not found - Failure
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteExceptionFailure(): void
    {
        $this->command->setPath('/../../../../config/autoload_test/local123.php');
        $this->tester->run([
            'command' => 'app:db:migrations-migrate-integration',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbMigrations app:db:migrations-migrate-integration', $output);
        $statusCode = $this->tester->getStatusCode();
        $this->assertEquals(\Symfony\Component\Console\Command\Command::FAILURE, $statusCode);
    }
}
