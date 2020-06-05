<?php

namespace Wsantana\VeManager;

interface EntityInterface
{
    /**
     * Set Entity ID
     *
     * @param int $id
     *
     * @return  Entity
     */
    public function setId($id);

    /**
     * Returns Entity ID
     *
     * @return int
     */
    public function getId();
    
    /**
     * Initializes the current Entity with data from a StdClass Object.
     * Usually sent by EntityManager
     *
     * @param Stdclass $object
     *
     * @return void
     */
    public function init(\StdClass $object);

    /**
     * Retorna o Nome da tabela a qual a Entidade se refere.
     * Se não for setado na entidade filho, sera usado o nome da
     * classe como nome da tabela por padrão.
     *
     * @return String
     */
    public function getTableName();

    /**
     * Retorna nome da coluna que guarda Chave Primaria da Entidade.
     * Por padrao o nome e' composto de "id_ + nome_da_classe" em lowercase.
     *
     * @return String
     */
    public function getPrimaryKeyName();
}
