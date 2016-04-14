#使用不同response 实例


通过以变量可以设定 response 类型 

``` php
XSetting::$respClass   //响应类
XSetting::$respInsFun  //响应实例函数，优先
``` 


### 已实现的response 类型 
- XHtmlResp  通过tpl输出html 
- XRestResp  输出resp 响应 
- XEchoResp  直接out 直接echo 信息

[response.php](../../src/interface/response.php)

### 示例
```php

XSetting::$respClass = "XHtmlResp" ;
XSetting::$respInsFun = function ($uri) {  return new XRestResp(); };

```
