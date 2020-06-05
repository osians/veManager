<?php

namespace Wsantana\VeManager;

class VirtualEntity extends Entity
{
    /**
     * Guarda Query que deu origem a essa entidade
     *
     * @var QueryBuilderInterface
     */
    private $__query = null;

    /**
     * Lista os campos/propriedades que foram alteradas
     *
     * @var array
     */
    protected $_changedFields = array();

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * keeps the QueryBuilder for later use
     *
     * @param  QueryBuilderInterface $query
     *
     * @return  \VirtualEntity
     */
    public function setQueryBuilder(QueryBuilderInterface $query)
    {
        $this->__query = $query;
        return $this;
    }

    /**
     * Returns QueryBuilder
     *
     * @return \QueryBuilderInterface
     */
    public function getQueryBuilder()
    {
        return $this->__query;
    }

    protected function _callSetMethod($property, $value)
    {
        $this->_checkIfPropertyExists($property);
        $this->setChangedProperty($property, $this->{$property}, $value);
        $this->{$property} = $value;
        return $this;
    }

    /**
     * Quando uma propriedade do objeto e' alterada,
     * guarda o nome da propriedade para facilitar
     * obter os campos alterados na hora do persist.
     *
     * @param String $property
     * @param String $before
     * @param String $after
     *
     * @return \VirtualEntity
     */
    protected function setChangedProperty($property, $before, $after)
    {
        if (!isset($this->_changedFields[$property]))
        {
            $key = $this->_camelCaseToSnakeCase(substr($property, 1, strlen($property)));
            $owner = $this->_getOwner($key);
            $ownerId = $this->_snakeCaseToCamelCase("id_{$owner}");

            $this->_changedFields[$key] = array(
                    'from' => $before,
                    'to' => $after,
                    'owner' => $owner,
                    'id' => $this->$ownerId,
                    'pk' => "id_{$owner}"
            );
        }
        return $this;
    }

    private function _getOwner($field)
    {
        return null !== $this->getQueryBuilder() 
            ? $this->getQueryBuilder()->getFieldOwner($field)
            : $this->getTableName();
    }

    /**
     * Returns Array with changed Properties
     *
     * @return Array of Array - [from, to, owner, id, pk]
     */
    public function getChangedProperty()
    {
        return $this->_changedFields;
    }

    protected function _callGetMethod($property, $argumentos)
    {
        $this->_checkIfPropertyExists($property);
        return $this->{$property};
    }

    protected function getPropertyFromMethodName($method)
    {
        return '_' . lcfirst(substr($method, 3));
    }

    protected function _checkIfPropertyExists($property)
    {
        if (property_exists($this, $property) == false) {
            throw new \Exception(
                "Property '{$property}' does not exist at '" . get_class($this) . "'"
            );
        }
    }
}
