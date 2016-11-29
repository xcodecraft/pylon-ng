<?php

namespace pylon\impl ;
use XProperty ;
use XBox ;
use XDBC ;
use XEntity ;
use XSetting ;
use XDBCException ;

class DaoFinderUtls
{
    const FACTORY='##factory';

    private static $binder=null;

    static public function regBinder($binder)
    {
        static::$binder = $binder;
    }
    static public function clearBinder()
    {
        static::regBinder(null);
    }

    static protected function findByCls($clsName)
    {
        return static::get_impl(XBox::DAO,$clsName);
    }

    static public function getExecuterList()
    {
        return array_values(XBox::space_objs(XBox::SQLE));
    }

    static public function registerFactory($daoFactory,$queryFactory)
    {
        XBox::regist(static::FACTORY,$daoFactory, __METHOD__,"/".XBox::DAO);
        XBox::regist(static::FACTORY,$queryFactory, __METHOD__,"/".XBox::QUERY);
    }

    static public function get_impl($key,$cls)
    {
        $cls  =  strtolower($cls);
        $obj  = XBox::get($key,"/$cls");
        if($obj !== null)
        {
            return $obj ;
        }

        $factory = XBox::get(static::FACTORY,"/$key");
        if($factory !== null)
        {
            $obj = call_user_func($factory,$cls);
            static::regist_impl($key,$cls,$obj);
            return $obj;
        }


        $names = Prompt::recommend($cls,array_keys(XBox::space_keys($key)));
        $str   = JoinUtls::jarray(',',$names);
        XDBC::unExpect("$cls $key unfoud","maybe in $str");
    }

    static public function query($clsName)
    {
        $query = static::get_impl(XBox::QUERY,$clsName);
        if(static::$binder!= null)
        {
            return static::$binder->proxy($clsName,$query);
        }
        return $query;
    }

    static public function find_($obj)
    {
        $dao=null;
        if(is_object($obj))
        {
            $obj = get_class($obj);
        }
        return static::findByCls($obj);
    }

    static public function find($obj)
    {
        $dao = static::find_($obj);
        if(static::$binder!= null)
        {
            return static::$binder->proxy( is_string($obj)? $obj: get_class($obj), $dao);
        }
        return $dao;
    }


    static private function registerExer($cls ,$exec)
    {
        $root_exec = XBox::get(XBox::SQLE);
        if ( $exec !== $root_exec)
        {
            XBox::regist(XBox::SQLE,$exec,__METHOD__ . ":$cls");
        }
    }
    static public function register($dao)
    {

        $clsName = strtolower($dao->cls);
        static::regist_impl(XBox::DAO,$clsName,$dao);
    }

    static public function regist_impl($key,$cls,$obj)
    {
        static::registerExer($cls,$obj->getExecuter());
        XBox::regist($key,$obj,__METHOD__,"/$cls");
    }
    static public function registerQuery($query)
    {
        $clsName = strtolower($query->getRegName());
        static::regist_impl(XBox::QUERY,$clsName,$query);
    }

    static public function registerAll($dao,$query)
    {
        if($dao !=null)
        {
            static::register($dao);
        }
        if($query !=null)
        {
            static::registerQuery($query);
        }
    }
    static public function clean()
    {
        XBox::clean(XBox::DAO);
        XBox::clean(XBox::QUERY);
        XBox::clean(static::FACTORY);
    }

}
