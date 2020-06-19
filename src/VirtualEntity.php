<?php

namespace Osians\VeManager;

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

    /**
     * @see Entity::_callSetMethod()
     * @return $this
     */
    protected function _callSetMethod($property, $value)
    {
        $this->_setChangedProperty($property, $this->{$property}, $value);
        return parent::_callSetMethod($property, $value);
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
    protected function _setChangedProperty($property, $before, $after)
    {
        if (!isset($this->_changedFields[$property])) {
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
}
