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

namespace ApplicationTest\unit\Document;

use ApplicationTest\AbstractMock;
use Carbon\Carbon;
use DateTime;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class DocumentTest - Unit tests for all Documents
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package ApplicationTest\unit\Document
 */
class DocumentTest extends AbstractMock
{
    /** @var array $documentsClassNames */
    protected array $documentsClassNames;

    /** @var array $viewsClassNames */
    public array $viewsClassNames = [
//        'Application\Document\Log',
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
     * @testCase - method testDocuments - must be a success
     * Test all Documents
     *
     * @return void
     * @throws ORMException
     */
    public function testDocuments(): void
    {
        $this->prepareDbMongoIntegration();
        $this->documentsClassNames = $this->documentManagerIntegration
            ->getConfiguration()
            ->getMetadataDriverImpl()
            ->getAllClassNames();
        foreach ($this->documentsClassNames as $className) {
            $this->handleDocument($className);
        }
    }

    /**
     * @param string $className
     */
    protected function handleDocument(string $className): void
    {
        /** @var ClassMetadata $metaData */
        $metaData = $this->documentManagerIntegration->getClassMetadata($className);
        $documentInfo = $this->getDocumentInfo($metaData);
        foreach ($documentInfo as $fieldName => $fieldType) {
            $setter = 'set' . ucfirst($fieldName);
            $getter = 'get' . ucfirst($fieldName);
            $mockValue = $this->getFieldValueMock($fieldType);
            if ($mockValue === null) {
                printf("Wrong field type. Document: %s, Fieldname: %s \n\r", $className, $fieldName);
            } else {
                $document = new $className();
                if (($metaData->isIdentifier($fieldName) && ! method_exists($document, $setter)) ||
                    in_array($className, $this->viewsClassNames)) {
                    $this->assertSame(
                        $document->{$getter}(),
                        null,
                        sprintf(' Document: %s, Fieldname: %s', $className, $fieldName)
                    );
                } else {
                    $document->{$setter}($mockValue);
                    $value = $document->{$getter}();
                    $this->assertSame(
                        $mockValue,
                        $value,
                        sprintf(' Document: %s, Fieldname: %s', $className, $fieldName)
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
    protected function getDocumentInfo(ClassMetadata $metaData): array
    {
        $fieldNames = $metaData->getFieldNames();
        $documentInfo = [];
        foreach ($fieldNames as $fieldName) {
            $documentInfo[$fieldName] = $metaData->getTypeOfField($fieldName);
        }

        return $documentInfo;
    }

    /**
     * @param string $fieldType
     *
     * @return bool|DateTime|float|int|string|array
     */
    private function getFieldValueMock(string $fieldType): bool|DateTime|float|int|string|array
    {
        switch ($fieldType) {
            case 'id':
                $value = self::LOG_ID;
                break;
            case 'collection':
                $value = ['currentUserId=' . self::USER_ID];
                break;
            case 'integer':
            case 'int':
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
            case 'date':
                $value = Carbon::now();
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
}
