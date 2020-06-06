<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class InstanceTest extends TestCase
{
    private $_drive;
    private $_databasename = 'datamanager';

    public function setUp()
    {
//        $utils = new Utils();
//        $validator = new Validate($utils);
//        $sanitize = new Sanitize();
//        $encoder = new Criptos();
//
//        $this->fdb = new \Flatdbase\Flatdbase();
//        $this->fdb->setSanitize($sanitize);
//        $this->fdb->setValidate($validator);
//        $this->fdb->setEncoder($encoder);
        
        //$this->fdb->use($this->databaseName);
    }

    public function testMysqlProviderSetUp()
    {
        $this->_driver = new \Osians\VeManager\Database\Provider\Mysql();
        $this->_driver
            ->setHostname('localhost')
            ->setPort('3306')
            ->setUsername('wsantana')
            ->setPassword('123456')
            ->setDatabaseName($this->_databasename);
        
        $this->assertInstanceOf('\Osians\VeManager\Database\Provider\Mysql', $this->_drive);
    }
    
    //    create Database
//    public function testDatabaseCreate()
//    {
//        $this->databaseName = 'test';
//        
//        if ($this->fdb->databaseExists($this->databaseName)) {
//            $this->fdb->delete($this->databaseName);
//        }
//
//        $newdatabase = $this->fdb->create($this->databaseName);
//
//        $this->assertInstanceOf('\Flatdbase\Flatdbase', $newdatabase);
//    }

    //    create table
//    public function testDatabaseCreateTable()
//    {
//        $this->fdb->use($this->databaseName);
//
//        $fields = array(
//            'id'    => array('type'=>'integer',   'size'=>'10','null'=>'0','auto_increment'=>'1'),
//            'nome'  => array('type'=>'stringOnly','size'=>'32','null'=>'0'),
//            'email' => array('type'=>'email',     'size'=>'64','null'=>'0'),
//            'status'=> array('type'=>'boolean',   'size'=>'1', 'null'=>'1','default'=>'1'),
//        );
//
//        // @var \Flatdbase\Response $r 
//        $r = $this->fdb->createTable('users', $fields);
//        
//        $this->assertTrue($r->getResult());
//    }

    // public function testDatabaseExists()
    // {
    //     if( !$database->exists( "database_test" ) )
    //     $database->create( "database_test" );   
    // }

    // public function testDatabaseInexists()
    // {

    // }



    // public function testImplementsPsr3LoggerInterface()
    // {
    //     $this->assertInstanceOf('\Osians\Logit\LogitInterface', $this->logger);
    // }

    // public function testAcceptsExtension()
    // {
    //     $this->assertStringEndsWith('.log', $this->errLogger->getLogFilePath());
    // }

    // public function testAcceptsPrefix()
    // {
    //     $filename = basename($this->errLogger->getLogFilePath());
    //     $this->assertStringStartsWith('error_', $filename);
    // }

    // public function testWritesBasicLogs()
    // {
    //     $this->logger->log(LogitLevel::DEBUG, 'This is a test');
    //     $this->errLogger->log(LogitLevel::ERROR, 'This is a test');

    //     $this->assertTrue(file_exists($this->errLogger->getLogFilePath()));
    //     $this->assertTrue(file_exists($this->logger->getLogFilePath()));

    //     $this->assertLastLineEquals($this->logger);
    //     $this->assertLastLineEquals($this->errLogger);
    // }


    // public function assertLastLineEquals(Logit $logr)
    // {
    //     $this->assertEquals($logr->getLastLogLine(), $this->getLastLine($logr->getLogFilePath()));
    // }

    // public function assertLastLineNotEquals(Logit $logr)
    // {
    //     $this->assertNotEquals($logr->getLastLogLine(), $this->getLastLine($logr->getLogFilePath()));
    // }

    // private function getLastLine($filename)
    // {
    //     $size = filesize($filename);
    //     $fp = fopen($filename, 'r');
    //     $pos = -2; // start from second to last char
    //     $t = ' ';

    //     while ($t != "\n") {
    //         fseek($fp, $pos, SEEK_END);
    //         $t = fgetc($fp);
    //         $pos = $pos - 1;
    //         if ($size + $pos < -1) {
    //             rewind($fp);
    //             break;
    //         }
    //     }

    //     $t = fgets($fp);
    //     fclose($fp);

    //     return trim($t);
    // }

    public function tearDown() {
        #@unlink($this->logger->getLogFilePath());
        #@unlink($this->errLogger->getLogFilePath());
    }
}

