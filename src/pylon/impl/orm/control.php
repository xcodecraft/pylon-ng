<?php
/**\addtogroup Ent
 * @{
 */
interface IControl
{
    const ALLOW=true;
    const FORBID=false;
    public function _need($name,$params);
    public function _before($name,$params);
    public function _after($name,$params);
    public function _other($name,$params);
};

class EmptyControl implements IControl
{
    public function _need($name,$params)
    {
        return false;
    }
    public function _before($name,$params)
    {
    }
    public function _after($name,$params)
    {
    }

    public function _other($name,$params)
    {
    }
}


class XFunctionControl extends EmptyControl
{
    public function __construct($default,$excepts)
    {
        $this->reset($default,$excepts);

    }
    public function _need($name,$params)
    {
        $iname = strtolower($name);
        if(in_array($iname ,$this->excepts))
        {
            return ! $this->default ;
        }
        return $this->default;

    }
    public function reset($default,$excepts)
    {
        $this->default= $default;
        $this->excepts = array_change_key_case($excepts) ;
    }
}


/**
 *  @}
 */
