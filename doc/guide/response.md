#响应
## RESTFul

``` php
$response->success($data) ;
```
## HTML
```php
XSetting::$respClass = "XHtmlResp" ;
```

## 简单输出

``` php
XSetting::$respClass = "XEchoResp" ;
$response->out($msg, $statusCode = 200) ;
```
## 自定义输出

```php
XSetting::$respClass = "XHtmlResp" ;
```

``` php
$response->out($msg, $statusCode = 200) ;
```
