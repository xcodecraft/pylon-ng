<?php
namespace Pylon ;

/**\addtogroup Ent
 * @{
 */
/**
* @brief 见UnitWork模式
 */
interface XUnitWork
{
    public function commit();
    public function regLoad($obj);
    public function regAdd($obj);
    public function regDel($obj);
}
class EmptyUnitWork implements XUnitWork
{
    public function commit()
    {}
    public function regLoad($obj)
    {
        return $obj;
    }
    public function regAdd($obj)
    {
        return $obj;
    }
    public function regDel($obj)
    {}
    public function clean()
    {}
}
class ScopeAutoTrans
{
    private $exer;
    private $issuc=false;
    public function __construct($exer)
    {
        $this->exer= $exer;
        $this->exer->beginTrans();
    }
    public function isSuccess()
    {
        $this->issuc=true;
    }
    public function __destruct()
    {
        if($this->issuc)
            $this->exer->commit();
        else
            $this->exer->rollback();
    }
}
/**
    * @brief  UnitWork 实现
 */
class UnitWorkImpl extends ObjUpdater implements XUnitWork
{
    public function __construct()
    {
    }
    public function __destruct()
    {
        parent::__destruct();
    }
    public function dao($obj)
    {
        return DaoFinderUtls::find($obj);
    }
    public function addImpl($obj)
    {
        $this->dao($obj)->add($obj);
    }
    public function delImpl($obj)
    {
        $this->dao($obj)->del($obj);
    }
    public function updateImpl($obj)
    {
        $obj->upgrade();
        $this->dao($obj)->update($obj);
    }

    public function commit()
    {
        $exers = DaoFinderUtls::getExecuterList();
        $this->transCommit($exers);
        $this->clean();
    }
    private function transCommit($exers)
    {
        $transObjs=array();
        foreach($exers as $e)
        {
            $transObjs[] = new ScopeAutoTrans($e);
        }
        $this->commitUpdate( array($this,'addImpl'), array($this,'delImpl'),array($this,'updateImpl'));
        foreach($transObjs as $o)
        {
            $o->isSuccess();
        }
    }
}

/**
 *  @}
 */
