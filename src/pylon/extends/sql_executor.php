<?php

/** 
 * @brief 基于PDO对Mysql 的SQL 执行器
 */
class FastSQLExecutor
{
    /** 
     * @brief 长连接
     */
    const LONG_CONN=true;
    /** 
     * @brief 短连接
     */
    const SHORT_CONN=false;
    private $_dbh = null;
    private $_sqlCollector =null;
    private $_sqlLogger  = null;
    private $_wsqlLogger  = null;

    private $_connectInfo=null;

    /** 
     * @brief  构造函数
     * 
     * @param $host 
     * @param $userName 
     * @param $password 
     * @param $dbName 
     * @param $connType  连接类型 
     * @param $charset 
     * @param $port 
     * 
     * @return  void
     */
    public function __construct($host, $userName, $password, $dbName,$connType=FastSQLExecutor::SHORT_CONN,$charset='GBK',$port=3306)
    {

        $this->_connectInfo['dsn']="mysql:host=$host;dbname=$dbName;port=$port";
        $this->_connectInfo['userName']=$userName;
        $this->_connectInfo['password']=$password;
        $this->_connectInfo['connType']=$connType;
        $this->_connectInfo['charset']=$charset;
        $this->connect();
    }

    public function reconnect()
    {
        $db_info = $this->_dbh->getAttribute(PDO::ATTR_SERVER_INFO);
        if($db_info == "MySQL server has gone away")
        {
            $this->connect();
        }
    }

    public function connect()
    {
        $dblog = new logger("_res");

        $this->_dbh = new PDO($this->_connectInfo['dsn'], $this->_connectInfo['userName'],$this->_connectInfo['password']
            , array(PDO::ATTR_PERSISTENT => $this->_connectInfo['connType']));
        $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_dbh->query("SET NAMES ".$this->_connectInfo['charset']);
        $dblog->info("connect mysql: " . $this->_connectInfo['dsn'] . " user: " . $this->_connectInfo['userName'] );
    }

    public function regCollector($collector)
    {
        $this->_sqlCollector = $collector;
    }
    public function haveCollector()
    {
        return $this->_sqlCollector !=null;
    }
    public function unRegCollector()
    {
        $this->_sqlCollector = null;
    }
    public function regLogger($sqlLogger,$wirteSqlLogger) 
    {
        $log = new logger("_pylon");
        $log->warn(" FastSQLExecutor::regLogger have deprecated! ");
    }
    public function regLogger4test($sqlLogger,$wirteSqlLogger) 
    {
        $this->_sqlLogger = $sqlLogger;
        $this->_wsqlLogger = $wirteSqlLogger;
    }
    public function query($sql, $values=array())
    {
        $dc = DiagnoseContext::create(__METHOD__);
        $this->logAllSql($dc,$sql,$values,"r");
        $sth = $this->_dbh->prepare($sql);
        $i = 0;
        foreach($values as $value)
        {
            $sth->bindValue(++$i, $value);
        }
        if($sth->execute())
        {
            $results = $sth->fetchAll(PDO::FETCH_ASSOC);
            if(is_array($results) && isset($results[0]))
            {
                $dc->notkeep();
                return $results[0];
            }
        }
        $dc->notkeep();
        return null;
    }

    public function querys($sql, $values=array())
    {

        $dc = DiagnoseContext::create(__METHOD__);
        $this->logAllSql($dc,$sql,$values,"r");
        $sth = $this->_dbh->prepare($sql);
        $i = 0;
        $res = array();
        foreach($values as $value)
        {
            $sth->bindValue(++$i, $value);
        }
        if($sth->execute())
        {
            $res= $sth->fetchAll(PDO::FETCH_ASSOC);
        }
        $dc->notkeep();
        return  $res;
    }


    static public function stdSqlValues($arr)
    {
        $lists = array();
        foreach($arr as $key => $item)
        {
            if(is_string($item)) {
                $lists[$key] = "'$item'";
            }
            elseif(is_null($item)) {
                $lists[$key] = "null";
            }
            else  {
                $lists[$key] = $item;
            }
        }
        return $lists;
    }
    public function logWritedSql($dc,$sql, $values=array())
    {
        if(!is_null( $this->_wsqlLogger) ) 
        {
            if(!empty($values))
            {
                // proc sql such as " like xxx%c"
                $logsql = str_replace('%','#',$sql); 
                $logsql = str_replace('?','%s',$logsql);
                $logsql= vsprintf($logsql,self::stdSqlValues($values));
                $logsql = str_replace('#','%s',$logsql);
                $this->_wsqlLogger->log($logsql);
                $dc->log("sql:$logsql");
            }
            else
            {
                $this->_wsqlLogger->log($sql);
                $dc->log("sql:$sql");
            }

        }
    }

    public function logAllSql($dc,$sql, $values=array(),$e="")
    {
        $slog = new logger("_sql");
        if(!empty($values))
        {
            $logsql = str_replace('%','#',$sql); // process like : linke %xxx%
            $logsql = str_replace('?','%s',$logsql);
            $logsql= vsprintf($logsql,self::stdSqlValues($values));
            $logsql = str_replace('#','%',$logsql);
            $dc->log("sql:$logsql");
            if(!is_null($this->_sqlLogger) )  {
                $this->_sqlLogger->log($logsql);
            }
            else {
                $slog->info($logsql,$e);
            }

        }
        else
        {
            $dc->log("sql:$sql");
            if(!is_null($this->_sqlLogger) )  {
                $this->_sqlLogger->log($sql);
            }
            else {
                $slog->info($sql,$e);
            }
        }

    }
    public function exeNoQuery($sql, $values=array())
    {
        $dc = DiagnoseContext::create(__METHOD__);
        $this->logWritedSql($dc,$sql,$values);
        $this->logAllSql($dc,$sql,$values,"w");
        $sth = $this->_dbh->prepare($sql);
        $i = 0;
        foreach($values as $value)
        {
            $sth->bindValue(++$i, $value);
        }
        if (! $sth->execute() ) 
        {
            throw new DBException( $sql);
        }
        $ret = $sth->rowCount();
        $dc->notkeep();
        return $ret;
    }
    public function exeNoQueryDirect($sql, $values=array())
    {
        //        $this->logAllSql($sql,$values);
        $sth = $this->_dbh->prepare($sql);
        $i = 0;
        foreach($values as $value)
        {
            $sth->bindValue(++$i, $value);
        }
        if (! $sth->execute() ) 
        {
            throw new DBException( $sql);
        }
        $ret = $sth->rowCount();
        return $ret ;
    }
    public function exeNoQuerys($cmds)
    {
        if(is_array($cmds) && !empty($cmds))
        {
            foreach($cmds as $cmd)
            {
                $this->exeNoQuery($cmd);
            }
        }
        else {
            return false;
        }

        return true;
    }
    public function execute($sql, $values=array())
    {
        $dc = DiagnoseContext::create(__METHOD__);
        $this->logWritedSql($dc,$sql,$values);
        $this->logAllSql($dc,$sql,$values,"w");
        $sth = $this->_dbh->prepare($sql);
        $i = 0;
        foreach($values as $value)
        {
            $sth->bindValue(++$i, $value);
        }
        if (!$sth->execute() ) {
            throw new DBException( $sql);
        }
        $dc->notkeep();
        return $sth->rowCount();
    }


    public function beginTrans()
    {
        $this->_dbh->beginTransaction();
    }

    public function commit()
    {
        if($this->_dbh!=null) {
            return $this->_dbh->commit();
        }
        return true;
    }

    public function rollback()
    {
        if($this->_dbh!=null) {
            return $this->_dbh->rollback();
        }
        return true;
    }

    public function getLastInsertID()
    {
        return (int)$this->_dbh->lastInsertId();
    }

}


class Pair
{
    public $first;
    public $second;

    public function __construct($first,$second)
    {
        $this->first    = $first;
        $this->second   = $second;
    }
}
/** 
 * @brief 基于 Layzer Load 的 SQL 执行器，用于在需要访问数据库是，才建立连接
 * @example 
 *          $executer = new LZLExecutor($dbConf->host,$dbConf->user,$dbConf->password,$dbConf->name,
 *                        FastSQLExecutor::SHORT_CONN,'utf8',"FastSQLExecutor");
 *
 */
class LZLExecutor
{
    public $host;
    public $userName;
    public $password;
    public $dbName;
    public $connType;
    public $charset;
    public $port;
    public $cls;
    public $ins = null;
    public $recordCalls= null;
    public $needRecord= true;
    /** 
     * @brief  同 FastSQLExecutor 的构造函数,除$cls参数外
     * 
     * @param $host 
     * @param $userName 
     * @param $password 
     * @param $dbName 
     * @param $connType 
     * @param $charset 
     * @param $cls 
     * @param $port 
     * 
     * @return 
     */
    public function __construct($host, $userName, $password, $dbName,$connType=FastSQLExecutor::SHORT_CONN,$charset='GBK', $cls = "FastSQLExecutor" , $port="3306")
    {
        $this->host     = $host;
        $this->userName = $userName;
        $this->password = $password;
        $this->dbName   = $dbName;
        $this->connType = $connType ;
        $this->charset  = $charset;
        $this->cls      = $cls;
        $this->port     = $port;
        $this->recordCalls = array();
    }
    public function __call($name, $arguments)
    {
        if($this->needRecord && (strncmp($name,"exe",3) != 0  &&  strncmp($name,"query",5) != 0 ))
        {
            array_push($this->recordCalls , new Pair($name , $arguments));
        }
        else
        {
            $this->needRecord = false;

            if ($this->ins == null)
            {
                $cls = $this->cls;
                $this->ins = new  $cls($this->host,$this->userName,$this->password,
                    $this->dbName,$this->connType,$this->charset,$this->port);
            }
            if(!empty($this->recordCalls))
            {
                foreach( $this->recordCalls  as $befcall)
                {
                    call_user_func_array(array($this->ins,$befcall->first),$befcall->second);
                }
                $this->recordCalls = null;
            }
            return call_user_func_array(array($this->ins,$name),$arguments);
        }
    }
}
