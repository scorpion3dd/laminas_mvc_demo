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

namespace UserTest\unit\Entity;

use ApplicationTest\AbstractMock;
use Carbon\Carbon;
use DateTime;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use UserTest\unit\Doctrine\ConnectionMock;
use UserTest\unit\Doctrine\DriverMock;

/**
 * Class EntityTest - Unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Entity
 */
class EntityTest extends AbstractMock
{
    /** @var EntityManagerInterface $em */
    protected EntityManagerInterface $em;

    /** @var array $entitiesClassNames */
    protected array $entitiesClassNames;

    /** @var array $viewsClassNames */
    public array $viewsClassNames = [
//        'User\Entity\UserRole',
    ];

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @testCase - method testEntities - must be a success
     * Test all entities
     *
     * @return void
     * @throws ORMException
     * @throws Exception
     */
    public function testEntities(): void
    {
        $this->prepareEM();
        $this->entitiesClassNames = $this->em->getConfiguration()->getMetadataDriverImpl()->getAllClassNames();
        foreach ($this->entitiesClassNames as $className) {
            $this->handleEntity($className);
        }
    }

    /**
     * @param string $className
     */
    protected function handleEntity(string $className): void
    {
//        var_dump($className);
        /** @var ClassMetadata $metaData */
        $metaData = $this->em->getClassMetadata($className);
        $entityInfo = $this->getEntityInfo($metaData);
        foreach ($entityInfo as $fieldName => $fieldType) {
//            var_dump($fieldName);
            $setter = 'set' . ucfirst($fieldName);
            $getter = 'get' . ucfirst($fieldName);
            $mockValue = $this->getFieldValueMock($fieldType);
            if ($mockValue === null) {
                printf("Wrong field type. Entity: %s, Fieldname: %s \n\r", $className, $fieldName);
            } else {
                $entity = new $className();
                if (($metaData->isIdentifier($fieldName) && ! method_exists($entity, $setter)) ||
                    in_array($className, $this->viewsClassNames)) {
                    $this->assertSame(
                        $entity->{$getter}(),
                        null,
                        sprintf(' Entity: %s, Fieldname: %s', $className, $fieldName)
                    );
                } else {
                    $entity->{$setter}($mockValue);
                    $value = $entity->{$getter}();
                    $this->assertSame(
                        $mockValue,
                        $value,
                        sprintf(' Entity: %s, Fieldname: %s', $className, $fieldName)
                    );
                }
            }
        }
    }

    /**
     * @param ClassMetadata $metaData
     *
     * @return array
     */
    protected function getEntityInfo(ClassMetadata $metaData): array
    {
        $fieldNames = $metaData->getFieldNames();
        $entityInfo = [];
        foreach ($fieldNames as $fieldName) {
            $entityInfo[$fieldName] = $metaData->getTypeOfField($fieldName);
        }

        return $entityInfo;
    }

    /**
     * @param string $fieldType
     *
     * @return bool|DateTime|float|int|string
     */
    private function getFieldValueMock(string $fieldType): bool|DateTime|float|int|string
    {
        switch ($fieldType) {
            case 'integer':
            case 'bigint':
                $value = 9;
                break;
            case 'smallint':
                $value = 1;
                break;
            case 'float':
                $value = 9.99;
                break;
            case 'text':
            case 'string':
                $value = 'Example text';
                break;
            case 'boolean':
                $value = true;
                break;
            case 'datetime':
                $value = Carbon::now();
                break;
            case 'date':
                $value = Carbon::now()->format('Y-m-d');
                break;
            case 'time':
                $value = Carbon::now()->format('H:i:s');
                break;
            case 'decimal':
                $value = '1.000';
                break;
            default:
                $value = null;
                break;
        }

        return $value;
    }

    /**
     * Prepare full EntityManager with Annotations, Metadata, fieldNames, fieldTypes
     *
     * @return void
     * @throws ORMException
     * @throws Exception
     */
    private function prepareEM(): void
    {
        $config = new Configuration();
        $config->setProxyDir(__DIR__ . '/../../Proxies');
        $config->setProxyNamespace('Doctrine\Tests\Proxies');

        $driverMock = new DriverMock();
        $conn = new ConnectionMock([], $driverMock);
        $reader = new CachedReader(new AnnotationReader(), new ArrayCache());
        AnnotationRegistry::loadAnnotationClass(Annotation::class);
        $paths = [__DIR__ . '/../../../src/Entity'];
        $metadataDriver = new AnnotationDriver($reader, $paths);
        $config->setMetadataDriverImpl($metadataDriver);

        $this->em = EntityManager::create($conn, $config);
    }
}
