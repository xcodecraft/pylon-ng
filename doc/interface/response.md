# 

## XHtmlResp

```
public function setRoot($root) ;
public function tpl($_xc,$file) ;
public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$statusCode = 500) ;
```

示例

```
class Htmlsvc extends XSimpleService implements XService   //@REST_RULE: /web/html/$sub
{
    public function _get($xcontext,$request,$response)
    {
        $xcontext->x = "google" ;
        $response->tpl($xcontext, $request->sub . ".html");
    }
}

```

## XRestResp

``` php

public function success($data,$status_code = 200 );
public function error($errmsg,$errno = XErrCode::BIZ_UNKNOW,$status_code = 500);

```

示例：
```
class GameSvc extends XSimpleService implements XService   //@REST_RULE: /game/$gkey
{
    public function _post($xcontext,$request,$response)
    {
        $response->success("good");
    }
}
```
##扩展

实现方法
```
public function send($logger,$set_header=true);
```