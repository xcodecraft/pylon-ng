<?php
namespace pylon\impl ;
/** \addtogroup BaseSvc
 *  @{
 */


interface Lockable
{
    public function lock( $timeout);
    public function unlock();
}

class FileLockImp implements Lockable
{
    private $_fp=null;
    private $key;
    public function __construct($key)
    {
        $this->key = $key;
    }
    public function lock($timeout)
    {
        $this->_fp = fopen('/tmp/'.$this->key.'.lock', "a+w");
        flock($this->_fp, LOCK_EX);
    }

    public function unlock()
    {
        flock($this->_fp, LOCK_UN);
        fclose($this->_fp);
        $this->_fp=null;
    }
}

class SQLLockImp implements Lockable
{
    private $_dbh;

    public function __construct($key)
    {
        $this->key = $key;
    }
    public function lock( $timeout)
    {
        $this->_dbh = XBox::must_get('SQLExecutor');
        $sql = sprintf("SELECT GET_LOCK('%s', %d)", $this->key, $timeout);
        $this->_dbh->query($sql);
    }

    public function unlock($key)
    {
        $sql = sprintf("SELECT RELEASE_LOCK('%s')", $this->key);
        $this->_dbh->query($sql);
    }
}

class LockUtil
{
    const EXPIRE = 3;
    const FILE_LOCK = 0;
    const DB_LOCK= 1;
    static public function timeScopeLock($key,$time,$type)
    {
        $lock = null;
        switch($type)
        {
        case FILE_LOCK:
            $lock = new FileLockImp($key);
            break;
        case DB_LOCK:
            $lock = new SQLLockImp($key);
        }
        $lock->lock($time);
    }
}


/** 
 *  @}
 */
?>
