#PYLON 3.0  数据访问
## 实体 XEntity
``` php
class User extends XEntity
{
    static public function createByBiz($name,$passwd)
    {
        $obj = XEntity::createIns(__CLASS__);
        $obj->logname = $name;
        $obj->name    = $name;
        $obj->passwd  = $passwd;
        return $obj ;
    }
}
```
## 数据读取

###  A

``` php
XQuery::obj()->get_Author_by_id("1000");
XQuery::arr()->get_Author_by_id("1000");
```

### XWriter
