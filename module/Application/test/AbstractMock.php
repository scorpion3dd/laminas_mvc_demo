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

namespace ApplicationTest;

use Application\Document\Log;
use Carbon\Carbon;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Setup;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use User\Entity\Permission;
use User\Entity\Role;
use User\Entity\User;
use User\Service\AuthManager;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Escaper\Escaper;
use Laminas\Log\Logger;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Session\Container;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

/**
 * Class AbstractMock
 * @package ApplicationTest
 */
abstract class AbstractMock extends AbstractHttpControllerTestCase
{
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';

    public const INVALID_ROUTE_URL = '/invalid/route';

    public const STATUS_CODE_200 = 200;
    public const STATUS_CODE_302 = 302;
    public const STATUS_CODE_401 = 401;
    public const STATUS_CODE_404 = 404;
    public const STATUS_CODE_500 = 500;

    public const LOG_MESSAGE = 'Exercitationem ea et iste ut. Molestiae voluptas est tempora ut at sint reprehenderit.';

    public const USER_ID = 5;
    public const USER_PASSWORD_RESET_TOKEN = '$2y$10$ElNx6J2nL56N1M2dFXSPV.RJchIoKlfPFLQB/lu8ltQ5KIm197Zwy';
    public const USER_EMAIL = 'user@example.com';
    public const USER_FULL_NAME = 'Randall Bashirian Jr.';
    public const USER_DATE_BIRTHDAY = '2023-02-01';
    public const USER_DESCRIPTION = 'Quidem et odit eveniet repellat maiores modi. '
    . 'Natus sequi omnis dolor quia. Veritatis magni commodi quia soluta. Consequatur necessitatibus illo et.';
    public const USER_ROLE_NAME_ADMINISTRATOR = 'Administrator';
    public const USER_ROLE_NAME_GUEST = 'Guest';
    public const USER_ROLE_DESCRIPTION = 'A person who manages users, roles, etc.';
    public const USER_PERMISSION_PROFILE_ANY_VIEW = 'profile.any.view';
    public const USER_PERMISSION_PROFILE_OWN_VIEW = 'profile.own.view';
    public const USER_PERMISSION_USER_MANAGE = 'user.manage';
    public const USER_PERMISSION_DESCRIPTION = 'View anyone\'s profile';

    public const PERMISSION_NAME = 'log.manage';
    public const PERMISSION_DESCRIPTION = 'View anyone\'s log';

    public const ROLE_NAME = 'QA';
    public const ROLE_DESCRIPTION = 'A person who testing users, roles, etc.';

    public const PERMISSION_ID = 1;
    public const ROLE_ID = 1;

    public const TYPE_TEST_FUNCTIONAL = 'functional';
    public const TYPE_TEST_UNIT = 'unit';

    public const HTML_START_WITH = '<!DOCTYPEhtml><htmllang="en"><head>';

    public const LOG_ID = '63d96d04477661e2140f23a1';

    /** @var string $typeTest */
    protected string $typeTest = self::TYPE_TEST_UNIT;

    /** @var bool $validation */
    protected static bool $validation = true;

    /** @var Container $sessionContainer */
    protected Container $sessionContainer;

    /** @var EntityManagerInterface|MockObject $entityManager */
    protected EntityManagerInterface|MockObject $entityManager;

    /** @var EntityManagerInterface|MockObject $entityManagerIntegration */
    protected EntityManagerInterface|MockObject $entityManagerIntegration;

    /** @var DocumentManager|MockObject $documentManager */
    protected DocumentManager|MockObject $documentManager;

    /** @var DocumentManager|MockObject $documentManagerIntegration */
    protected DocumentManager|MockObject $documentManagerIntegration;

    /** @var ServiceLocatorInterface|ServiceManager $serviceManager */
    protected ServiceLocatorInterface|ServiceManager $serviceManager;

    /**
     * Contents of the 'access_filter' config key
     * @var array $config
     */
    protected array $config;

    /** @var string $encoding */
    protected $encoding = 'UTF-8';

    /** @var Escaper|null $escaper */
    protected ?Escaper $escaper;

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];
        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../config/application.config.test.php',
            $configOverrides
        ));
        $this->serviceManager = $this->getApplication()->getServiceManager();
        $this->serviceManager->setAllowOverride(true);
        $config = $this->serviceManager->get('Config');
        $this->setConfig($config);
        $this->prepareDbMySql();

        if ($this->isFunctionalTest()) {
            parent::setUp();
        }
    }

    /**
     * @return bool
     */
    private function isFunctionalTest(): bool
    {
        return $this->getTypeTest() == self::TYPE_TEST_FUNCTIONAL;
    }

    /**
     * Get the encoding to use for escape operations
     *
     * @return string
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @return Escaper|null
     */
    public function getEscaper(): ?Escaper
    {
        if (empty($this->escaper)) {
            $this->setEscaper(new Escaper($this->getEncoding()));
        }

        return $this->escaper;
    }

    /**
     * @param Escaper|null $escaper
     *
     * @return $this
     */
    public function setEscaper(?Escaper $escaper): self
    {
        $this->escaper = $escaper;

        return $this;
    }

    /**
     * @return array
     */
    protected function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    protected function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return void
     */
    protected function setConfigToService(): void
    {
        $this->serviceManager->setService('Config', $this->config);
    }

    /**
     * @param int $seconds
     *
     * @return void
     */
    protected function sleep(int $seconds = 1): void
    {
        sleep($seconds);
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getConfigFromService(): void
    {
        $this->config = $this->serviceManager->get('Config');
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function prepareSessionContainer(): void
    {
        $this->sessionContainer = $this->serviceManager->get('Demo_Auth');
    }

    /**
     * @return void
     */
    private function prepareDbMySql(): void
    {
        $this->entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->onlyMethods([
                'getConnection', 'getRepository', 'persist', 'flush', 'remove', 'beginTransaction', 'commit', 'rollback'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityManager->expects($this->any())
            ->method('persist')
            ->willReturn(null);
        $this->entityManager->expects($this->any())
            ->method('flush')
            ->willReturn(null);
        $this->entityManager->expects($this->any())
            ->method('remove')
            ->willReturn(null);
        $this->serviceManager->setService(
            'doctrine.entitymanager.orm_default',
            $this->entityManager
        );
    }

    /**
     * @return void
     * @throws ORMException
     */
    protected function prepareDbMongoIntegration(): void
    {
        if (empty($this->documentManagerIntegration)) {
            $params = require __DIR__ . '/../../../config/autoload_test/module.doctrine-mongo-odm.local.php';
            $connectConfig = isset($params['doctrine']['connection']['odm_default'])
                ? $params['doctrine']['connection']['odm_default'] : [];
            $configuration = isset($params['doctrine']['configuration']['odm_default'])
                ? $params['doctrine']['configuration']['odm_default'] : [];

            if (! file_exists($file = __DIR__ .'/../../../vendor/autoload.php')) {
                throw new RuntimeException('Install dependencies to run this script.');
            }
            $loader = require $file;
            $loader->add('Documents', __DIR__);
            /** @phpstan-ignore-next-line */
            AnnotationRegistry::registerAutoloadNamespace('Doctrine\ODM\MongoDB\Mapping\Annotations');

            $configDm = new Configuration();
            $configDm->setProxyDir($configuration['proxy_dir']);
            $configDm->setProxyNamespace($configuration['proxy_namespace']);
            $configDm->setHydratorDir($configuration['hydrator_dir']);
            $configDm->setHydratorNamespace($configuration['hydrator_namespace']);
            $configDm->setDefaultDB($connectConfig['dbname']);
            $configDm->setMetadataDriverImpl(AnnotationDriver::create(__DIR__ . '/../src/Document'));

            $this->documentManagerIntegration = DocumentManager::create(null, $configDm);
        }
    }

    /**
     * Example:  mongodb://localhost:27017
     * @param array $connectConfig
     *
     * @return string
     */
    protected function createMongoDbConnectionString(array $connectConfig): string
    {
        return 'mongodb://' . $connectConfig['server'] . ':' . $connectConfig['port'];
    }

    /**
     * Prepare simple EntityManager only for CRUD operations
     * without Annotations, Metadata, fieldNames, fieldTypes
     *
     * @return void
     * @throws ORMException
     */
    protected function prepareDbMySqlIntegration(): void
    {
        if (empty($this->entityManagerIntegration)) {
            $config = Setup::createAnnotationMetadataConfiguration(
                [__DIR__ . "/../src/Entity"],
                true,
                null,
                null,
                false
            );
            $params = require __DIR__ . '/../../../config/autoload_test/local.php';
            $dbParams = $params['doctrine']['connection']['orm_default']['params'];
            $dbParams['driver'] = 'pdo_mysql';
            $this->entityManagerIntegration = EntityManager::create($dbParams, $config);
        }
    }

    /**
     * @return void
     */
    protected function prepareDbMongo(): void
    {
        $this->documentManager = $this
            ->getMockBuilder(EntityManager::class)
            ->onlyMethods([
                'getConnection', 'getRepository', 'persist', 'flush', 'remove', 'beginTransaction', 'commit', 'rollback'
            ])
            ->disableOriginalConstructor()
            ->getMock();
        $this->documentManager->expects($this->any())
            ->method('persist')
            ->willReturn(null);
        $this->documentManager->expects($this->any())
            ->method('flush')
            ->willReturn(null);
        $this->documentManager->expects($this->any())
            ->method('remove')
            ->willReturn(null);
        $this->serviceManager->setService(
            'doctrine.documentmanager.odm_default',
            $this->documentManager
        );
    }

    /**
     * @return string
     */
    public function getTypeTest(): string
    {
        return $this->typeTest;
    }

    /**
     * @param string $typeTest
     */
    public function setTypeTest(string $typeTest): void
    {
        $this->typeTest = $typeTest;
    }

    /**
     * @see https://github.com/xvoland/html-validate
     * @param string $html
     * @param bool $cond
     * @param string $output
     *
     * @return bool
     * @throws Exception
     */
    protected function assertHTML(string $html, bool $cond = true, string $output = 'text'): bool
    {
        if (empty($html)) {
            self::assertEmpty($html, self::isEmpty());
        }
        if (! is_string($html)) {
            throw new Exception('string', 1);
        }
        $_output = ['xhtml', 'html', 'xml', 'json', 'text'];
        if (! is_string($output) || ! in_array($output, $_output)) {
            throw new Exception('string - text/xhtml/html/xml/json', 2);
        }

        $curlOpt = [
            CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
            CURLOPT_URL            => 'https://html5.validator.nu/',
            CURLOPT_PORT           => null,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => ['out'     => $output, 'content' => $this->makeHTMLBody($html)]
        ];
        $curl = curl_init();
        curl_setopt_array($curl, $curlOpt);
        if (! $response = curl_exec($curl)) {
            if (self::$validation) {
                $this->echo(sprintf('Can\'t check validation. cURL returning error %s', trigger_error(curl_error($curl))));
                self::$validation = false;
            }
        }
        curl_close($curl);
        if ($response !== false
            && (stripos($response, 'Error') !== false
            || stripos($response, 'Warning') !== false)
        ) {
            if ($cond) {
                self::fail($response);
            }
        }

        return true;
    }

    /**
     * @param string $isHTML
     *
     * @return string
     */
    private function makeHTMLBody(string $isHTML): string
    {
        if (stripos($isHTML, 'html>') === false) {
            return '<!DOCTYPE html><html><head><meta charset=utf-8 /><title></title></head><body>'.$isHTML.'</body></html>';
        } else {
            return $isHTML;
        }
    }

    /**
     * @param string $text
     * @param bool $cond
     */
    private function echo(string $text, bool $cond = true): void
    {
        if ($cond) {
            echo "\n" . $text . "\n";
        }
    }

    /**
     * @param string $html
     *
     * @return string
     */
    protected function trim(string $html): string
    {
        return str_replace([" ", "\r\n", "\r", "\n"], '', $html);
    }

    /**
     * @param $entity
     * @param $value
     *
     * @return void
     * @throws ReflectionException
     */
    public function setEntityId($entity, $value): void
    {
        $class = new ReflectionClass($entity);
        $property = $class->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, $value);
    }

    /**
     * @return Log
     */
    protected function createLog(): Log
    {
        $log = new Log();
        $log->setMessage(self::LOG_MESSAGE);
        $log->setExtra(['currentUserId=' . self::USER_ID]);
        $log->setTimestamp(Carbon::now());
        $log->setPriority(Logger::ALERT);
        $priorityList = Log::getPriorities();
        $log->setPriorityName($priorityList[Logger::ALERT]);

        return $log;
    }

    /**
     * @return User
     */
    protected function createUser(): User
    {
        $user = new User();
        $user->setEmail(self::USER_EMAIL);
        $user->setFullName(self::USER_FULL_NAME);
        $user->setDescription(self::USER_DESCRIPTION);
        $bcrypt = new Bcrypt();
        $passwordHash = $bcrypt->create(User::PASSWORD_ADMIN);
        $user->setPassword($passwordHash);
        $user->setStatus(User::STATUS_ACTIVE_ID);
        $user->setAccess(User::ACCESS_NO_ID);
        $user->setGender(User::GENDER_MALE_ID);
        $user->setDateBirthday(Carbon::now());
        $user->setDateCreated(Carbon::now());
        $user->setDateUpdated(Carbon::now());
        $role = $this->createRole();
        $user->addRole($role);

        return $user;
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return Role
     */
    protected function createRole(
        string $name = self::USER_ROLE_NAME_ADMINISTRATOR,
        string $description = self::USER_ROLE_DESCRIPTION
    ): Role {
        $role = new Role();
        $role->setName($name);
        $role->setDescription($description);
        $role->setDateCreated(Carbon::now());
        $role->setPermissions($this->createPermission());

        return $role;
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return Permission
     */
    protected function createPermission(
        string $name = self::USER_PERMISSION_PROFILE_ANY_VIEW,
        string $description = self::USER_PERMISSION_DESCRIPTION
    ): Permission {
        $permission = new Permission();
        $permission->setName($name);
        $permission->setDescription($description);
        $permission->setDateCreated(Carbon::now());

        return $permission;
    }

    /**
     * @return Query|QueryBuilder
     */
    protected function createQuery(): Query|QueryBuilder
    {
        return $this->entityManager->createQueryBuilder();
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function setAuth(): void
    {
        $this->prepareSessionContainer();
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->member = 'session';
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->user_id = self::USER_ID;
        /** @phpstan-ignore-next-line */
        $this->sessionContainer->session = User::EMAIL_ADMIN;
    }

    /**
     * @param string $controllerName
     * @param string $actionName
     * @param int $result
     *
     * @return void
     */
    protected function setAuthMock(string $controllerName, string $actionName, int $result): void
    {
        $authManagerMock = $this->getMockBuilder(AuthManager::class)
            ->onlyMethods(['filterAccess'])
            ->disableOriginalConstructor()
            ->getMock();

        $authManagerMock->expects(self::once())
            ->method('filterAccess')
            ->with($controllerName, $actionName)
            ->willReturn($result);

        $this->serviceManager->setService(AuthManager::class, $authManagerMock);
    }

    /**
     * @return void
     */
    protected function setEnvTest(): void
    {
        putenv('APPLICATION_ENV=TEST');
    }

    /**
     * @param string $text
     *
     * @return string
     */
    protected function escapeHtml(string $text): string
    {
        return $this->getEscaper()->escapeHtmlAttr($text);
    }

    /**
     * @param int $count
     *
     * @return string
     */
    protected function getLongRedirectUrl(int $count = 50): string
    {
        $redirectUrl = 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq';
        for ($i = 1; $i <= $count; $i++) {
            $redirectUrl .= 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';
        }

        return $redirectUrl;
    }

    /**
     * @param string $email
     *
     * @return void
     * @throws ORMException
     */
    protected function setPasswordResetTokenToUser(string $email = User::EMAIL_ADMIN): void
    {
        $this->prepareDbMySqlIntegration();
        /** @var User|null $user */
        $user = $this->entityManagerIntegration->getRepository(User::class)->findOneBy(['email' => $email]);
        if (! empty($user)) {
            $bcrypt = new Bcrypt();
            $tokenHash = $bcrypt->create(self::USER_PASSWORD_RESET_TOKEN);
            $user->setStatus(User::STATUS_ACTIVE_ID);
            $user->setPasswordResetToken($tokenHash);
            $user->setPasswordResetTokenCreationDate(Carbon::now());
            $this->entityManagerIntegration->persist($user);
            $this->entityManagerIntegration->flush();
        }
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return void
     * @throws ORMException
     */
    protected function setPasswordToUser(string $email = User::EMAIL_ADMIN, string $password = User::PASSWORD_ADMIN): void
    {
        $this->prepareDbMySqlIntegration();
        /** @var User|null $user */
        $user = $this->entityManagerIntegration->getRepository(User::class)->findOneBy(['email' => $email]);
        if (! empty($user)) {
            $bcrypt = new Bcrypt();
            $tokenHash = $bcrypt->create($password);
            $user->setStatus(User::STATUS_ACTIVE_ID);
            $user->setPassword($tokenHash);
            $this->entityManagerIntegration->persist($user);
            $this->entityManagerIntegration->flush();
        }
    }

    /**
     * @param string $name
     *
     * @return Permission|null
     * @throws ORMException
     */
    protected function getPermission(string $name = self::USER_PERMISSION_PROFILE_OWN_VIEW): ?Permission
    {
        $this->prepareDbMySqlIntegration();

        return $this->entityManagerIntegration->getRepository(Permission::class)->findOneBy(['name' => $name]);
    }

    /**
     * @param string $name
     *
     * @return Role|null
     * @throws ORMException
     */
    protected function getRole(string $name = self::USER_ROLE_NAME_GUEST): ?Role
    {
        $this->prepareDbMySqlIntegration();

        return $this->entityManagerIntegration->getRepository(Role::class)->findOneBy(['name' => $name]);
    }
}
