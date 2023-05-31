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

namespace ApplicationTest\integration\Controller;

use Application\Controller\IndexController;
use ApplicationTest\AbstractMock;
use Doctrine\ORM\ORMException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Entity\User;

/**
 * Class IndexControllerIntegrationNegativeTest - Integration negative tests for IndexController
 * with connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are real
 *
 * @package ApplicationTest\integration\Controller
 */
class IndexControllerIntegrationNegativeTest extends AbstractMock
{
    public const MODULE_NAME = 'application';
    public const CONTROLLER_NAME = IndexController::class;
    public const CONTROLLER_CLASS = 'IndexController';
    public const ROUTE_URL = '/';
    public const ROUTE_HOME = 'home';
    public const ROUTE_APPLICATION = 'application';

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        $this->setTypeTest(self::TYPE_TEST_FUNCTIONAL);
        parent::setUp();
    }

    /**
     * @testCase - route view action test - id not valid
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionIdNotValid(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/view/' . 0, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetViewActionIdNotValid.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route view action test - User empty
     *
     * @return void
     * @throws Exception
     */
    public function testViewActionUserEmpty(): void
    {
        $this->dispatch(self::ROUTE_URL . 'application/view/' . 1000, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetViewActionIdNotValid.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User empty
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testSettingsActionUserEmpty(): void
    {
        $this->setAuth();
        $this->dispatch(self::ROUTE_URL . 'application/settings/' . 1000, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetSettingsActionUserEmpty.html'
        );
//        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User access not - redirect to not-authorized route
     * authorization one user, by userId = 3,
     * but view route /application/settings/5 other user, with userId = 5
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     */
    public function testSettingsActionRedirectToNotAauthorized(): void
    {
//        self::markTestSkipped('skiped');
        $userId = 3;
        $this->prepareSessionContainer();
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->member = 'session';
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->user_id = $userId;
        $this->prepareDbMySqlIntegration();
        /** @var User|null $user */
        $user = $this->entityManagerIntegration->getRepository(User::class)->findOneBy(['id' => $userId]);
        $response = 'error';
        if (! empty($user)) {
            /** @phpstan-ignore-next-line */
            $this->sessionContainer->session = $user->getEmail();
            $this->dispatch(self::ROUTE_URL . 'application/settings/' . self::USER_ID, self::METHOD_GET);
            $this->assertResponseStatusCode(self::STATUS_CODE_302);
            $this->assertModuleName(self::MODULE_NAME);
            $this->assertControllerName(self::CONTROLLER_NAME);
            $this->assertControllerClass(self::CONTROLLER_CLASS);
            $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
            $response = $this->getResponse()->getContent();
        }
        $expected = '';
        self::assertSame($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route settings action - User access not - Exception
     * User access not - RbacManager isGranted - Exception There is no user with such identity
     * authorization one user, by userId = 1000,
     * but view route /application/settings/5 other user, with userId = 5
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function testSettingsActionException(): void
    {
        $this->prepareSessionContainer();
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->member = 'session';
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->user_id = 1000;
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->session = 'guest1000@example.com';
        $this->dispatch(self::ROUTE_URL . 'application/settings/' . self::USER_ID, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetSettingsActionException.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - route language action - Exception Incorrect redirect URL
     *
     * @return void
     * @throws Exception
     */
    public function testLanguageActionException(): void
    {
        $_SERVER['HTTP_REFERER'] = 'application/settings';

        $this->dispatch(self::ROUTE_URL . 'application/language/ru_RU', self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_500);
        $this->assertModuleName(self::MODULE_NAME);
        $this->assertControllerName(self::CONTROLLER_NAME);
        $this->assertControllerClass(self::CONTROLLER_CLASS);
        $this->assertMatchedRouteName(self::ROUTE_APPLICATION);
        $response = $this->getResponse()->getContent();
        self::assertTrue($this->assertHTML($response, false));
        $expected = file_get_contents(
            __DIR__ . '/../data/Controller/Index/GetLanguageActionException.html'
        );
//        self::assertStringStartsWith($this->trim($expected), $this->trim($response));
    }

    /**
     * @testCase - invalid route does not crash
     *
     * @return void
     * @throws Exception
     */
    public function testInvalidRouteDoesNotCrash(): void
    {
        $this->dispatch(self::INVALID_ROUTE_URL, self::METHOD_GET);
        $this->assertResponseStatusCode(self::STATUS_CODE_404);
    }
}
