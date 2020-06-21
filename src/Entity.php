<?php

namespace Osians\VeManager;

abstract class Entity implements EntityInterface
{
    /**
     * Nome da tabela da Entidade.
     * Mantenha null para usar o nome da
     * classe como nome da Tabela
     *
     * @var String
     */
    protected $__tableName = null;

    /**
     * Keeps Model Changed Fields
     *
     * @var array
     */
    protected $_changedFields = array();
    
    /**
     * Guarda Query que deu origem a essa entidade
     *
     * @var QueryBuilderInterface
     */
    private $__query = null;
    
    /**
     * Construct
     */
    public function __construct()
    {
    }

    /**
     * Set Entity ID
     *
     * @param Int $id
     *
     * @return Entity
     */
    public function setId($id)
    {
        $idProperty = $this->_snakeCaseToCamelCase($this->getPrimaryKeyName());
        $this->$idProperty = $id;
        return $this;
    }

    /**
     * Get Entity ID
     *
     * @return Int
     */
    public function getId()
    {
        $idProperty = $this->_snakeCaseToCamelCase($this->getPrimaryKeyName());
        return $this->$idProperty;
    }

    /**
     * Initialize Entity
     *
     * @param StdClass $object
     *
     * @return Entity
     */
    public function init(\StdClass $object)
    {
        foreach (get_object_vars($object) as $key => $value) {
            $property = $this->_snakeCaseToCamelCase($key);
            $this->{$property} = $value;
        }
        return $this;
    }

    /**
     * Converts a given snake case text format into Camel case
     *
     * @param  String $text
     *
     * @return String
     */
    protected function _snakeCaseToCamelCase($text)
    {
        return '_' . lcfirst(
            str_replace('_', '', ucwords($text, '_'))
        );
    }

    /**
     * Converts a given Camel case text format into Snake case
     *
     * @param  String $text
     *
     * @return String
     */
    protected function _camelCaseToSnakeCase($text = '')
    {
        if ($text == '_id') {
            return $this->getPrimaryKeyName();
        }

        return ltrim(strtolower(preg_replace(
            ["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
            ["_$1", "_$1_$2"], lcfirst($text))), '_');
    }

    /**
     * Call para metodos nao implementados da Entity
     *
     * @param  String $method
     * @param  Array $argumentos
     *
     * @return Entity
     * 
     * @throws Exception
     */
    public function __call($method, $argumentos)
    {
        if ($this->_isValidSetMethod($method)) {
            $this->_callSetMethod(
                $this->getPropertyFromMethodName($method),
                $argumentos[0]
            );
            return $this;
        }

        if ($this->_isValidGetMethod($method)) {
            return $this->_callGetMethod(
                $this->getPropertyFromMethodName($method), 
                $argumentos
            );
        }

        throw new \Exception(
            "Method '{$method}' does not exist in the class '".get_class($this)."'."
        );
    }

    /**
     * Verifica se o metodo chamado pelo cliente é um get*
     *
     * @param  String $method
     *
     * @return boolean
     */
    protected function _isValidGetMethod($method)
    {
        return substr(strtolower($method), 0, 3) === 'get';
    }

    /**
     * Verifica se o metodo chamado pelo cliente é um set*
     *
     * @param  String $method
     *
     * @return boolean
     */
    protected function _isValidSetMethod($method)
    {
        return substr(strtolower($method), 0, 3) === 'set';
    }

    /**
     * Call Set Method
     *
     * @param type $property
     * @param type $value
     *
     * @return $this
     */
    protected function _callSetMethod($property, $value)
    {
        $this->_setChangedProperty($property, $this->{$property}, $value);
        $this->_checkIfPropertyExists($property);
        $this->{$property} = $value;
        return $this;
    }
    
    /**
     * Call Get Method
     *
     * @param string $property
     * @param string $argumentos
     *
     * @return mixed
     */
    protected function _callGetMethod($property, $argumentos)
    {
        $this->_checkIfPropertyExists($property);
        return $this->{$property};
    }
    
    /**
     * Verify if property exists in this class
     *
     * @param string $property
     *
     * @throws \Exception
     */
    protected function _checkIfPropertyExists($property)
    {
        if (property_exists($this, $property) == false) {
            throw new \Exception(
                "Property '{$property}' does not exist in class '".get_class($this)."'"
            );
        }
    }
    
    /**
     * Get Property Name from Method Name
     *
     * @param string $method
     *
     * @return string
     */
    protected function getPropertyFromMethodName($method)
    {
        return '_' . lcfirst(substr($method, 3));
    }
    
    /**
     * Set table Name
     *
     * @param string $tablename
     *
     * @return $this
     */
    public function setTablename($tablename)
    {
        $this->__tableName = $tablename;
        return $this;
    }
    
    /**
     * @see EntityInterface::getTableName()
     * @return String
     */
    public function getTableName()
    {
        return null != $this->__tableName 
            ? $this->__tableName 
            : strtolower(get_class($this)); 
    }

    /**
     * @see EntityInterface::getPrimaryKeyName()
     * @return String
     */
    public function getPrimaryKeyName()
    {
        return "id_{$this->getTableName()}";
    }

    /**
     * Verifica se um nome qualquer segue o padrao de nomes de
     * propriedades de classes modelo. Ou seja: "_NomeDaPropriedade"
     *
     * @param  String $property
     *
     * @return boolean
     */
    protected function _isValidModelPropertyName($property)
    {
        return $property[0] == '_' && $property[1] != '_';
    }

    /**
     * Retorna Propriedades do Objeto Cliente em formato de Array
     *
     * @return Array
     */
    public function toArray()
    {
        $retorno = array();

        foreach (get_object_vars($this) as $property => $value) {
            if ($this->_isValidModelPropertyName($property)) {
                $key = $this->_camelCaseToSnakeCase($property);
                $retorno[$key] = $value;
            }
        }

        return $retorno;
    }

    /**
     * Quando uma propriedade do objeto e' alterada,
     * guarda o nome da propriedade para facilitar
     * obter os campos alterados na hora do persist.
     *
     * @param String $property - Model Class Property
     * @param String $before - Value Before Change
     * @param String $after - New Value
     *
     * @return \VirtualEntity
     */
    protected function _setChangedProperty($property, $before, $after)
    {
        if (isset($this->_changedFields[$property])) {
            return $this;
        }
        
        $key = $this->_camelCaseToSnakeCase(substr($property, 1, strlen($property)));
        $owner = $this->_getOwner($key);

        $ownerId = $this->_snakeCaseToCamelCase("id_{$owner}");
        if (!property_exists($this, $ownerId)) {
            $this->$ownerId = null;
        }

        $this->_changedFields[$key] = array(
            'from' => $before,
            'to' => $after,
            'owner' => $owner,
            'id' => $this->$ownerId,
            'pk' => "id_{$owner}"
        );
       
        return $this;
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

    /**
     * Get the name of the Table that owner this field
     *
     * @param string $field
     *
     * @return string
     */
    private function _getOwner($field)
    {
        return null !== $this->getQueryBuilder()
            ? $this->getQueryBuilder()->getFieldOwner($field)
            : $this->getTableName();
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
     *    Caso clone, elimina o ID na classe resultante
     */
    public function __clone()
    {
        $this->_id = null;
    }
}
