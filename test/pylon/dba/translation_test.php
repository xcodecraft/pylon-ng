<?php

use PHPUnit\Framework\TestCase;
class TransTc //extends TestCase
//class TransTc
{
    private $_insertCmd = "insert trans_test (id,name) values(1,\"normal\"); ";
    private $_insertCmd1 = "insert trans_test_1 (id,name) values(1,\"normal\"); ";
    private $_updateCmd = "update trans_test set name=\"normal\" where id = 1;";
    private	$_queryCmd  = "select * from trans_test where id =1; ";
    private $_delCmd  = "delete from trans_test where id =1; ";
    private $_delCmd1  = "delete from trans_test_1 where id =1; ";

    private $_executer  =null;

    public function setUp()
    {

        $dbConf =  Conf::getDBConf();
        $this->_executer = new FastSQLExecutor( $dbConf->host,$dbConf->user,$dbConf->password,$dbConf->name);

        $cmds[] = "drop table if exists  trans_test";
        $cmds[] = "drop table if exists  trans_test_1";
        $cmds[] = "create table  trans_test
            (
                id                             integer(11),
                   name                           varchar(30),
                   PRIMARY KEY (id)
               ) ENGINE=Innodb
               ";
        $cmds[] = "create table  trans_test_1
            (
                id                             integer(11),
                   name                           varchar(30) ,
                   PRIMARY KEY (id)
               ) ENGINE=Innodb
               ";
        $this->_executer->exeNoQuerys($cmds);
    }
    public function tearDown()
    {
        $this->_executer->exeNoQuery($this->_delCmd);
        $this->_executer->exeNoQuery($this->_delCmd1);
    }
    function accessDB()
    {
        if(
            $this->_executer->exeNoQuery($this->_insertCmd) &&
            $this->_executer->exeNoQuery($this->_insertCmd1) &&
            $this->_executer->query($this->_queryCmd)&&
            $this->_executer->exeNoQuery($this->_updateCmd))
        {
            return true;
        }

        return false;
    }
    function accessDBFail()
    {
        if(
            $this->_executer->exeNoQuery($this->_insertCmd) &&
            $this->_executer->exeNoQuery($this->_insertCmd1) &&
            $this->_executer->exeNoQuery($this->_insertCmd1) &&
            $this->_executer->query($this->_queryCmd)&&
            $this->_executer->exeNoQuery($this->_updateCmd))
        {
            return true;
        }

        return false;
    }

    function doSucCommit()
    {
        $trans = new Translation($this->_executer);

        $this->accessDB();
        $this->assertTrue($this->_executer->haveCollector());
        if($trans->commit())
        {
            return $this->_executer->query($this->_queryCmd) != null;
        }
        return false;
    }
    function doFailCommit()
    {
        $trans = new Translation($this->_executer);
        $this->accessDBFail();
        $this->assertTrue($this->_executer->haveCollector());
        try{
        if($trans->commit())
        {
            return $this->_executer->query($this->_queryCmd) != null;
        }
        }catch(Exception $e)
        {
            $row = $this->_executer->query($this->_queryCmd) ;
            $this->assertTrue($row == null);
            throw $e;
        }
        return false;
    }

    function doRollback()
    {
        $trans = new Translation($this->_executer);
        $this->accessDB();
        $this->assertTrue($this->_executer->haveCollector());
        if($trans->rollback())
        {
            return $this->_executer->query($this->_queryCmd) == null;
        }
        return false;
    }

    function testCommit()
    {
        try{
            if($this->doSucCommit())
            {
                $this->assertTrue(true);
                return ;
            }
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        }
        $this->assertTrue(false);
    }


    public function testCommitFail()
    {
        try
        {
            if(!$this->doFailCommit())
            {
                $this->assertTrue(false);
            }
        }
        catch(Exception $e)
        {
            $this->assertTrue(!$this->_executer->haveCollector());
        }
    }


    public function testRollback()
    {
        try
        {
            if($this->doRollback())
            {
                $this->assertTrue(!$this->_executer->haveCollector());
                return ;
            }
        }
        catch(Exception $e)
        {
            $this->assertTrue(!$this->_executer->haveCollector());
            $this->assertTrue(false);
        }
    }

    /*
    function doNestCommit()
    {
        $manager = &TransManager::instance();
        $trans   = &$manager->createTrans();

        assert($this->doSucCommit());
        if($trans->commit())
        {
            return $this->_executer->query($row =null,$this->_queryCmd)
                        && $row != null;
        }
        return false;

    }
    function testNest1()
    {
        $manager = &TransManager::instance();
        $trans   = $manager->createTrans();
        $this->testCommit();
        $trans->commit();
    }

    function testNest2()
    {
        $manager = &TransManager::instance();
        $trans   = $manager->createTrans();
        $this->testCommit();
        $trans->rollback();
    }
    function testNest3()
    {
        $manager = &TransManager::instance();
        $trans   = $manager->createTrans();
        $this->testCommit();
        $this->testRollback();
        $trans->commit();
    }

    function testNest4()
    {
        $manager = &TransManager::instance();
        $trans   = $manager->createTrans();
        $this->testCommit();
        $this->testCommit();
        $trans->rollback();
    }
     */

}
