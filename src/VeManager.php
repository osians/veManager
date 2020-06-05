<?php

namespace Wsantana\VeManager;

use \Wsantana\VeManager\VirtualEntity;


/**
 * Virtual Entity Manager
 * 
 * @author Wanderlei Santana <sans.pds@gmail.com>
 * @package VEM - Virtual Entity Manager
 */
class VeManager
{
	/**
	 *    PDO Connection
	 *
	 *    @var \PDO
	 */
	protected $_connection = null;

	/**
	 *    Construct
	 *
	 *    @param \PDO $conn
	 */
	public function __construct(\PDO $conn)
	{
		$this->setConnection($conn);
	}

	/**
	 *    Set PDO Connection to be used
	 *
	 *    @param \PDO $conn [description]
	 *
	 *    @return  VeManager
	 */
	public function setConnection(\PDO $conn)
	{
		$this->_connection = $conn;
		return $this;
	}

	/**
	 *    Get PDO Connection
	 *
	 *    @return \PDO
	 *
	 *    @throws String [<description>]
	 */
	public function getConnection()
	{
		if (null === $this->_connection) {
			throw new Exception("PDO Connection is missing", 1);
		}

		return $this->_connection;
	}

	/**
	 *    Prepare an SQL Query
	 *
	 *    @param  QueryBuilderInterface $query
	 *
	 *    @return Stmt
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

	public function save(VirtualEntity $entity)
	{
		//    insert
		if (null == $entity->getId()) {
			return $this->_saveNewRecord($entity);
		}
		//    update
		return $this->_saveExistingRecord($entity);
	}


	/**
	 *    Insert a new Record into table
	 *
	 *    @param  VirtualEntity $entity
	 *
	 *    @return false | last_inserted_id
	 */
	protected function _saveNewRecord(VirtualEntity $entity)
	{
		$values = array();
		foreach ($entity->getChangedProperty() as $column => $struct) {
			$values[$column] = $struct['to'];
		}

		if (empty($values)) {
			return false;
		}

		$query = new QueryBuilder();
		$query->insert()->into($entity->getTableName())->values($values);
		$stm = $this->getConnection()->prepare($query->sql());
		$stm->execute();

		$entity->setId($this->getConnection()->lastInsertId());
		return $this->getConnection()->lastInsertId();
	}


	/**
	 *    Update a record into table database
	 *
	 *    @param  VirtualEntity $entity
	 *
	 *    @return bool
	 */
	protected function _saveExistingRecord(VirtualEntity $entity)
	{
		$camposAlterados = $entity->getChangedProperty();

		$tables =$entity->getQueryBuilder()->getUsedTables();

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

			$query = new QueryBuilder();
			$query->update($table);

			foreach ($sets as $key => $value) {
				$query->set($key, $value);
			}

			$query->where("{$struct['pk']} = ?", $struct['id']);

			$stm = $this->getConnection()->prepare($query->sql());
			$stm->execute();
		}

		return $this;
	}


	/**
	 *    Creates a new Virtual Entity from Database Table
	 *
	 *    @param  String $tablename
	 *
	 *    @return VirtualEntity
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
