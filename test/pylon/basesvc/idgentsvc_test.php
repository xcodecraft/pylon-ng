<?php

use pylon\driver\XFastSQLExecutor ;
use pylon\driver\IDGenterSvcImp ;
use pylon\driver\MySqlIDGenerator ;
use PHPUnit\Framework\TestCase;
class TestX
{
    private $name;
    public function __construct($name)
    {
        $this->name =$name;
    }
}
class IDGenterSvcTC extends TestCase
{
    private $_executer;
    private $_tableName;

    public function __construct()
    {
        $host     = XSetting::ensureEnv("DB_HOST") ;
        $user     = XSetting::ensureEnv("DB_USER") ;
        $password = XSetting::ensureEnv("DB_PWD") ;
        $name     = XSetting::ensureEnv("DB_NAME") ;
        $this->_executer = new XFastSQLExecutor( $host,$user,$password,$name);
        $this->_tableName = "id_genter";

    }


    public function setUp()
    {
        $cmd = "CREATE TABLE IF NOT EXISTS {$this->_tableName}
        (
                    id                             integer(11),
                    obj                            varchar(30),
                    step  integer(11)
               )";

        $this->assertFalse($this->_executer == NULL);
        $this->_executer->exeNoQuery($cmd);

        $cmd = "SELECT * FROM {$this->_tableName}";
        $row = $this->_executer->query($cmd);
        if(empty($row))
        {
            $cmds[] = "INSERT INTO {$this->_tableName}(id, obj, step) VALUES(1, \"test\", 10)";
            $cmds[] = "INSERT INTO {$this->_tableName}(id, obj, step) VALUES(1, \"other\", 10)";
            $this->_executer->exeNoQuerys($cmds);
        }
    }

    public function testCreate()
    {
        $gener = new IDGenterSvcImp($this->_executer);
        $otherid=array();
        $testid=array();
        for($i = 0; $i < 20; $i++)
        {
            $otherid[] = $gener->createID('other');
            $testid[] =  $gener->createID('test');
        }
        for($i=1 ; $i<19; $i++)
        {
            $this->assertTrue($otherid[$i-1]<$otherid[$i]);
            $this->assertTrue($testid[$i-1]<$testid[$i]);
        }
    }

    public function testSameObj()
    {
        $a = new TestX("a");
        $b = new TestX("a");
        $c = clone $a;
        $this->assertTrue($a == $b );
        $this->assertTrue($a !== $b );
        $this->assertTrue($a !== $c );
    }
}

class Double_Master_IDGenterSvcTC extends TestCase
{
    private $_executer;
    private $_tableName;

    public function __construct()
    {

        $host     = XSetting::ensureEnv("DB_HOST") ;
        $user     = XSetting::ensureEnv("DB_USER") ;
        $password = XSetting::ensureEnv("DB_PWD") ;
        $name     = XSetting::ensureEnv("DB_NAME") ;
        $this->_executer = new XFastSQLExecutor( $host,$user,$password,$name);
    }

    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
    }

    public function _setUp()
    {
        $cmd = "DROP TABLE IF EXISTS {$this->_tableName}";
        $this->assertFalse($this->_executer == NULL);
        $this->_executer->exeNoQuery($cmd);
        $cmd = "CREATE TABLE IF NOT EXISTS {$this->_tableName}
        (
                    id                             integer(11),
                    obj                            varchar(30),
                    step  integer(11)
               )";

        $this->_executer->exeNoQuery($cmd);

        $cmd = "SELECT * FROM {$this->_tableName}";
        $row = $this->_executer->query($cmd);
        if(empty($row))
        {
            $cmds[] = "INSERT INTO {$this->_tableName}(id, obj, step) VALUES(1, \"test\", 10)";
            $cmds[] = "INSERT INTO {$this->_tableName}(id, obj, step) VALUES(1, \"other\", 10)";
            $this->_executer->exeNoQuerys($cmds);
        }
    }

    public function Create($tableName, $second=false)
    {
        $this->setTableName($tableName);
        $this->_setUp();
        $gener = new IDGenterSvcImp($this->_executer, $clone=true, $second);
        $otherid=array();
        $testid=array();
        for($i = 0; $i < 20; $i++)
        {
            $otherid[] = $gener->createID('other');
            $testid[] =  $gener->createID('test');
        }
        for($i=1 ; $i<19; $i++)
        {
            if($second)
            {
                $result = $otherid[$i] & 1 ;
                $this->assertFalse($result == 1);
                $result = $testid[$i] & 1 ;
                $this->assertFalse($result == 1);
            }
            else
            {
                $result = $otherid[$i] & 1 ;
                $this->assertTrue($result == 1);
                $result = $testid[$i] & 1 ;
                $this->assertTrue($result == 1);
            }
            $this->assertTrue($otherid[$i-1]<$otherid[$i]);
            $this->assertTrue($testid[$i-1]<$testid[$i]);
        }
    }

    public function testCreateFirst()
    {
        IDGenterSvcImp::ENABLE_DOUBLE_MASTER();
        $this->Create('id_genter');
    }

    public function testCreateSecond()
    {
        IDGenterSvcImp::ENABLE_DOUBLE_MASTER();
        $this->Create('second_id_genter', $second=true);
        IDGenterSvcImp::$ENABLE_DOUBLE_MASTER = false;
    }
}
