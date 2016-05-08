
## 添加拦截器

``` php
  class  AccessAllow  extends XInterceptor
  {


      public function _before($xcontext,$request,$response)
      {
          if (isset($_SERVER['HTTP_ORIGIN']))
          {
              header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
              header('Access-Control-Allow-Credentials: true');

              // Access-Control headers are received during OPTIONS requests
              if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
              {
                  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                  {
                      header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
                  }

                  if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                  {
                      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
                  }
              }
          }
      }

  }
```
