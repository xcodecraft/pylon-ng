# PYLON2 升级到 PYLON-NG




## XEntity

```
$obj         = XEntity::createIns(__CLASS__) ;
```

XEntEnv 代替 XDaoUtls
```
// XDaoUtls::simpleSetup($executer) ;
```

```
XEntEnv::simpleSetup($executer);
```

## WEB 入口

``` php
  include "pylon/pylon.php" ;
  XSetting::$logMode   = XSetting::LOG_DEBUG_MODE ;
  XSetting::$prjName   = "<project>" ;
  XSetting::$logTag    = XSetting::ensureEnv("USER") ;
  XSetting::$runPath   = XSetting::ensureEnv("RUN_PATH") ;
  XSetting::$respClass = "XHtmlResp" ;
  XPylon::serving();
```


## bootstrap.php
框架运行后的入口，可以：
* 拦截器设置
* 实体环境设置
``` php
  $host     = XEnv::get("DB_HOST") ;
  $name     = XEnv::get("DB_NAME") ;
  $user     = XEnv::get("DB_USER") ;
  $pwd      = XEnv::get("DB_PWD" ) ;
  $executer = new LZLExecutor($host,$user,$pwd,$name,
					          FastSQLExecutor::SHORT_CONN,'utf8',"FastSQLExecutor");
  XEntEnv::simpleSetup($executer) ;
  XAop::append_by_match_uri(".*", new AutoCommit());
```


## 实体

实体类
``` php

class OrderEnt  extends XEntity
{
    static public function bizCreate($custName)
    {
        $obj = XEntity::createIns(__class__) ;
        $obj->custName = $custName ;
        return $obj;
    }
}

```

获取数据
```
$data = XQuery::arr()->list_orderent_by_custname("zuowenjian") ;
```
