<?php
/**
 * @brief  ID生成器接口
 * @include idgenerator.php
 */
interface XIDGenerator
{
    /**
     * @brief 创建ID
     *
     * @param $idname  ID名称
     *
     * @return
     */
    public function createID($idname='other');
}



/**
 * @ingroup entity
 * @brief  SQL执行器接口定义
 */
interface XSQLExecuter
{
    ///
    /// @brief
    ///
    /// @param $cmd
    ///
    /// @exception DBException
    /// @return array
    ///
    public function query($cmd);
    ///
    /// @brief
    ///
    /// @param $cmd
    ///
    /// @exception DBException
    /// @return object Array
    ///
    public function querys($cmd);
    ///
    /// @brief
    ///
    /// @param $cmd
    /// @param $begin
    /// @param $count
    /// @exception DBException
    /// @return array of array
    ///
    public function querysPage($cmd, $begin, $count);
    /**
     * @brief  Sql 的写操作
     *
     * @param $cmd
     *
     * @return
     */
    public function exeNoQuery($cmd);
    public function beginTrans();
    public function commit();
    public function rollback();
    public function regLogger($writeLogger, $readLogger);
    public function haveCollector();
    public function regCollector($collector);
    public function unRegCollector();
    public function exeNoQueryDirect($cmd);
}

/**
 * @brief  根据 URI 查找实现类
 */
interface XIRouter
{
    /**
     * @brief
     *
     * @param $uri
     *
     * @return
     */
    public function _find($uri);
}
