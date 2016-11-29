<?php

use pylon\impl\DaoFinderUtls ;
use pylon\impl\SimpleDaoFactory ;
use pylon\impl\SimpleQueryFactory ;
use pylon\impl\SimpleMapping ;
use pylon\impl\StdMapping ;
use pylon\impl\DaoImp ;
class XEntEnv
{

    /**
     * @brief  在一般情况下，不需要编写Dao和Query的特别实现,可由Facotry来产生
     *
     * @param $daoFactory
     * @param $queryFactory
     *
     * @return
     */
    static public function registerFactory($daoFactory,$queryFactory)
    {
        DaoFinderUtls::registerFactory($daoFactory,$queryFactory);
    }


    static public function registerDao($dao)
    {
        DaoFinderUtls::register($dao);
    }
    static public function registerDaos()
    {
        $daos = func_get_args();
        foreach($daos as $dao)
        {
            DaoFinderUtls::register($dao);
        }
    }

    static public function registerQuerys()
    {
        $querys = func_get_args();
        foreach($querys as $query)
        {
            DaoFinderUtls::registerQuery($query);
        }
    }
    static public function registerQuery($query)
    {
        DaoFinderUtls::registerQuery($query);
    }

    static public function clean()
    {

        DaoFinderUtls::clean();
    }

    /**
     * @brief  simple setup for signle mysql ;
     *
     * @param $sql_exec  数据库执行器 ;
     * @param $idgenter  ID生成器 ;
     *
     * @return
     */
    static public function simpleSetup($sql_exec = null,$idgenter=null)
    {
        if ($sql_exec !== null)
        {
            XBox::regist(XBox::SQLE,$sql_exec,__METHOD__);
            if (XBox::have(XBox::IDG))
            {
                return ;
            }
            if(empty($idgenter))
            {
                XBox::regist(XBox::IDG, new pylon\driver\MySqlIDGenerator($sql_exec),__METHOD__);
            }
            else
            {
                XBox::regist(XBox::IDG, $idgenter,__METHOD__);
            }
        }

        $executer = XBox::must_get(XBox::SQLE);
        static::registerFactory( SimpleDaoFactory::funIns($executer), SimpleQueryFactory::funIns($executer));
    }
    /**
        * @brief  配置Dao
        *
        * @param $cls  类名
        * @param $table  表名
        * @param $mapping  映射方式: simple,std
        * @param $hashfun  分表方式
        *
        * @return
     */
    static public function configDao($cls,$table,$mapping="simple",$hashfun=null)
    {
        $executer = XBox::get(SQLE,"/$cls");
        $map_ins  = SimpleMapping::ins();
        if ($mapping === "std")
        {
            $map_ins =  StdMapping::ins();
        }
        $dao   = new DaoImp($cls,$executer,$table,$map_ins,$hashfun);
        static::registerDao($dao);
    }

    static public function query($clsName)
    {
        return DaoFinderUtls::query($clsName);
    }
    static public function dao($cls)
    {
        return DaoFinderUtls::find($cls);
    }


}

