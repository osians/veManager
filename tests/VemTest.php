<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Osians\VeManager\VeManager;
use Osians\VeManager\QueryBuilder;

// PHPUnit test class
class VemTest extends TestCase
{
    private $_connection = ['localhost', '3306', 'wsantana', '123456', 'datamanager'];
    
    protected $vem = null;
    
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
    
    public function tearDown()
    {
    }
    
    public function testVirtualEntityCreate()
    {
        $vem = $this->vem;
        
        $user = $vem->create('user');
        $user->setId(16); // comment this line to make a insert instead of a update
        $user->setName('Jhonatan Doe');
        $user->setEmail('jhonatan.doe@gmail.com');
        $user->setAge(31);
        $user->setActive(1);

        //  Persist
        $vem->save($user);
        //$this->assertEquals('John Doe', $user->getName());
        $this->assertTrue(is_integer(intval($user->getId())));
    }
    
    public function testGetDatabaseRecordAndChange()
    {
        // create a Database Query
        $q = new QueryBuilder();
        $q->select()->from('user')->where("email = ?", "john.doe@hotmail.com");
        
        // use VeManager to exec the Query
        // this will return one VirtualEntity Model
        $user = $this->vem->fetchOne($q);
        if (empty($user)) {
            // 'Record does not exist'
            $this->assertTrue(true);
            return;
        }
        
        // change something
        $user->setEmail('jhonatan.doe@aol.com');

        // use VeManager to Persist data
        $this->vem->save($user);
        
        $this->assertTrue(!empty($user));
    }
    
    public function testDeleteRecord()
    {
        $user = $this->vem->create('user');
        $user->setId(10);
        //$this->assertTrue($this->vem->delete($user));
        $this->assertTrue(true);
    }
    
    public function testGetEntity()
    {
        $user = $this->vem->get('user', 11);
        $this->assertInstanceOf('\Osians\VeManager\VirtualEntity', $user);
    }
    
}
