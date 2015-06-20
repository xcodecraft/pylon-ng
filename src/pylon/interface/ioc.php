<?php

/**
 * @ingroup aop
 * @brief  拦截器管理器
 */
class XAop
{

	static public $rules= null ;

	/**
	 * @brief  获得$pos 拦截点的拦截器规则集合
	 *
	 * @param $pos  拦截点
	 *
	 * @return
     * @remark
     * @code
     * 示例:
        XAop::append_by_match_name(".*", new AutoCommit());
    * @endcode
	 */
	static public function rule()
	{

		if(self::$rules == null )
        {
			self::$rules = new XAopRuleSet();
        }
        return  self::$rules ;
	}
    static function __callStatic($name,$arguments)
    {
        call_user_func_array( array(self::rule(),$name),$arguments );
    }
    static private function logger()
    {
        static $log_ins =null;
        if ($log_ins === null)
            return new logger("_pylon");
        return $log_ins;
    }
	static public function using($conf)
	{
		return self::rule()->using($conf);
	}

	static public function using_all($pos)
	{
		return self::$rules->using_all();
	}

}


/**
 * @ingroup assembly
 * @brief  框架容器
 */
class XBox
{
    const ROUTER      = 'router'  ;
    const DAO              = 'dao'  ;
    const QUERY            = 'query' ;
    const SQLE             = 'SQLExecuter' ;
    const IDG              = 'IDGenterService' ;
    static $_objs    = array();
    static $_wheres  = array();
    static public function regist_where($call_level)
    {
        $bt     = debug_backtrace();
        $where  = '';
        if (count($bt) >=$call_level)
        {
            $frame  = $bt[$call_level];
            $where  = $frame['file']   . " : " . $frame['line'];
        }
        return $where ;
    }

    /**
     * @brief  替换原来的注册对象
     *
     * @param $key
     * @param $obj
     * @param $space  = '/'
     *
     * @return
     */
    static public function replace($key,$obj,$where,$space='/')
    {
        DBC::requireNotNull($obj,'$obj');
        self::registImpl($key,$obj,$space,$force=true,$were);
    }
    static public function regist($key,$obj,$where,$space='/')
    {
        DBC::requireNotNull($obj,'$obj');
        self::registImpl($key,$obj,$space,$force=false,$where);
    }
    /**
     * @brief  注册
     *
     * @param $key
     * @param $obj
     * @param $space  空间的意义在于，可以针对不同的实体对象，提供不同的数据库的访问器 ;
     * @param $force
     * @param $where
     *
     * @return
     */
    static private function registImpl($key,$obj,$space='/',$force=false,$where='')
    {
        DBC::requireNotNull($key,'$key');
        if (! isset(self::$_objs[$key]))
            self::$_objs[$key] = array();
        $space_obj = &self::$_objs[$key];
        if (! isset(self::$_wheres[$key]))
            self::$_wheres[$key] = array();
        $space_where = &self::$_wheres[$key];
        if($force === false)
        {
            if( isset($space_obj[$space]))
            {
                $first_where = $space_where[$space];
                throw new LogicException( "have regist $key obj in $space , first regist at [$first_where]");
            }
        }
        $space_obj[$space]      = $obj;
        $space_where[$space]    = $where;
    }
    static public function registByCLS($obj,$space='/',$force=false)
    {
        self::regist(get_class($obj),$obj,$space,$force);
    }

    static public function get($key,$space='/')
    {
        DBC::requireNotNull($key,'$key');

        while( true )
        {
            if (! isset(self::$_objs[$key]))
                self::$_objs[$key] = array();
            $space_obj = &self::$_objs[$key];
            if(isset($space_obj[$space]))
            {
                return $space_obj[$space];
            }
            else
            {
                if ($space === '/'  || $space == null || $space == ""  )
                    return null ;
                else
                    $space = dirname($space);

            }
        }
    }
    static public function space_objs($key)
    {
        if (! isset(self::$_objs[$key]))
            self::$_objs[$key] = array();
        $space_obj = &self::$_objs[$key];
        return $space_obj ;
    }
    static public function space_keys($key)
    {
        return array_keys(self::space_objs($key));
    }

    static public function must_get($key,$space='/')
    {
        $found = self::get($key,$space);
        if ($found === null)
            throw new LogicException("unfound $key obj in $space");
        return $found ;

    }
    static public function clean($key=null)
    {
        if ( $key == null)
            self::$_objs = array();
        else if ( isset(self::$_objs[$key]))
            self::$_objs[$key] = array();
    }
}

/**
 * @ingroup assembly
 * @brief
 * @include assembly.php
 */
interface XAssembly
{
    public function setup() ;
}

