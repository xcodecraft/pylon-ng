<?php
namespace Pylon ;

/**\addtogroup DBA
 * @{
 */

/** 
 * @brief 
 */
class SqlCollector
{
    var $_writes;
    var $_reads;

    function SqlCollector()
    {
        $this->_writes = array();
        $this->_reads = array();

    }
    function addWriteCmd($cmd)
    {
        array_push($this->_writes,$cmd);
    }

    function addReadCmd($cmd)
    {
        array_push($this->_reads,$cmd);
    }
    function joinWCmds()
    {
        $joined = '';
        foreach($this->_writes as $cmd)
        {
            $joined = $joined.$cmd.";";
        }
        return $joined;
    }
}
/** 
 * @brief  事务
 * @example test_translation.php
 */
class Translation
{
    var $_executer;
    var $_isReged = false;
    var $_collector;
    var $_debuger;
    var $_isEnd=false;

    public function __construct($executer )
    {

        $this->_executer = $executer;
        $this->_isReged = false;
        // if(!$this->_executer->haveCollector())
        // {
        //
        //     $this->_collector = new SqlCollector();
        //     $this->_executer->regCollector($this->_collector);
        //     $this->_isReged = true;
        // }
    }

    /** 
        * @brief  commit translation
        * 
        * @return 
     */
    public function commit()
    {

        if($this->_isEnd) 
            return true;

        if($this->_isReged)
        {

            $sqls = $this->_collector->_writes;
            $this->_executer->unRegCollector();

            if(empty($sqls)) return true;
            $this->_executer->beginTrans() ;
            try{
                if($this->_executer->exeNoQuerys($sqls))
                {
                    if($this->_executer->commit())
                    {
                        $this->_isEnd=true;
                        return true;
                    }
                }
                return false;
            }
            catch(Exception $e)
            {
                $this->_executer->rollback();
                throw $e;
            }

        }
        return true;
    }

    /** 
        * @brief 
        * 
        * @return 
     */
    public function rollback()
    {

        if($this->_isReged)
        {
            $collector = $this->_executer->unRegCollector();
        }
        $this->_isEnd=true;
        return true;
    }
    public function __destruct()
    {
        $this->rollback();
    }
}

/** 
 *  @}
 */
?>
