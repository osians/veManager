<?php

require_once __DIR__ . '/../vendor/autoload.php';

use \Wsantana\VeManager\QueryBuilderInterface;
use \Wsantana\VeManager\QueryBuilder;
use \Wsantana\VeManager\Database\Provider\Mysql;
use \Wsantana\VeManager\EntityInterface;
use \Wsantana\VeManager\Entity;
use \Wsantana\VeManager\VirtualEntity;
use \Wsantana\VeManager\VeManager;

# Conexao...
$driver = new Mysql();
$driver
	->setHostname('localhost')
	->setPort('3306')
	->setUsername('wsantana')
	->setPassword('123456')
	->setDatabaseName('mdm');

$connection = $driver->conectar();

# query Builder
// $query = new QueryBuilder();
// $query->select()->from('usuario')->where('id_usuario = ?', 2);

# Virtual Entity Manager
$vem = new VeManager($connection);
// $entity = $vem->query($query)[0];

// $entity->setNome('Pia');
// $entity->setSbnome('Hasselhorst');
// $entity->setEmail('pia.hasselhorst@hotmail.com');


# criando uma nova entidade Usuario
# cria uma nova entidade a partir do banco de dados
// $entity = $vem->createEntity('usuario');

// $entity->setNome('Mario');
// $entity->setSbnome('Quintana');
// $entity->setEmail('mario@quintana.com.br');
// $entity->setAtivo(0);


// print_r($entity);

// $vem->save($entity);

// -- ----------------------------------------------
$query = new QueryBuilder();
$query
	->select()
	->from('usuario', ['*'])
	->innerJoin(['ue' => 'usuario_endereco'], 'ue.id_usuario = usuario.id_usuario AND ue.ativo = 1')
	->innerJoin('endereco', 'endereco.id_endereco = ue.id_endereco')
	->where('usuario.id_usuario = ?', 3);

die($query);

// $rs = $vem->query($query);
// print_r($rs);

// -- ----------------------------------------------


// -- ----------------------------------------------
// @todo precisa testar o Delete

$query = new QueryBuilder();
$query->delete()->from('usuario')->where('id_usuario = 20');

echo $query;

// -- ----------------------------------------------

// @todo precisa adicionar uma nova tabela endereços e tentar realizar as relations



