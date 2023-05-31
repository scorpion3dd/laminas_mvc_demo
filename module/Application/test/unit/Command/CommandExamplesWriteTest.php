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

use Application\Command\CommandExamplesWrite;
use ApplicationTest\AbstractMock;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommandExamplesWriteTest - Unit tests for CommandExamplesWrite
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Command
 */
class CommandExamplesWriteTest extends AbstractMock
{
    /** @var Application $app */
    private Application $app;

    /** @var ApplicationTester $tester */
    private ApplicationTester $tester;

    /** @var CommandExamplesWrite $command */
    private CommandExamplesWrite $command;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->serviceManager->get(CommandExamplesWrite::class);
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
    public function testExecute(): void
    {
        $this->tester->run([
            'command' => 'app:examples-write',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $this->tester->assertCommandIsSuccessful();
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('! [NOTE] CommandExamplesWrite app:examples-write - started', $output);
    }
}
