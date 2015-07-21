<?php

/**\addtogroup BaseSvc
 * @{
 */
class NullSessionDriver implements ISessionDriver
{
    public function init()
    { }
}
/**
    * @brief  Session 服务
 */
class XSessionSvc
{
    static $_isStart = false;
    private $driver;
    /**
        * @brief  构造函数
        *
        * @param $sessName
        * @param $driver  实现 ISessionDriver 接口的具体Dirver
        *
        * @return
     */
    public function __construct($sessName,$driver)
    {
        $this->driver=$driver;
        $driver->init();
        session_name($sessName);
    }
    static private function ensureStarOnce()
    {
        if (!self::$_isStart)
        {
            session_start();
            self::$_isStart = true;
        }
    }
    static function clear()
    {
        session_destroy();
    }
    public function setSessionID($id)
    {
        session_id($id);
    }
    public function getSessionID()
    {
        self::ensureStarOnce();
        return session_id();
    }
    /**
        * @brief 与 get()成对
        *
        * @param $key
        * @param $var
        *
        * @return
     */
    public function save($key, $var='')
    {
        self::ensureStarOnce();
        $_SESSION[$key] = $var;
        return true;
    }

    /**
        * @brief
        *
        * @param $key
        *
        * @return
     */
    public function get($key)
    {
        self::ensureStarOnce();
        if (isset($_SESSION[$key]))
            return $_SESSION[$key];
        else
            return NULL;
    }

    /**
        * @brief 同 destory() 方法
        *
        * @param $key
        *
        * @return
     */
    public function del($key)
    {
        return $this->destroy($key);
    }
    public function destroy($key)
    {
        self::ensureStarOnce();
        unset($_SESSION[$key]);
        return true;
    }

    public function destroyAll()
    {
        self::ensureStarOnce();
        $_SESSION = array();
        return true;
    }

    public function getAll()
    {
        self::ensureStarOnce();
        return $_SESSION;
    }

/**
 *  @}
 */
}
