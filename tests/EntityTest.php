<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Osians\VeManager\VeManager;

// test Entity
class User extends Osians\VeManager\Entity
{
    protected $_name = null;
    protected $_age = null;
    protected $_email = null;
}

// PHPUnit test class
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
        
        $connection = $provider->connect();

        $this->vem = new VeManager($connection);
    }
    
    public function testEntityCreate()
    {
        $entity = new User();
        $this->assertInstanceOf('\Osians\VeManager\Entity', $entity);
    }
    
    public function testEntitySetData()
    {
        $user = new User();
        $user->setId(13);
        $user->setName('Diego da Silva');
        $user->setAge(28);
        $user->setEmail('luckshor@hotmail.com');

        $this->vem->save($user);
        
        $this->assertEquals('Diego da Silva', $user->getName());
//        $this->assertTrue(is_integer(intval($user->getId())));
    }
    
    public function tearDown()
    {
    }
}
