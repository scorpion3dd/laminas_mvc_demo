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

namespace Console;

use Application\Document\Log;
use Application\Repository\LogsRepository;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Fixtures\AbstractFixtures;
use Fixtures\RoleFixtures;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;
use Exception;
use User\Entity\Role;
use UserTest\unit\Doctrine\ConnectionMock;
use UserTest\unit\Doctrine\DriverMock;

/**
 * Class Db
 * @package Console
 */
class Db extends AbstractFixtures
{
    public const TYPE_INTEGRATION = 'Integration';

    /** @var array $errors */
    private array $errors = [];

    /** @var EntityManager $entityManager */
    private EntityManager $entityManager;

    /** @var string $type */
    private string $type = '';

    /** @var array $dbParams */
    private array $dbParams = [];

    /**
     * Db constructor
     * @param string $type
     * @throws ORMException
     * @throws Exception
     */
    public function __construct(string $type = '')
    {
        parent::__construct(['redis']);
        $this->type = $type;
        $this->buildEntityManager();
    }

    /**
     * @param string|array $file
     * @param string $message
     *
     * @return void
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function execute(string|array $file = '', string $message = ''): void
    {
        try {
            if (count($this->errors) == 0) {
                if (is_string($file)) {
                    $this->executeDb($file);
                } elseif (is_array($file)) {
                    $this->executeFixtures($file);
                }
                echo PHP_EOL . $message . PHP_EOL;
                echo PHP_EOL;
            } else {
                echo 'Errors: ';
                foreach ($this->errors as $error) {
                    echo PHP_EOL . $error;
                }
            }
        } catch (Exception $e) {
            echo 'Error execute: ' . $this->getExceptionMessage($e);
        }
    }

    /**
     * @param string $file
     *
     * @return void
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws Exception
     */
    private function executeDb(string $file = ''): void
    {
        $fileSql = __DIR__ . "/../data/db/$file";
        if (! file_exists($fileSql)) {
            throw new Exception("$file - file not exists");
        }
        if ($file == 'dropStructure.sql') {
            $this->dropRedis();
            $this->dropMongo();
        }
        if ($file == 'dropStructureIntegration.sql') {
            $this->dropRedis(Db::TYPE_INTEGRATION);
            $this->dropMongo(Db::TYPE_INTEGRATION);
        }
        /** @var string $sql */
        $sql = file_get_contents($fileSql);
        $stmt = $this->entityManager->getConnection()->prepare($sql);
        $stmt->executeStatement();
    }

    /**
     * @param string $type
     *
     * @return void
     * @throws Exception
     */
    private function dropRedis(string $type = ''): void
    {
        $this->initRedis();
        if ($type == Db::TYPE_INTEGRATION) {
            $this->redis->del(\FixturesIntegration\RoleFixtures::REDIS_SETS_ROLES);
            $this->redis->del(\FixturesIntegration\RoleFixtures::REDIS_ROLE_SET);
        } else {
            $this->redis->del(RoleFixtures::REDIS_SETS_ROLES);
            $this->redis->del(RoleFixtures::REDIS_ROLE_SET);
        }
        $roles = $this->entityManager->getRepository(Role::class)->findBy([], ['id' => 'ASC']);
        /** @var Role $role */
        foreach ($roles as $role) {
            if ($type == Db::TYPE_INTEGRATION) {
                $this->redis->del(\FixturesIntegration\RoleFixtures::REDIS_ROLE . $role->getId());
            } else {
                $this->redis->del(RoleFixtures::REDIS_ROLE . $role->getId());
            }
        }
    }

    /**
     * @param string $type
     *
     * @return void
     * @throws MongoDBException
     */
    private function dropMongo(string $type = ''): void
    {
        $this->initMongo($type);
        if (! empty($this->dm)) {
            try {
                /** @var LogsRepository $logRepository */
                $logRepository = $this->dm->getRepository(Log::class);
                $logRepository->deleteAllLogs();
            } catch (Exception $e) {
                echo 'Error dropMongo: ' . $this->getExceptionMessage($e);
            }
        }
    }

    /**
     * @param array $classes
     *
     * @return void
     * @throws Exception
     */
    private function executeFixtures(array $classes = []): void
    {
        $loader = new Loader();
        foreach ($classes as $class) {
            echo PHP_EOL . $class;
            if (! class_exists($class)) {
                throw new Exception("$class - class not exists");
            }
            /** @var FixtureInterface $class */
            $loader->addFixture(new $class());
        }
        $executor = new ORMExecutor($this->entityManager, new ORMPurger());
        /** @phpstan-ignore-next-line */
        $executor->execute($loader->getFixtures(), append: true);
    }

    /**
     * Prepare simple EntityManager only for CRUD operations
     * without Annotations, Metadata, fieldNames, fieldTypes
     *
     * @return void
     * @throws ORMException
     */
    private function buildEntityManager(): void
    {
        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__ . "/src"],
            true,
            null,
            null,
            false
        );
        $params = require_once __DIR__ . '/../config/autoload/local.php';
        if ($this->type == Db::TYPE_INTEGRATION) {
            $params = require_once __DIR__ . '/../config/autoload_test/local.php';
        }
        if (empty($params['doctrine']['connection']['orm_default']['params'])) {
            $this->errors[] = 'dbParams - not read';
        }
        $dbParams = $params['doctrine']['connection']['orm_default']['params'];
        if (! is_array($dbParams)) {
            $this->errors[] = 'dbParams - not array';
        }
        $dbParams['driver'] = 'pdo_mysql';
        $this->entityManager = EntityManager::create($dbParams, $config);
        $this->setDbParams($dbParams);
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->getDbParams()['dbname'];
    }

    /**
     * @return array
     */
    public function getDbParams(): array
    {
        return $this->dbParams;
    }

    /**
     * @param array $dbParams
     */
    public function setDbParams(array $dbParams): void
    {
        $this->dbParams = $dbParams;
    }

    /**
     * @return EntityManager
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     */
    public function getEntityManager(): EntityManager
    {
        $this->prepareEM();

        return $this->entityManager;
    }

    /**
     * Prepare full EntityManager with Annotations, Metadata, fieldNames, fieldTypes
     *
     * @return void
     * @throws ORMException
     * @throws \Doctrine\DBAL\Exception
     */
    private function prepareEM(): void
    {
        $config = new Configuration();
        $config->setProxyDir(__DIR__ . '/data/DoctrineMongoODMModule/Proxies');
        $config->setProxyNamespace('Doctrine\Tests\Proxies');

        $driverMock = new DriverMock();
        $conn = new ConnectionMock([], $driverMock);
        $reader = new CachedReader(new AnnotationReader(), new ArrayCache());
        AnnotationRegistry::loadAnnotationClass(Annotation::class);
        $paths = [__DIR__ . '/../module/User/src/Entity'];
        $metadataDriver = new AnnotationDriver($reader, $paths);
        $config->setMetadataDriverImpl($metadataDriver);

        $this->entityManager = EntityManager::create($conn, $config);
    }
}
