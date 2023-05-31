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

namespace Application\Repository;

use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Query\Query;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Application\Document\Log;

/**
 * This is the custom repository class for Log Document
 * @package Application\Repository
 */
class LogsRepository extends DocumentRepository
{
    /**
     * DocumentManager
     * public function createQueryBuilder($documentName = null): Query\Builder
     * Method createQueryBuilder may not return value of type Mock_QueryBuilder_d11fd6f6,
     * its declared return type is "Doctrine\ODM\MongoDB\Query\Builder"
     *
     * @return Query
     */
    public function findAllLogs(): object
    {
        // @codeCoverageIgnoreStart
        $dm = $this->getDocumentManager();
        $queryBuilder = $dm->createQueryBuilder(Log::class)->limit(20);

        return $queryBuilder->getQuery();
        // @codeCoverageIgnoreEnd
    }

    /**
     * DocumentManager
     * public function createQueryBuilder($documentName = null): Query\Builder
     * Method createQueryBuilder may not return value of type Mock_QueryBuilder_d11fd6f6,
     * its declared return type is "Doctrine\ODM\MongoDB\Query\Builder"
     *
     * @return void
     * @throws MongoDBException
     */
    public function deleteAllLogs(): void
    {
        // @codeCoverageIgnoreStart
        $dm = $this->getDocumentManager();
        $dm->createQueryBuilder(Log::class)
            ->remove()
            ->getQuery()
            ->execute();
        // @codeCoverageIgnoreEnd
    }
}
