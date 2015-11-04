<?php
namespace Pylon ;

/**\addtogroup DBA
 * @{
 */

/**
 * @brief
 * @TODO: 是不是没用?
 */
class Express
{
    var $_sql;
    var $_selfStg=null;
    public function __construct($stg=null)
    {
        $this->_sql = "";
        $this->_selfStg = $stg;
    }
    public function generateSql($stg )
    {
        return $this->_sql;
    }
    /**
     * @brief  ÒÔÓëµÄ¹ØÏµÆ´½Ó¶à¸ö×ÓÌõ¼þ
     *
     * @return
     */
    static public function ands()
    {
        $args = func_get_args();
        return new Ands($args);
    }
    /**
     * @brief  ÒÔ»òµÄ¹ØÏµÆ´½Ó¶à¸ö×ÓÌõ¼þ
     *
     * @return
     */
    static public function ors()
    {
        $args = func_get_args();
        return new Ors($args);
    }
    static public function GreatEqColumn($column1, $column2)
    {
        return new GreatEqColumn($column1, $column2);
    }
    static public function LessEqColumn($column1, $column2)
    {
        return new LessEqColumn($column1, $column2);
    }
    static public function ge($column, $value,$stg=null)
    {
        return new GreatEq($column, $value,$stg);
    }
    static public function le($column, $value,$stg=null)
    {
        return new LessEq($column, $value,$stg);
    }
    static public function eq($column, $value,$stg=null)
    {
        return new Eq($column, $value,$stg);
    }
    static public function eqColumn($column1, $column2,$stg=null)
    {
        return new EqColumn($column1, $column2,$stg);
    }
    static public function gt($column, $value,$stg=null)
    {
        return new Great($column, $value,$stg);
    }
    static public function lt($column, $value,$stg=null)
    {
        return new Less($column, $value,$stg);
    }
    /**
     * @brief Ô­ÉúµÄSQLÓï¾ä
     *
     * @param $cmd
     *
     * @return
     */
    static public function txt($cmd)
    {
        return new TxtExpress($cmd);
    }
    function needGe($stgs,$column,$value)
    {
        if($this->_selfStg !=null)
            return $this->_selfStg->needGenerate($column,$value);
        return $stgs->needGenerate($column,$value);
    }

}


/**
 * @brief
 */
class TxtExpress extends Express
{
    private $_txtSql = null;
    public function __construct($txtSql)
    {
        $this->_txtSql = $txtSql;
    }
    function generateSql($stgs )
    {
        return $this->_txtSql;
    }
}

/**
 * @brief
 */
class Eq extends Express
{
    var $_column;
    var $_value;
    public function __construct($column, $value,$stg=null)
    {
        $this->_column = $column;
        $this->_value = $value;
        $this->_colstg = $stg;
        parent::__construct($stg);
    }
    public function generateSql($stgs)
    {
        if($this->needGe($stgs,$this->_column,$this->_value))
            $this->_sql = sprintf("(%s = \"%s\")", $this->_column, $this->_value);
        return $this->_sql;
    }
}

/**
 * @brief
 */
class GreatEq extends Express
{
    var $_column;
    var $_value;
    function GreatEq($column, $value,$stg=null)
    {
        $this->_column = $column;
        $this->_value = $value;
        parent::__construct($stg);
    }
    public function generateSql($stgs)
    {
        if($this->needGe($stgs,$this->_column,$this->_value))
        {
            $this->_sql = sprintf("(%s >= \"%s\")", $this->_column, $this->_value);
        }
        return $this->_sql;

    }
}

/**
 * @brief
 */
class LessEq extends Express
{
    var $_column;
    var $_value;
    function LessEq($column, $value,$stg=null)
    {
        $this->_column = $column;
        $this->_value = $value;
        $this->_colstg =$stg;
        parent::__construct($stg);
    }
    public function generateSql($stgs)
    {
        if($this->needGe($stgs,$this->_column,$this->_value))
        {
            $this->_sql = sprintf("(%s <= \"%s\")", $this->_column, $this->_value);
        }
        return $this->_sql;
    }
}

/**
 * @brief
 */
class Great extends Express
{
    var $_column;
    var $_value;
    function Great($column, $value,$stg=null)
    {
        $this->_column = $column;
        $this->_value = $value;
        parent::__construct($stg);
    }
    public function generateSql($stgs)
    {
        if($this->needGe($stgs,$this->_column,$this->_value))
        {
            $this->_sql = sprintf("(%s > \"%s\")", $this->_column, $this->_value);

        }
        return $this->_sql;
    }
}

/**
 * @brief
 */
class Less extends Express
{
    var $_column;
    var $_value;
    function Less($column, $value,$stg=null)
    {
        $this->_column = $column;
        $this->_value = $value;
        parent::__construct($stg);
    }
    public function generateSql($stg)
    {
        if($this->needGe($stg,$this->_column,$this->_value))
        {
            $this->_sql = sprintf("(%s < \"%s\")", $this->_column, $this->_value);
        }
        return $this->_sql;
    }
}

/**
 * @brief
 */
class Ands extends Express
{
    var $_items;
    public function __construct($args)
    {
        $this->_items = $args;
        parent::__construct(null);
    }
    public function generateSql($stgs)
    {
        $sqls = array();
        foreach($this->_items as $item)
        {
            if(is_object($item))
            {
                $sql = $item->generatesql($stgs);

                if(!empty($sql))
                    $sqls[]  = $sql;

            }
            else
            {
                if($stgs->needGenerate("TXT",$item))
                {
                    $sqls[] = $item;
                }
            }
        }
        return JoinUtls::jarray(' and ',$sqls);
    }
}

/**
 * @brief
 */
class Ors extends Express
{
    var $_items;
    public function __construct($args)
    {
        $this->_items = $args;
        parent::__construct(null);
    }
    public function generateSql($stgs)
    {

        $sqls = array();
        foreach($this->_items as $item)
        {
            if(is_object($this->_items[$i]))
            {
                $sqls[]  = $this->_items[$i]->generatesql($stgs);

            }
            else
                $sqls[] = $this->_items[$i];
        }
        return array2str($sqls,'or');
    }
}


/**
 * @brief
 */
class EqColumn extends Express
{
    var $_column1;
    var $_column2;
    public function __construct($column1, $column2)
    {
        $this->_column1 = $column1;
        $this->_column2 = $column2;

        parent::__construct(null);
    }
    function generateSql($stg)
    {
        return $this->_sql = sprintf("(%s = %s)", $this->_column1, $this->_column2);

    }
}
/**
 * @brief
 */
class GreatEqColumn extends Express
{
    var $_column1;
    var $_column2;
    public function __construct($column1, $column2)
    {
        $this->_column1 = $column1;
        $this->_column2 = $column2;

        parent::__construct(null);
    }
    function generateSql($stg)
    {
        return $this->_sql = sprintf("(%s >= %s)", $this->_column1, $this->_column2);

    }
}
/**
 * @brief
 */
class LessEqColumn extends Express
{
    var $_column1;
    var $_column2;
    public function __construct($column1, $column2)
    {
        $this->_column1 = $column1;
        $this->_column2 = $column2;

        parent::__construct(null);
    }
    function generateSql($stg)
    {
        return $this->_sql = sprintf("(%s <= %s)", $this->_column1, $this->_column2);

    }
}


/**
 * @brief
 */
class GenerateStg
{
    function GenerateStg()
    {
    }
    function needGenerate($column, $value)
    {
        assert(false);
    }
}
/**
 * @brief
 */
class NULLStg extends GenerateStg
{
    function NULLStg()
    {
    }
    function needGenerate($column, $value)
    {
        return true;
    }
}
/**
 * @brief
 */
class NULLColumnStg extends GenerateStg
{
    function NULLColumnStg()
    {
    }
    function needGenerate($column, $value)
    {
        if($value === "" || $value === NULL)
            return false;

        return true;
    }
    public static function ins()
    {
        static $stg =  null;
        if($stg === null)
            $stg = new NULLColumnStg();

        return $stg;
    }
}
/**
 * @brief
 */
class ValueColumnStg extends GenerateStg
{
    var $_value ;
    function ValueColumnStg($value)
    {
        $this->_value =$value ;
    }
    function needGenerate($column, $value)
    {
        if($value === $this->_value || $value === NULL)
            return false;
        return true;
    }
}
/**
 * @brief
 */
class LessValueColunmStg extends GenerateStg
{
    var $_value ;
    function LessValueColumnStg($value)
    {
        $this->_value =$value ;
    }
    function needGenerate($column, $value)
    {
        if($value === $this->_value || $value === NULL)
            return false;
        return true;
    }
}
/**
 * @brief
 */
class ColumnStg extends GenerateStg
{
    var $_columns;
    function ColumnStg()
    {
        $this->_columns = func_get_args();
    }
    function needGenerate($column, $value)
    {
        if(in_array($column, $this->_columns))
            return false;

        return true;
    }

}

/**
 * @brief  ×Ô¶¨º¯ÊýµÄÀ©Õ¹
 */
class UserFunStg extends GenerateStg
{
    var $_fun;
    public function __construct($fun)
    {
        $this->_fun = $fun;
    }
    function needGenerate($column, $value)
    {
        $fun= $this->_fun;
        return $fun($column,$value);
    }
}
/**
 * @brief
 */

function pyl_stg_not_empty($col,$val)
{
    return !empty($val);
}

function pyl_stg_not_null($col,$val)
{
    return !is_null($val);
}

class StgUtls
{

    static public function filterEmpty()
    {
        $fun =  function ($col,$val) {
            return !empty($val);
        };
        return new UserFunStg($fun);
    }
    static public function filterNull()
    {

        $fun =  function ($col,$val) {
            return !is_null($val);
        };
        return new UserFunStg($fun);
    }
}


/**
 *  @}
 */

?>
