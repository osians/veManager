<?php

require_once __DIR__ . '/../src/EntityInterface.php';
require_once __DIR__ . '/../src/Entity.php';
require_once __DIR__ . '/../src/VirtualEntity.php';
require_once __DIR__ . '/../src/VeManager.php';
require_once __DIR__ . '/../src/QueryBuilderInterface.php';
require_once __DIR__ . '/../src/QueryBuilder.php';
require_once __DIR__ . '/../src/Database/Provider/ProviderInterface.php';
require_once __DIR__ . '/../src/Database/Provider/Mysql.php';

use Osians\VeManager\QueryBuilderInterface;
use Osians\VeManager\QueryBuilder;
use Osians\VeManager\Database\Provider\Mysql;
use Osians\VeManager\EntityInterface;
use Osians\VeManager\Entity;
use Osians\VeManager\VirtualEntity;
use Osians\VeManager\VeManager;

// init provider
$drive = new Mysql();
$drive
    ->setHostname('localhost')
    ->setPort('3306')
    ->setUsername('wsantana')
    ->setPassword('123456')
    ->setDatabaseName('sooho658_apps');

// get pdo
$pdo = $drive->connect();

// create a query
$select = new QueryBuilder();

// build the query
$select
    ->select()
    ->from('usuario')
    ->where('ativo = ?', 1)
    ->limit(1);
 
// create entity manager
$vem = new VeManager($pdo);

// get result
$result = $vem->setConnection($pdo)->query($select);
$entity = $result[0];

var_dump($entity);