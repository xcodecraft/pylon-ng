<?php
namespace pylon\impl ;

/**\addtogroup Proxy
 * @{
 */


/**
 * @brief
 */
class  LDProxy
{
    private  $fun;
    private  $args;
    private  $obj = null;

    public function __construct($fun,$args)
    {
        DBC::requireNotNull($fun);
        $this->fun = $fun;
        $this->args=$args;
    }
    public function loadObj()
    {
        $this->obj=call_user_func($this->fun,$this->args);
        DBC::requireNotNull($this->obj);
    }
    public function getObj()
    {
        if($this->obj == null)
            $this->loadObj();
        return $this->obj;
    }
    public function __get($name)
    {
        if($this->obj==null)
            $this->loadObj();
        return $this->obj->$name;
    }
    public function __set($name,$value)
    {
        if($this->obj==null)
            $this->loadObj();
        $this->obj->$name=$value;
    }
    public function __call($name,$params)
    {
        if($this->obj==null)
            $this->loadObj();
        $obj = call_user_func_array(array($this->obj,$name),$params);
        return $obj;

    }

}


/**
 *  @}
 */
