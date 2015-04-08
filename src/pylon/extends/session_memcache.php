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
        self::$sess_life = $life;
        self::$mem_svc   = $memCacheSvc;

    }
    public function init()
    {
        session_module_name('user');
        register_shutdown_function('session_write_close');
        session_set_save_handler(  
            array("MemcacheSessDriver","sess_open"),  
            array("MemcacheSessDriver","sess_close"),  
            array("MemcacheSessDriver","sess_read"),  
            array("MemcacheSessDriver","sess_write"),  
            array("MemcacheSessDriver","sess_destroy"),  
            array("MemcacheSessDriver","sess_gc"));  
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
        $memSvc = self::$mem_svc;
        $val = $memSvc->get($sessionID);
        return $val;
    }
    public static  function sess_write($sessionID,$val)
    {  
        $memSvc = self::$mem_svc;
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
        $memSvc = self::$mem_svc;
        return $memSvc->delete($sessionID);
    }
    public static  function sess_gc($maxlifetime)
    {
        return true;
//        $memSvc = self::$mem_svc;
//        return $memSvc->flush();
    }
}


/** 
 *  @}
 */
?>
