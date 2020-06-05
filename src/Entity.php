<?php

namespace Wsantana\VeManager;

class Entity implements EntityInterface
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
     *    Construct
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
            "O método '{$method}' não existe na classe '" . 
            get_class($this) . "'."
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
     *    Caso clone, elimina o ID na classe resultante
     */
    public function __clone()
    {
        $this->_id = null;
    }
}
