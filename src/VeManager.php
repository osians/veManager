<?php

namespace Osians\VeManager;

use \Osians\VeManager\VirtualEntity;
use \Osians\VeManager\QueryBuilder;

/**
 * Virtual Entity Manager
 *
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @package VEM - Virtual Entity Manager
 */
class VeManager
{
    /**
     * PDO Connection
     *
     * @var \PDO
     */
    protected $_connection = null;

    /**
     * Default QueryBulder
     *
     * @var \Osians\VeManager\QueryBuilder
     */
    protected $_queryBuilder = null;
    
    /**
     * Construct
     *
     * @param \PDO $conn
     */
    public function __construct(\PDO $conn)
    {
        $this->setConnection($conn);
        $this->initQueryBuilder();
    }

    /**
     * Set PDO Connection to be used
     *
     * @param \PDO $conn [description]
     *
     * @return  VeManager
     */
    public function setConnection(\PDO $conn)
    {
        $this->_connection = $conn;
        return $this;
    }

    /**
     * Get PDO Connection
     *
     * @return \PDO
     *
     * @throws String [<description>]
     */
    public function getConnection()
    {
        if (null === $this->_connection) {
            throw new Exception("PDO Connection is missing", 1);
        }

        return $this->_connection;
    }

    /**
     * Initialize Default Query Builder
     *
     * @return VeManager
     */
    public function initQueryBuilder()
    {
        $this->_queryBuilder = new QueryBuilder();
        return $this;
    }
    
    /**
     * Get Default Query Builder
     *
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->_queryBuilder;
    }
    
    /**
     * Prepare an SQL Query
     *
     * @param  QueryBuilderInterface $query
     *
     * @return Stmt
     */
    protected function _prepare(QueryBuilderInterface $query)
    {
        return $this->getConnection()->prepare($query);
    }

    public function query(QueryBuilderInterface $query, $asArray = false)
    {
        $resultSet = array();
        $stm = $this->_prepare($query);
        $stm->execute();

        if ($stm->rowCount() > 0) {
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);

                // returns simple Array of StdClass
                if ($asArray) {
                        return $rows;
                }

                // returns  array of VirtualEntity
            foreach ($rows as $row) {
                $ve = new VirtualEntity();
                $ve->init($row);
                $ve->setQueryBuilder($query);
                $ve->setTablename($query->getTableName());
                $resultSet[] = $ve;
            }
        }

        return $resultSet;

    }

    public function fetchOne(QueryBuilderInterface $query)
    {
        $rs = $this->query($query);
        return count($rs) > 0 ? $rs[0] : $rs;
    }

    /**
     * Persist Data
     *  
     * @param Entity $entity
     *
     * @return integer|false - Last Insert ID or FALSE (case error)
     */
    public function save(Entity $entity)
    {
        if ($entity->getQueryBuilder() == null) {
            $entity->setQueryBuilder($this->getQueryBuilder());
        }
        
        if (null == $entity->getId()) {
            return $this->_saveNewRecord($entity);
        }

        return $this->_saveExistingRecord($entity);
    }


    /**
     * Insert a new Record into table
     *
     * @param  Entity $entity
     *
     * @return false | last_inserted_id
     */
    protected function _saveNewRecord(Entity $entity)
    {
        $values = array();
        foreach ($entity->getChangedProperty() as $column => $struct) {
            $values[$column] = $struct['to'];
        }

        if (empty($values)) {
            return false;
        }

        $qb = $this->getQueryBuilder();
        $qb->insert()->into($entity->getTableName())->values($values);
        $stm = $this->getConnection()->prepare($qb->sql());
        $stm->execute();

        $entity->setId($this->getConnection()->lastInsertId());
        return $this->getConnection()->lastInsertId();
    }


    /**
     * Update a record into table database
     *
     * @param  Entity $entity
     *
     * @return bool
     */
    protected function _saveExistingRecord(Entity $entity)
    {
        $camposAlterados = $entity->getChangedProperty();

        $tables = $entity->getQueryBuilder()->getUsedTables();

        if (empty($tables)) {
            $tables[] = $entity->getTableName();
        }
        
        foreach ($tables as $table) {

            $sets = array();

            foreach ($camposAlterados as $column => $struct) {
                if ($struct['id'] == null || $table != $struct['owner']) {
                    continue;
                }
                $sets["{$column} = ?"] = $struct['to'];
            }

            if (empty($sets)) {
                continue;
            }

            $qb = $this->getQueryBuilder();
            $qb->update($table);

            foreach ($sets as $key => $value) {
                $qb->set($key, $value);
            }

            $qb->where("{$struct['pk']} = ?", $struct['id']);

            $stm = $this->getConnection()->prepare($qb->sql());
            $stm->execute();
        }

        return $this;
    }


    /**
     * Creates a new Virtual Entity from Database Table
     *
     * @param  String $tablename
     *
     * @return VirtualEntity
     */
    public function createEntity($tablename)
    {
        $ve = new VirtualEntity();
        $desc = $this->_getDescFromTable($tablename);

        $obj = new \StdClass;
        foreach (array_keys($desc) as $property) {
            $obj->$property = null;
        }

        $ve->init($obj);
        $ve->setTablename($tablename);
        
        // set Default Query Builder
        $ve->setQueryBuilder($this->getQueryBuilder());

        return $ve;
    }

    private function _getDescFromTable($tablename)
    {
        $stm = $this->getConnection()->prepare("DESC `{$tablename}`");
        $stm->execute();

        $properties = null;

        if ($stm->rowCount() > 0) {
            $rows = $stm->fetchAll(\PDO::FETCH_OBJ);

            foreach ($rows as $key => $value) {
                $properties[$value->Field] = $value;
            }
        }

        return $properties;
    }
}
