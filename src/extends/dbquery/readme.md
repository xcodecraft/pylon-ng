# dbquery数据库查询文档
SvcUtls\DB 扩展
## 数据库链接 创建链接并使用
``` php
    //项目需要引入svc_utls src
     $dbExecutor = new LZLExecutor(
                 self::$sqlContect['host'],
                 self::$sqlContect['user'],
                  self::$sqlContect['pwd'],
                  self::$sqlContect['dbName'],
                   FastSQLExecutor::SHORT_CONN, 'utf8', "FastSQLExecutor"
     );
     \Pylon\db\dbquery::Q()->db($dbExecutor);
```
## 获取数据方式
##### 1.获取一条数据的第一列 返回值为一个字符串
``` php
    /**
    * 返回值为 id 的值 例如: 10000
    */
   \Pylon\db\dbquery::Q()->db($dbExecutor)->fetchOne('select id from coupon');
```

##### 2.获取一条数据的第一行 返回值为一维数组
``` php
    /**
    * 返回值为 array 的值 例如:
    * array(
    *   'id'=>1000,
    *   'name'=>'张三'
    * )
    */
   \Pylon\db\dbquery::Q()->db($dbExecutor)->fetchRow('select id from coupon');
```

##### 3.获取多条数据 并以第一个字段为键值的数组，如果字段数量为2则是以为数组，如果字段大于2，则是多维数组
``` php
    /**
    * 返回值为 array 的值 字段为两个的时候 返回值如下：
    * $data[1000] = '张三'
    */
   \Pylon\db\dbquery::Q()->db($dbExecutor)->fetchPairs('select id,name from coupon');

   /**
   * 返回值为 array 的值 字段为>两个的时候 返回值如下：
   * $data[1000] = array(
   *     'name'=>'张三',
   *     'actName'=>'端午节活动',
   * )
   */
  \Pylon\db\dbquery::Q()->db($dbExecutor)->fetchPairs('select id,name,actName from coupon');
```

##### 4.获取多条数据 返回而为数组和获取分页数据
``` php
    /**
    * 返回值为 array 当没有limit 设置的时候 返回值如下：
    * $data = array(
    *    0=>array(
    *        'id'=>1000,
    *        'name'=>'保洁券'
    *    ),
    *    1=>array(
    *        'id'=>1001,
    *        'name'=>'保洁券'
    *    )
    * );
    */
   \Pylon\db\dbquery::Q()->db($dbExecutor)->fetchAll('select id,name from coupon');

   /**
   * 返回值为 array 当有limit 设置的时候 返回值如下：
   * $data = array(
   *     'totalRows'=>'100000',
   *     'datas'=>array(
   *              0=>array(
   *                  'id'=>1000,
   *                  'name'=>'保洁券'
   *              ),
   *              1=>array(
   *                  'id'=>1001,
   *                  'name'=>'保洁券'
   *              )
   *     ));
   */
  \Pylon\db\dbquery::Q()->db($dbExecutor)->limit('0,2')->fetchAll('select id,name from coupon');
```

##### 5.其他统计查询方法
``` php
    /**
    * count 查询
    * 解析后的sql语句为 select count(1) from coupon group by id
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)->table('coupon')->group('id')->count();

    /**
    * sum 查询
    * 解析后的sql语句为 select sum(amount) from coupon
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)->db($this->dbContect)->table('coupon')->sum(amount);

    /**
    * min 查询
    * 解析后的sql语句为 select sum(amount) from coupon
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)->db($this->dbContect)->table('coupon')->min(amount);

    /**
    * max 查询
    * 解析后的sql语句为 select max(amount) from coupon
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)->db($this->dbContect)->table('coupon')->max(amount);

    /**
    * avg 查询
    * 解析后的sql语句为 select avg(amount) from coupon
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)->db($this->dbContect)->table('coupon')->avg(amount);
```

## where条件解析
##### 1.支持对象传入自动解析方式，使用方法如下
``` php
    /**
    * arrIns 自动将$couponQueryDTO映射到couponDTO，根据表达式映射成sql语句
    * 解析后的sql语句为 select * from coupon where id=xxx
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = $queryArr['id'];
    $data               = \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)
                            ->table('coupon')
                            ->fetchAll();
```
##### 2.等于，大于，小于，大于等于，小于等于 表达式说明
``` php
    /**
    * 等于
    * 解析后的sql语句为 select * from coupon where id=xxx
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = 'xxx';
    $data               = \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)
                            ->db($this->dbContect)->table('coupon')->fetchAll();

    /**
    * 大于
    * 解析后的sql语句为 select * from coupon where id>xxx
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '>xxx';

    /**
    * 大于等于
    * 解析后的sql语句为 select * from coupon where id>=xxx
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '>=xxx';

    /**
    * 小于
    * 解析后的sql语句为 select * from coupon where id<xxx
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '<xxx';

    /**
    * 小于等于
    * 解析后的sql语句为 select * from coupon where id<=xxx
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '<=xxx';
```

##### 3.区间表达式说明
``` php
    /**
    * 开区间
    * 解析后的sql语句为 select * from coupon where id>xxxA and id <xxxB
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '(xxxA,xxxB)';
    $data               = \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)
                            ->db($this->dbContect)->table('coupon')->fetchAll();

    /**
    * 闭区间
    * 解析后的sql语句为 select * from coupon where id>=xxxA and id <=xxxB
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '[xxxA,xxxB]';

    /**
    * 半开半闭区间
    * 解析后的sql语句为 select * from coupon where id>xxxA and id <=xxxB
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '(xxxA,xxxB]';

    /**
    * 半开半闭区间
    * 解析后的sql语句为 select * from coupon where id>=xxxA and id <xxxB
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '[xxxA,xxxB)';
```

##### 4.in 和notin查询
``` php
    /**
    * in 查询
    * 解析后的sql语句为 select * from coupon where id in(xxxA,xxxB)
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '{xxxA,xxxB}';
    $data               = \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)
                        ->db($this->dbContect)->table('coupon')->fetchAll();

    /**
    * not in 查询
    * 解析后的sql语句为 select * from coupon where id not in (xxxA,xxxB)
    */
    $couponQueryDTO     = new CouponDTO;
    $couponQueryDTO->id = '!{xxxA,xxxB}';
```

##### 5.like 和 not like 查询
``` php
    /**
    * like 查询
    * 解析后的sql语句为 select * from coupon where name like '%xxx%'
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)
       ->db($this->dbContect)->table('coupon')->setCond('name',array('like','xxx'))->fetchAll();

    /**
    * notlike 查询
    * 解析后的sql语句为 select * from coupon where name not like '%xxx%'
    */
    \Pylon\db\dbquery::arrIns($couponQueryDTO, CouponDTO)
    ->db($this->dbContect)->table('coupon')->setCond('name',array('notlike','xxx'))->fetchAll();
```
