<?php
namespace Pylon ;
use \XIDGenerator as XIDGenerator  ;
/** 
 * @ingroup extends
 * @brief  基于Mysql实现的IDGenterService
 */

class MySqlIDGenerator implements XIDGenerator
{
    private $_executer;
    private $enable_second = false;
    public static $ENABLE_DOUBLE_MASTER = false;
    private $_idsets = array() ;

    /** 
     * @brief 
     * 
     * @param $executer  数据库执行器
     * @param $clone     是否独立于事务，默认为 true
     * @param $second    双 Master模式下 ，true 为双数ID, false 为 单数。
     * 
     * @return 
     */
    public function __construct($executer,$clone=true,$second=false)
    {
        if(!$clone){
            $this->_executer = $executer; 
        }
        else {
            $this->_executer = clone $executer; 
        }

        if( self::$ENABLE_DOUBLE_MASTER && $second) {
            $this->_enable_second = true;
        }

    }

    /** 
     * @brief  打开双 Master支持
     * 
     * @return 
     */
    public static function ENABLE_DOUBLE_MASTER()
    {
        self::$ENABLE_DOUBLE_MASTER = true;
    }

    private function readIDSets()
    {
        $cmd = "select * from " . ($this->_enable_second ? 'second_id_genter' : 'id_genter');
        $rows = $this->_executer->querys($cmd);
        if($rows)
        {
            foreach($rows as $row)
            {
                $this->_idsets[$row['obj']]['curID'] = $row['id'];
                $this->_idsets[$row['obj']]['maxID'] = $row['id'];
                $this->_idsets[$row['obj']]['step'] = $row['step'];
            }
        }
    }

    public function createID($idname='other')
    {
        if(empty($this->_idsets))
        {
            $this->readIDSets();
        }
        if(array_key_exists($idname, $this->_idsets))
        {
            return $this->createIDimp($idname);
        }
        else
        {
            return $this->createIDimp('other');
        }

    }

    private function createIDimp($objName)
    {
        if($this->_idsets[$objName]['curID'] == $this->_idsets[$objName]['maxID'] )
        {
            if(!$this->getBatchID($objName)) return false;
        }

        $this->_idsets[$objName]['curID'] += self::$ENABLE_DOUBLE_MASTER ? 2 : 1;
        $createdID = $this->getID($this->_idsets[$objName]['curID']);
        return $createdID;
    }

    private function getID($id)
    {
        if ( self::$ENABLE_DOUBLE_MASTER )
        {
            if ( $this->_enable_second ) {
                return ($id & 1) ? ($id - 1) : $id;
            }
            else {
                return ($id & 1) ? $id : ($id - 1);
            }
        }
        else {
            return $id;
        }
    }

    public function getBatchID($objName)
    {
        if ( self::$ENABLE_DOUBLE_MASTER ) {
            $step = $this->_idsets[$objName]['step'] * 2;
        }
        else {
            $step = $this->_idsets[$objName]['step'];
        }

        $cmd = "UPDATE " . ($this->_enable_second ? 'second_id_genter' : 'id_genter') . " SET id = LAST_INSERT_ID(id + $step) where obj= '$objName';";
        if($this->_executer->exeNoQueryDirect($cmd))
        {
            $cmd = 'SELECT LAST_INSERT_ID() as id;';
            $row = $this->_executer->query($cmd);
            if($row)
            {
                $this->_idsets[$objName]['maxID'] = $row['id'];
                $this->_idsets[$objName]['curID'] = $row['id'] - $step;
                return true;
            }
        }
        return false;
    }
}
class IDGenterSvcImp extends MySqlIDGenerator
{}

