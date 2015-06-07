#对REST的支持


## jsonp
使用 XRestResp 的 jsonp 进行设定！

```
class XRestResp implements XResponse
{
    public function jsonp($callback)
    {
    }
}
```

```
public function _get($xcontext,$request,$response)
{
  ...
  $response->jsonp('callback');
}
```
