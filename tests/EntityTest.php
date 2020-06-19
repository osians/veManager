<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Osians\VeManager\VeManager;
use Osians\VeManager\QueryBuilder;
use Osians\VeManager\Entity;

class EntityTest extends TestCase
{
    private $_connection = ['localhost', '3306', 'wsantana', '123456', 'datamanager'];

    public function setUp()
    {
        $provider = new \Osians\VeManager\Database\Provider\Mysql();
        $provider
            ->setHostname($this->_connection[0])
            ->setPort($this->_connection[1])
            ->setUsername($this->_connection[2])
            ->setPassword($this->_connection[3])
            ->setDatabaseName($this->_connection[4]);
        
        $connection = $provider->conectar();

        $this->vem = new VeManager($connection);
    }
    
    public function testEntityCreate()
    {
        $entity = new Entity();
        $this->assertInstanceOf('\Osians\VeManager\Entity', $entity);
    }
    
//    public function testEntitySetData()
//    {
//        $entity = new Entity();
//        $entity->setId(4);
//        $entity->setName('John Doe');
//        $entity->setAge(54);
//        $entity->setMail('john.doe@acmeinc.com');
//        var_dump($entity);
////        die();
//        $this->assertTrue(false);
//    }
    
    public function tearDown()
    {
    }
}
