<?php
/** \addtogroup Cache 
 *  @{
 */


/** 
 * @brief  接口定义
 */
interface  Cachable
{
    /** 
     * @brief get cached value
     * 
     * @param $key  string 
     * 
     * @return success return obj ,else return null;
     */
    public function get($key);
    /** 
     * @brief 
     * 
     * @param $key 
     * @param $value  obj not need serialize!
     * @param $expire  seconds
     * 
     * @return void  
     */
    public function set($key, $value, $expire=0);
    /** 
     * @brief 
     * 
     * @param $key 
     * 
     * @return bool  true or false; 
     */
    public function delete($key);
    /** 
     * @brief 
     * 
     * @return bool true or false; 
     */
    public function flush();

    public function statusInfo();
}

/** 
 * @brief 空Cache驱动
 */
class NullDriver implements Cachable
{
    public function get($key)
    {
        return null;
    }
    public function set($key, $value, $expire=0)
    {
        return true;
    }

    public function delete($key)
    {
        return true;
    }
    public function flush()
    {
        return true;
    }
    public function statusInfo()
    {
        return "this is NullDriver Object";
    }
}

/** 
 * @brief   基于APC实现的Cache Driver
 */
class APCDriver implements Cachable
{
    public function get($key)
    {
        return unserialize(apc_fetch($key));
    }

    public function set($key, $value, $expire=0)
    {
        return apc_store($key, serialize($value), $expire);
    }

    public function delete($key)
    {
        apc_delete($key);
    }
    public function flush()
    {
        return false;
    }
    static public function isEnable()  
    {
        return function_exists('apc_fetch');
    }

    public function statusInfo()
    {
        return "this is APCDriver Object";
    }
}


/** 
 * @brief  Cachable implements by Memcached
 */
class MemCacheSvc implements Cachable
{
    private $_memcache;
    private $_servers;
    private $_compress=false;
    private $_prefix;

    public function __construct($servers,$prefix="_")
    {
        $this->_servers = $servers;
        $this->_prefix  = $prefix;
        $this->enable();
    }
    static public function localhostConf()
    {
        $conf[] = array('host'=>'localhost','port'=>'11211');
        return $conf;
    }

    static public function isEnable()  
    {

        return function_exists('memcache_connect');
    }

    public function echoMemCacheInfo()
    {
        echo $this->statusInfo();
    }

    public function statusInfo()
    {
        $info="This is Memached!";
        $info .= "version: ". $this->_memcache->getVersion() . "  <br>\n";
        foreach($this->_servers as $svr)
        {
            $info .= "status:" .$this->_memcache->getServerStatus($svr['host'], $svr['port'])." <br>\n";
        }
        return $info;
    }
    public function __destruct()
    {
        $this->disable();
    }
    public function get($key)
    {
        $key = $this->_prefix . $key ;
        $obj = $this->_memcache->get($key);
        return unserialize($obj);
    }

    public function add($key, $value, $expire=0)
    {
        $key = $this->_prefix . $key ;
        return $this->_memcache->add($key, serialize($value), $this->_compress, $expire);
    }

    public function set($key, $value, $expire=0)
    {
        $key = $this->_prefix . $key ;
        return $this->_memcache->set($key, serialize($value), $this->_compress, $expire);
    }

    public function replace($key, $value, $expire=0)
    {
        $key = $this->_prefix . $key ;
        return $this->_memcache->replace($key, serialize($value), $this->_compress, $expire);
    }

    public function delete($key)
    {
        $key = $this->_prefix . $key ;
        return $this->_memcache->delete($key);
    }

    public function flush()
    {
        return $this->_memcache->flush();
    }
    public function enable()
    {
        $this->_memcache = new Memcache();
        foreach($this->_servers as $server)
        {
            $persistent = isset($server['persistent']) ? $server['persistent'] : true;
            $this->_memcache->addServer($server['host'], $server['port'], $persistent);
        }

        $this->_compress = false;
    }
    public function disable()
    {
        return $this->_memcache->close();
    }
}

/** 
 * @brief 用于统一设定失效规则
 */
class  CacheStg 
{
    public $expireSet;
    public $getkefun;
    public function __construct($expire,$getkeyfun=null) 
    {
        $this->expireSet = $expire;
        $this->getkeyfun = $getkeyfun;
    }
    public function getkey($value)
    {
        $fun=$this->getkeyfun;
        return $fun($value);
    }
}

/** 
 * @brief  可以不同Cache Driver 实现的 PylCacheSvc 
 */
class  PylCacheSvc
{
    /** 
     * @brief 
     * 
     * @param $name 
     * @param $driver 
     * @param $stg  CacheStg
     * 
     * @return 
     */
    public function __construct($name,$driver,$stg)
    {
        if(!is_string($name) || $name == "array")
        {
            assert("name is not string ");
            exit; 
        }

        $this->name=$name;
        $this->driver= $driver;
        $this->stg=$stg;
        $this->ver = $this->initVer();

    }
    public function initVer()
    {
        $ver = $this->driver->get($this->name);
        if(!$ver) 
        {
            $ver = 1 ;
            $this->driver->set($this->name,$ver);
        }
        return $ver;
    }
    public function clear()
    {
        $this->ver +=1;
        $this->driver->set($this->name,$this->ver);
    }
    /** 
     * @brief 
     * 
     * @param $key 
     * @param $value 
     * @param $expire  如果为null,则使用构造对象中传入的stg
     * 
     * @return 
     */
    public function set($key,$value,$expire=null)
    {
        $key= "{$this->name}_{$this->ver}_$key ";
        if($key ==null) 
        {
            $key = $this->stg->getkey($value);
        }
        if($expire ==null)
        {
            $expire= $this->stg->expireSet;
        }
        $this->driver->set($key,$value,$expire);
    }
    public function get($key)
    {
        $key= "{$this->name}_{$this->ver}_$key ";
        $obj = $this->driver->get($key);
        return $obj;
    }
    public function delete($key)
    {
        return $this->driver->delete($key);
    }
    public function __call($name,$params) 
    {
        return  call_user_func_array(array($this->driver,$name),$params);
    }
}

/** 
 * @brief 兼容 同 CacheSvc
 */
class  CacheSvcWarpper extends PylCacheSvc
{
}


/** 
 * @brief   
 */
class  CacheProxy
{
    private  $cache;
    public   $dao;
    private  $prefix;

    /** 
     * @brief  构造函数
     * 
     * @param $cache  具体的Cache 
     * @param $dao    访问数据的服务
     * @param $prefix 
     * 
     * @return 
     */
    public function __construct($cache,$dao,$prefix)
    {
        $this->cache=$cache;
        $this->dao=$dao;
        $this->prefix = $prefix;
    }
    public function __get($name)
    {
        $key = md5($this->prefix."__get".serialize($name));
        $obj =  $this->cache->get($key);
        if($obj) return  $obj;
        $obj = $this->dao->$name;
        $this->cache->set($key,$obj);
        return $obj;
    }
    public function __call($name,$params) 
    {
        $key = md5($this->prefix.$name.serialize($params));
        $obj =  $this->cache->get($key);
        if($obj) return  $obj;
        $obj = call_user_func_array(array($this->dao,$name),$params);
        $this->cache->set($key,$obj);
        return $obj;

    }
}

class InnerCache implements Cachable
{
    public $objmap=array();
    public function get($key)
    {
        if(isset($this->objmap[$key]))
            return $this->objmap[$key];
        return null;
    }
    public function set($key, $value, $expire=0)
    {
        $this->objmap[$key]=$value;
    }
    public function delete($key)
    {
        unset($this->objmap[$key]);
    }
    public function flush()
    {
        $this->objmap=array();
    }
    public function statusInfo()
    {}
}

class CacheSpace
{
    public $driver = null;
    public $stg    = null;
    public $caches = array();
    public function __construct($driver,$stg)
    {
        $this->driver= $driver;
        $this->stg   = $stg;
    }
    public function find($name)
    {
        $name = strtolower($name);
        if(array_key_exists($name,$this->caches))
        {
            return $this->caches[$name];
        }
        $cache = new CacheSvcWarpper($name,$this->driver,$this->stg);
        $this->caches[$name] = $cache;
        return $cache;
    }
    public function clears()
    {
        $names = func_get_args(); 
        $this->clearArr($names);
    }
    public function clearArr($arr)
    {
        foreach($arr as $n)
        {
            $n= strtolower($n);
            $this->find($n)->clear();
        }
    }
    public function clearAll()
    {
        foreach($this->caches as $c)
        {
            $c->clear();
        }
        $this->caches = array();
    }
}

class CacheSapce extends CacheSpace
{}
class CacheAdmin
{

    static public $netCacheSpace= null;
    static public $innerCacheSpace=null;
    static public $caches = array();
    static public function setup($driver,$stg)
    {
        static::$netCacheSpace   = new CacheSapce($driver,$stg);
        static::$innerCacheSpace = new CacheSapce(new InnerCache(),$stg);
    }

}

/** 
 *  @}
 */
?>

