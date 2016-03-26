<?php
/**\addtogroup utls
 * @{
 */
class EchoLogger
{
    public function log($msg)
    {
        echo "\nlog: $msg\n<br>";
    }
}
class MemCollectLogger
{
    public $logMsgs=array();
    public function log($msg)
    {
        $this->logMsgs[]=$msg;
    }
}
class CombinLogger
{
    private $logers=null;
    public function __construct()
    {
        $this->logers =  func_get_args();
    }
    public function log($msg)
    {
        foreach($this->logers as $logger)
        {
            $logger->log($msg);
        }
    }
}
class ScopeExec
{
    protected $endFun;
    protected $arg;
    protected function __construct($beginFun,$endFun,$arg)
    {
        $this->endFun=$endFun;
        $this->arg=$arg;
        $beginFun($this->arg);
    }
    public function __destruct()
    {
        $endFun=$this->endFun;
        $endFun($this->arg);
    }
}


/**
 *  @}
 */

