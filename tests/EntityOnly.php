<?php

require_once __DIR__ . '/../src/EntityInterface.php';
require_once __DIR__ . '/../src/Entity.php';


use Osians\VeManager\Entity;

class User extends Entity
{
    protected $_name = null;
    protected $_age = null;
    protected $_email = null;
}


function testEntitySetData()
{
    $entity = new User();
    $entity->setId(4);
    $entity->setName('John Doe');
//    $entity->setAge(54);
//    $entity->setMail('john.doe@acmeinc.com');
//    var_dump($entity);
    die();
}


testEntitySetData();
