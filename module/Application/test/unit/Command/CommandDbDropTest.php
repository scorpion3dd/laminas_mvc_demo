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

use Application\Command\AbstractCommand;
use Application\Command\CommandDbDrop;
use Application\Command\Db;
use ApplicationTest\AbstractMock;
use Doctrine\ORM\EntityRepository;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;
use User\Entity\Role;

/**
 * Class CommandDbDropTest - Unit tests for CommandDbDrop
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Command
 */
class CommandDbDropTest extends AbstractMock
{
    /** @var Application $app */
    private Application $app;

    /** @var ApplicationTester $tester */
    private ApplicationTester $tester;

    /** @var CommandDbDrop $command */
    private CommandDbDrop $command;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->command = $this->serviceManager->get(CommandDbDrop::class);
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
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);


        $db = $this->getMockBuilder(Db::class)
            ->onlyMethods(['dropMySql', 'getMySqlDbName'])
            ->disableOriginalConstructor()
            ->getMock();

        $db->expects($this->once())
            ->method('dropMySql');

        $dbName = 'laminas_mvc';
        $db->expects($this->once())
            ->method('getMySqlDbName')
            ->willReturn($dbName);

        $this->command->setDb($db);

        $this->tester->run([
            'command' => 'app:db:drop',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $this->tester->assertCommandIsSuccessful();
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbDrop app:db:drop', $output);
        $this->assertStringContainsString('ALL EXECUTED SUCCESS', $output);
    }

    /**
     * @testCase - method execute - must be a success
     * appEnv = TYPE_INTEGRATION
     *
     * @return void
     * @throws Exception
     */
    public function testExecute2(): void
    {
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);


        $db = $this->getMockBuilder(Db::class)
            ->onlyMethods(['dropMySql', 'getMySqlDbName'])
            ->disableOriginalConstructor()
            ->getMock();

        $db->expects($this->once())
            ->method('dropMySql');

        $dbName = 'laminas_mvc';
        $db->expects($this->once())
            ->method('getMySqlDbName')
            ->willReturn($dbName);

        $this->command->setDb($db);

        putenv('APP_ENV=' . $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = AbstractCommand::TYPE_INTEGRATION);
        $this->tester->run([
            'command' => 'app:db:drop',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $this->tester->assertCommandIsSuccessful();
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbDrop app:db:drop', $output);
        $this->assertStringContainsString('ALL EXECUTED SUCCESS', $output);
    }

    /**
     * @testCase - method execute - must be a Exception - Mongo DB collection Log NOT dropped
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteException1(): void
    {
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);


        $db = $this->getMockBuilder(Db::class)
            ->onlyMethods(['dropMongo', 'dropMySql', 'getMySqlDbName'])
            ->disableOriginalConstructor()
            ->getMock();

        $db->expects($this->once())
            ->method('dropMongo')
            ->willThrowException(new Exception('Error'));

        $db->expects($this->once())
            ->method('dropMySql');

        $dbName = 'laminas_mvc';
        $db->expects($this->once())
            ->method('getMySqlDbName')
            ->willReturn($dbName);

        $this->command->setDb($db);

        $this->tester->run([
            'command' => 'app:db:drop',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $this->tester->assertCommandIsSuccessful();
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbDrop app:db:drop', $output);
        $this->assertStringContainsString('EXECUTED WITH ERRORS', $output);
    }

    /**
     * @testCase - method execute - must be a Exception - MySql DB NOT dropped
     *
     * @return void
     * @throws Exception
     */
    public function testExecuteException2(): void
    {
        $roles = [];
        $role = $this->createRole();
        $this->setEntityId($role, self::ROLE_ID);
        $roles[] = $role;

        $repositoryMock = $this->getMockBuilder(EntityRepository::class)
            ->onlyMethods(['findBy'])
            ->disableOriginalConstructor()
            ->getMock();

        $repositoryMock->expects($this->once())
            ->method('findBy')
            ->with(
                $this->equalTo([]),
                $this->equalTo(['id' => 'ASC'])
            )
            ->willReturn($roles);

        $this->entityManager->expects($this->exactly(1))
            ->method('getRepository')
            ->withConsecutive(
                [Role::class]
            )
            ->willReturn($repositoryMock);


        $db = $this->getMockBuilder(Db::class)
            ->onlyMethods(['dropMySql', 'getMySqlDbName'])
            ->disableOriginalConstructor()
            ->getMock();

        $db->expects($this->once())
            ->method('dropMySql')
            ->willThrowException(new Exception('Error'));

        $dbName = 'laminas_mvc';
        $db->expects($this->once())
            ->method('getMySqlDbName')
            ->willReturn($dbName);

        $this->command->setDb($db);

        $this->tester->run([
            'command' => 'app:db:drop',
            '--no-interaction' => '',
            '--ansi' => '',
        ]);
        $this->tester->assertCommandIsSuccessful();
        $output = $this->tester->getDisplay();
        $this->assertStringContainsString('CommandDbDrop app:db:drop', $output);
        $this->assertStringContainsString('EXECUTED WITH ERRORS', $output);
    }
}
