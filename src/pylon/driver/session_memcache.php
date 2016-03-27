<?php

/** @ingroup extends
 *  @{
 */
/** 
* @brief  基于Memcache 的 Session Driver
 */
class MemcacheSessDriver implements ISessionDriver
{
    static $mem_svc ;
    static $sess_life ;
    public function __construct($memCacheSvc,$life=1440) 
    {
        if(!MemCacheSvc::isEnable()  )
        {
            die("<li>not exist memcache support! ");  
        }
        if(empty($memCacheSvc))
        {
            die( "<li>not exist memcache svc ! " );  
        }
        static::$sess_life = $life;
        static::$mem_svc   = $memCacheSvc;

    }
    public function init()
    {
        session_module_name('user');
        register_shutdown_function('session_write_close');
        $cls = "MemcacheSessDriver" ;
        session_set_save_handler(  
            array($cls,"sess_open"),  
            array($cls,"sess_close"),  
            array($cls,"sess_read"),  
            array($cls,"sess_write"),  
            array($cls,"sess_destroy"),  
            array($cls,"sess_gc"));  
    }

    public static  function sess_open($save_path="", $session_name="")
    {
        return true;
    }
    public static  function sess_close()
    {
        return true;  
    }
    public static  function sess_read($sessionID)
    {
        $memSvc = static::$mem_svc;
        $val = $memSvc->get($sessionID);
        return $val;
    }
    public static  function sess_write($sessionID,$val)
    {  
        $memSvc = static::$mem_svc;
        if(!empty($val))
        {
            $data = $memSvc->get($sessionID);
            if(empty($data) )
            {
                $r = $memSvc->add($sessionID,$val,$sess_life);
            }
            else
            {
                $r=  $memSvc->replace($sessionID,$val,$sess_life);
            }
            return $r;  
        }
        else
        {
            $memSvc->delete($sessionID);
        }
        return false;
    }  
    public static  function sess_destroy($sessionID)
    {
        $memSvc = static::$mem_svc;
        return $memSvc->delete($sessionID);
    }
    public static  function sess_gc($maxlifetime)
    {
        return true;
    }
}


/** 
 *  @}
 */
