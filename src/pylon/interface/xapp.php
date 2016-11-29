<?php
use pylon\impl\UnitWorkImpl ;


/**
 * @ingroup entity
 * @brief  应用Session
 */
class XAppSession
{
    private     $isCancle=true;
    protected   $unitWork=null;
    protected   $useDaos=array();
    static public function begin($uwork=null)
    {
        $where_begin = XBox::regist_where(1);
        if($uwork === null)
        {
            return  new XAppSession(new UnitWorkImpl(),$where_begin);
        }
        return new XAppSession($uwork,$where_begin);
    }

    protected function __construct($unitWork,$where_begin)
    {
        $this->unitWork = $unitWork;

        XBox::regist("unitwork",$unitWork,$where_begin );
    }

    public function __destruct()
    {
        if($this->isCancle )
        {
            XBox::clean("unitwork");
            $this->unitWork = null;
        }
    }
    public function commit()
    {
        $this->unitWork->commit();

        $this->isCancle=true;
    }
}
