# PYLON 的扩展
`解决什么问题?`

## 配置信息的高效访问:
- autoload 的类索引
- route 的规则

## 多进程间高效的数据访问：


## 日志的高效实现

------

## 对外API


### 日志


```
class log_kit
{
    public:
        enum level_t    { debug = 0, info, warn,error,undef=99};
        enum channel_t  { ch0   = 0, ch1,ch2,ch3,ch4,ch5,ch6,ch7 }  ;
        enum outer_t    { none  = 0 ,console=1 };

        static logger* log_ins(const char* name);

        static void init(const char * prjname,const char* tag ,level_t l);

        static void level(const char * name , level_t l, uint ratio=1);

        static void tag(const char * name , const char* tag );

        static void event(const char * event);

        static void out(const char * name , outer_t extra_out);

        static void clear();

        static void channel( log_kit::channel_t  );

        static void toall( bool );
        struct impl;
};

```

### 共享字典

```php
function    pylon_sdict_create   ( proc_space, msize) ;
function    pylon_sdict_using    ( proc_space ) ;
function    pylon_sdict_data     ( data_file,key_prefix , data_prefix, force) ;
function    pylon_sdict_find     ( key) ;
function    pylon_sdict_count    ( ) ;
function    pylon_sdict_remove   ( ) ;
```

### 进程字典

``` php
function   pylon_dict_data    ( data_file,key_prefix , data_prefix, force) ;
function   pylon_dict_find    ( key ) ;
function   pylon_dict_has     ( key ) ;
function   pylon_dict_prompt  ( key ) ;
function   pylon_dict_count   ( ) ;
```

