<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Osians\VeManager\VeManager;

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
        
        $connection = $provider->conectar();

        $this->vem = new VeManager($connection);
    }
    
    public function tearDown()
    {
    }
    
    public function testVirtualEntityCreate()
    {
        $vem = $this->vem;
        
        $user = $vem->createEntity('user');
        $user->setId(12);
        $user->setName('Jhonatan Doe');
        $user->setEmail('jhonatan.doe@gmail.com');
        $user->setAge(31);
        $user->setActive(1);

        //  Persiste
        $vem->save($user);
        //$this->assertEquals('John Doe', $user->getName());
        $this->assertTrue(is_integer(intval($user->getId())));
    }
    
}
