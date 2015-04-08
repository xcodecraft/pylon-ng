<?php
interface  LCachable  //LCache 表达是轻量级的Cache;
{
    public function get($key);
    public function set($key, $value, $expire=0);
    public function delete($key);
    public function flush();
    public function increment($key,$expire=600,$v=1);
    public function statusInfo();
}

class LArrayDriver implements LCachable
{
    public $data=array();
    public $accessCnt=0;
    public function get($key)
    {
        $this->accessCnt +=1;
        if(is_string($key))
            return isset($this->data[$key])? $this->data[$key]:false; 
        if(is_array($key))
        {
            $datas ;
            foreach($key as $i)
            {
                if(isset($this->data[$i]))
                    $datas[$i] = $this->data[$i];
            }
        }
        return $datas;
    }
    public function set($key, $value, $expire=0)
    {
        $this->accessCnt +=1;
        $this->data[$key] = $value;
    }
    public function delete($key)
    {
        unset($this->data[$key]);
    }
    public function flush()
    {
        $this->data =array();
    }
    public function increment($key,$expire=600,$v=1)
    {
        $this->accessCnt +=1;
        if(!isset($this->data[$key]))
        {
            $this->data[$key] = 0;
        }
        $this->data[$key] += $v;
        return $this->data[$key];
    }
    public function statusInfo()
    {
        return $this->data;
    }
}

/** 
 * @brief  CacheSvc implement by APC
 */

/** 
 * @brief  CacheSvc  implement by  eaccelerator
 * @example cachesvc_test.php
 */
class LEADriver implements LCachable
{
    static public function isEnable()  
    {
        return function_exists('eaccelerator_get');
    }
    public function get($key)
    {
        if(is_string($key))
        {
            $ret = eaccelerator_get($key);
            return $ret ? $ret :false;
        }
        if(is_array($key))
        {
            $datas ;
            foreach($key as $i)
            {
                $ret= eaccelerator_get($i);
                $datas[$i] = $ret ? $ret : false; 
            }
            return $datas;
        }
        return false;
    }

    public function set($key, $value, $expire=600)
    {
        $ret = eaccelerator_put($key, $value, $expire);
        return $ret;
    }

    public function delete($key)
    {
        eaccelerator_rm($key);
    }
    public function flush()
    {
        eaccelerator_clean();
    }

    public function increment($key,$expire=600,$v=1)
    {
        $storeVal=$this->get($key);
        if($storeVal == null)
            $storeVal = 0 ;
        $storeVal +=$v;
        $this->set($key,$storeVal,$expire);
        return $storeVal;
    }

    public function statusInfo()
    {
        $info = eaccelerator_info();
        return $info;
    }
}

/** 
 * @brief  Cachable implements by Memcached
 */
class LMemCacheDriver implements LCachable
{
    private $_memcache;
    private $_servers;
    private $_compress=false;
    private $_port=11211;

    public function __construct($servers,$port=11211)
    {
        $this->_servers = $servers;
        $this->_port    = $port;
        $this->enable();
    }
    static public function isEnable()  
    {
        
        $res=function_exists('memcache_connect');
        return $res;
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
        if(!is_array($key))
        {
            $str = $this->_memcache->get($key);
            return $str;
//            $obj= unserialize($str);
//            return $obj;
        }
        else
        {
            $arr = $this->_memcache->get($key);
            return $arr;
//            foreach($arr as $a)
//            {
//                $objs[] = unserialize($a);
//            }
//            return $objs; 
        }
    }

    public function set($key, $value, $expire=0)
    {
//        return $this->_memcache->set($key, serialize($value), $this->_compress, $expire);
        return $this->_memcache->set($key, $value, $this->_compress, $expire);
    }

    public function increment($key,$expire=600,$v=1)
    {
        return $this->_memcache->increment($key);
    }

    public function delete($key)
    {
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
            $this->_memcache->addServer($server, $this->_port );
        }

        $this->_compress = false;
    }
    public function disable()
    {
        return $this->_memcache->close();
    }
}

class LMutiLevelCache
{
    private $firstCache=null;
    private $secondCache=null;
    private $sec=60;
    private $incGap=10;
    public function __construct($firstCache,$secondCache,$fstCacheSec=60,$incGap=10)
    {
        $this->firstCache= $firstCache;
        $this->secondCache = $secondCache;
        $this->sec= $fstCacheSec;
        $this->incGap=$incGap;
    }
    public function get($key)
    {
        $found = $this->firstCache->get($key);
        if(!$found  ||  empty($found))
        {
            $found = $this->secondCache->get($key);
            if($found)
            {
                if(!is_array($found))
                {
                    foreach($found  as $key => $val)
                    {
                        $this->firstCache->set($key,$val,$this->sec);
                    }
                }
                else
                {
                    $this->firstCache->set($key,$found,$this->sec);
                }
            }
        }

        return $found;
    }
    public function set($key, $value, $expire=0)
    {

        $this->secondCache->set($key,$value,$expire);
        $this->firstCache->set($key,$value,$expire);
    }

    public function delete($key)
    {
        $this->secondCache->delete($key);
        $this->firstCache->delete($key);
    }
    public function increment($key,$expire=600,$v=1)
    {
        $l1Val = $this->firstCache->get($key);
        $l1Inc = $this->firstCache->increment("$key-inc",$expire,$v);
        if($l1Inc >= $this->incGap)
        {
             $l2Val= $this->secondCache->increment($key,$expire,$this->incGap);
             $this->firstCache->set($key,$l2Val,$expire);
             $this->firstCache->set("$key-inc",0,$expire);
             return $l2Val;
        }
        else
        {
            return $l1Val + $l1Inc;
        }
    }
    public function flush()
    {
        $this->secondCache->flush();
        $this->firstCache->flush();
    }
}

class LMDB
{
    private $space="_df";
    static $cache=null;
    private function __construct()
    {
    }
    static public function setup($memSvrs,$port=11211,$throwErr=false)
    {
        if(self::$cache != null) return ;
        self::$cache = new LMemCacheDriver($memSvrs,$port);
    }
    static public function optimizeSetup($memSvrs,$port=11211,$throwErr=false)
    {
        require_once('cache.php');
        if(self::$cache != null) return ;
        $localcache = new EADriver();
        $netcache = new MemCacheDriver($memSvrs,$port);
        self::$cache = new MutiLevelCache($localcache,$netcache);
    }
    static public function selfdefSetup($cache)
    {
        if(self::$cache != null) return ;
        self::$cache = $cache;
    }
    static public function spaceIns($name)
    {
        $ins = new StatMDB();
        $ins->space  = $name;
        return $ins;
    }
    public function  swapSpace($v)
    {
        $swap = $this->space;
        $this->space = $v;
        return $swap;
    }
    private function storeKey($key)
    {
        $storeKey = '_st_'.$this->space .'_'. $key;
        return $storeKey;
    }
    public function set($key,$v,$expire=3600)
    {
        $storekey=$this->storeKey($key);
        self::$cache->set($storekey,$v,0,$expire);
    }
    public function increment($key,$expire=600,$v=1)
    {

        $storekey=$this->storeKey($key);
        return self::$cache->increment($storekey);
    }
    public function get($key)
    {
        if(!is_array($key))
            return self::$cache->get($this->storeKey($key));
        $storekeys=array();
        foreach($key as $i)
        {
            $storekeys[] = $this->storeKey($i);
        }
        return self::$cache->get($storekeys);
    }
    public function delete($key)
    {
        $storekey=$this->storeKey($key);
        self::$cache->delete($storekey);
    }
    public function statusInfo()
    {
        return self::$cache->getStats();
    }
}
class StatMDB extends LMDB
{
}

class StatQueue
{
    const  QUEUE_MAX_MSG=50;
    const  MSG_QUEUE="queue_st";
    const  FILTER_EXP='filter_exp';
    const  FILTER_ITEM='filter_item';
    const  QUEUE_HEAD='queue_top';
    const  QUEUE_TAIL='queue_tail';
    private $mdb=null;
    static $instance=null; 
    public function __construct($mdb)
    {
        $this->mdb=$mdb;
    }
    static public function ins()
    {
        if(self::$instance == null)
        {
            self::$instance = new StatQueue(StatMDB::spaceIns('QUEUE'));
        }
        return self::$instance;
    }
    //SAFE:SU
    //URL:stglib
    public function setFilter($item,$exp)
    {
        $this->mdb->set(self::FILTER_ITEM,$item);
        $this->mdb->set(self::FILTER_EXP,$exp);
    }
    public function clearFilter()
    {
        $this->mdb->delete(self::FILTER_ITEM);
        $this->mdb->delete(self::FILTER_EXP);
    }
    public function pushMsg($msg) 
    {

        $filterItem= $this->mdb->get(self::FILTER_ITEM);
        $filterExp= $this->mdb->get(self::FILTER_EXP);
        if($filterItem && $filterExp)
        {
            $target = isset($msg[$filterItem]) ? $msg[$filterItem] : "";
            if(!preg_match("/$filterExp/",$target)) return ;
        }
        $max=self::QUEUE_MAX_MSG;
        $head =  $this->mdb->increment(self::QUEUE_HEAD);
        if($head==false )
        {
            $head = self::QUEUE_MAX_MSG;
            $this->mdb->set(self::QUEUE_HEAD,$head);
        }
        $head =  $this->mdb->get(self::QUEUE_HEAD);
        $this->mdb->set($head,$msg,300);
    }
    public function listMsgs()
    {
        $head =  $this->mdb->get(self::QUEUE_HEAD);
        if($head==false  )
        {
            $data =array();
        }
        else
        {
            $keys=range($head,$head -  self::QUEUE_MAX_MSG ,-1);
            $data = $this->mdb->get($keys);
            if($data==false) $data=array();
        }
        return $data;
    }


}
class StatWatcher
{
    const  KEEP_TIME=600;
    const  ITEM_KEEP_TIME=120;
    const  WATCH_KEY='watch_key';
    const  MAX_BOX=101;
    private $mdb=null;
    static $instance=null; 

    public function __construct($mdb)
    {
        $this->mdb=$mdb;
    }
    static public function ins()
    {
        if(self::$instance == null)
        {
            self::$instance = new StatWatcher(StatMDB::spaceIns('WTC'));
        }
        return self::$instance;
    }
    public function needWatch($v)
    {
        $value = $this->mdb->get(self::WATCH_KEY);
        if( is_null($v) || $value  !=$v) return false;
        return true;
    }
    public function watchConf()
    {
        $value = $this->mdb->get(self::WATCH_KEY);
        return $value;
    }
    public function setWatchConf($v)
    {
        $value = $this->mdb->set(self::WATCH_KEY,$v,self::KEEP_TIME);
    }
    public function watchRequest($v,$req)
    {
        if(!$this->needWatch($v)) return;
        $hashval=base_convert(substr(md5($req),0,4),36,10);
        $key = intval($hashval)%self::MAX_BOX;
        $box = $this->mdb->get("$v-$key");
        if($box)
        {
            if(count($box) >= 10) return ;
            $box[$req] +=1;
        }
        else
            $box[$req] = 1;

        $this->mdb->set("$v-$key",$box,self::ITEM_KEEP_TIME);
    }
    public function listWatchedRequests($v)
    {
        if(is_null($v)) return array();
        $data=array();
        for($i = 0 ; $i < self::MAX_BOX ; $i++)
        {
            $box=$this->mdb->get("$v-$i");
            if(is_array($box))
                $data=array_merge($data,$box);
        }
        arsort($data);
        return $data;
    }
}
class StatControl
{
    const ACL_PRIFX='_stat_acl';
    static $instance=null; 
    private $mdb=null;

    public function __construct($mdb)
    {
        $this->mdb=$mdb;
    }
    static public function ins()
    {
        if(self::$instance == null)
        {
            self::$instance = new StatControl(StatMDB::spaceIns('ACL'));
        }
        return self::$instance;
    }
    public function  isTrustAcess($who)
    {
        $acl=$this->mdb->get($who);
        if($acl == false ||  $acl == null  || $acl !== 'F')
        {
            return true;
        }
        return false;
    }
    public function unTrust($who)
    {
        $this->mdb->set($who,'F', 3600*12);
    }
    public function trust($who)
    {
        $this->mdb->delete($who);
    }
}
