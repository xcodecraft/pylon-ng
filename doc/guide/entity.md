
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

##实体映射规则
- 数据库的表名与实体属性名一致;
- 不区分大小写(表全为小写) 
- 实体中对象与对象1对1的关系:在表中使用classname__id的方式表示关联的对象

## 数据读取

###  XQuery

``` php
XQuery::obj()->get_Author_by_id("1000");
XQuery::arr()->get_Author_by_id("1000");
```

### XWriter
