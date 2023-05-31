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

namespace UserTest\unit\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\Keywords\SQLServerKeywords;

/**
 * Class DatabasePlatformMock - For unit tests for all Entities
 * without connecting to external services, such as databases, message brokers, etc.
 * all calls to any external services are mute
 *
 * @package UserTest\unit\Doctrine
 */
class DatabasePlatformMock extends AbstractPlatform
{
    private $_sequenceNextValSql = "";
    private $_prefersIdentityColumns = true;
    private $_prefersSequences = false;

    /** @override */
    public function getNativeDeclaration(array $field)
    {
    }

    /** @override */
    public function getPortableDeclaration(array $field)
    {
    }

    /** @override */
    public function prefersIdentityColumns()
    {
        return $this->_prefersIdentityColumns;
    }

    /** @override */
    public function prefersSequences()
    {
        return $this->_prefersSequences;
    }

    /** @override */
    public function getSequenceNextValSQL($sequenceName)
    {
        return $this->_sequenceNextValSql;
    }

    /** @override */
    public function getBooleanTypeDeclarationSQL(array $field)
    {
    }

    /** @override */
    public function getIntegerTypeDeclarationSQL(array $field)
    {
    }

    /** @override */
    public function getBigIntTypeDeclarationSQL(array $field)
    {
    }

    /** @override */
    public function getSmallIntTypeDeclarationSQL(array $field)
    {
    }

    /** @override */
    protected function _getCommonIntegerTypeDeclarationSQL(array $columnDef)
    {
    }

    /** @override */
    public function getVarcharTypeDeclarationSQL(array $field)
    {
    }

    /** @override */
    public function getClobTypeDeclarationSQL(array $field)
    {
    }

    /* MOCK API */

    public function setPrefersIdentityColumns($bool)
    {
        $this->_prefersIdentityColumns = $bool;
    }

    public function setPrefersSequences($bool)
    {
        $this->_prefersSequences = $bool;
    }

    public function setSequenceNextValSql($sql)
    {
        $this->_sequenceNextValSql = $sql;
    }

    public function getName()
    {
        return 'mock';
    }

    protected function initializeDoctrineTypeMappings()
    {
    }

    /**
     * @inheritDoc
     */
    public function getBlobTypeDeclarationSQL(array $field)
    {
        // TODO: Implement getBlobTypeDeclarationSQL() method.
    }

    /**
     * @inheritDoc
     */
    public function getListTablesSQL()
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    protected function getReservedKeywordsClass()
    {
        return SQLServerKeywords::class;
    }
}
