<?php
namespace pylon\impl ;
/** 
 * @brief 
 */
class DiagnoseMonitor
{
    static $contexts=array();
    static public function push($context)
    {
        array_push(self::$contexts,$context);
    }
    static public function pop()
    {
        array_pop(self::$contexts);
    }
    static public function msgs()
    {
        $msgs = array();
        foreach( self::$contexts as $c)
        {
            $msgs[] = "context: [{$c->name}]";
            $msgs = array_merge($msgs,$c->messages);
        }
        return $msgs;
    }
}

/** 
 * @brief  正常退出时DiagnoseContext 对象将被清除,否则将保持记录的信息
 * @remark
 * @code
 * 添加诊断信息
    function test_a()
    {
        $dc = DiagnoseContext::create(__METHOD__);
        $dc->log("test");
        $dc->notkeep();
    }

    //显示诊断信息
    $msgs = DiagnoseMonitor::msgs();
    foreach($msgs as $msg)
    {
        echo "$msg<br>";                                                                         
    }
 * @endcode    
 */
class DiagnoseContext
{
    public $name;
    public $iskeep=true;
    public $messages=array();
    private  function __construct($name)
    {
        $this->name = $name;
        $this->impl = new DContextImpl();
        DiagnoseMonitor::push($this->impl);
    }
    static function create($name)
    {
        return new DiagnoseContext($name);
    }
    public function log($msg)
    {
        $this->impl->log($msg) ;
    }
    public function notkeep()
    {
        $this->iskeep=false;
    }
    public function __destruct()
    {
        if(!$this->iskeep)
            DiagnoseMonitor::pop();
    }

}

class DContextImpl
{
    public $name;
    public $messages=array();
    public function log($msg)
    {
        array_push($this->messages,$msg);
    }
}
