<?php
class MyGameSvc extends XSimpleService implements XService //@REST_RULE: /mygame/$uid/,/mygame/$uid
{
    public function _post($xcontext,$request,$response)
    {
        XLogKit::logger("rest")->debug(__FUNCTION__,"his");
        XLogKit::logger("rest")->debug(__FUNCTION__);
        $response->error("post error",XErrCode::SYS_UNKNOW,404);
    }

    public function _put($xcontext,$request,$response)
    {
        $response->error("put error");
    }
    public function _get($xcontext,$request,$response)
    {
        $response->success("hellow world user: " . $request->uid );
    }
}
class GameSvc extends XSimpleService implements XService   //@REST_RULE: /game/$gkey
{
    public function _get($xcontext,$request,$response)
    {
        throw new XBizException("biz error");
    }
    public function _post($xcontext,$request,$response)
    {
        $response->success("good");
    }
}
class AllgameSvc extends XSimpleService implements XService   //@REST_RULE: /allgame
{
    public function _get($xcontext,$request,$response)
    {
        $response->success("hellow world user: " . $request->uid );
    }
    public function _after($xcontext,$request,$response)
    {
        $response->error("after",101);
    }
}


/**
 * 对外接口，建议使用标准的REST风格
 * @brief
 * GET  /books                  获取所有book列表
 * eg:  curl '${DOMAIN}:8360/books'
 *
 * GET  /books/$book_id         根据book_id获取书籍详情
 * eg:  curl '${DOMAIN}:8360/books/123'
 *
 * POST /books                  新建/修改一个book
 * eg:  curl '${DOMAIN}:8360/books'  -d "uid=123&book_name=天龙八部&author_name=金庸"
 *
 * DELETE /books/$book_id       删除book_id所对应的书籍
 * eg:  curl -X DELETE '${DOMAIN}:8360/books/123'
 */

class BooksSvc extends XSimpleService implements XService   //@REST_RULE: /books/$uid,/books/,/books
{
    public function _post($xcontext,$request,$response)
    {
        $uid         = XssFilter::filter($request->uid);          //防止XSS攻击,不用做SQL注入处理，框架会帮我们完成
        $book_name   = XssFilter::filter($request->book_name);    //防止XSS攻击
        $author_name = XssFilter::filter($request->author_name);  //防止XSS攻击

        XLogKit::logger("rest")->info(__CLASS__.'::'.__FUNCTION__.' ['.__LINE__.'] '."uid=$uid&book_name=$book_name&author_name=$author_name");//记日志
        $response->error("post error ",XErrCode::SYS_UNKNOW,404);//error(错误描述，错误号，http状态码),错误返回:json格式
    }

    public function _put($xcontext,$request,$response)
    {
        throw new XBizException("put error :", 20001);//XBizException(错误描述，错误号),错误返回:json格式
    }

    public function _get($xcontext,$request,$response)
    {
        $uid = XssFilter::filter($request->uid);  //防止XSS攻击
        $response->success("get ok: " . $request->uid );
    }

    public function _delete($xcontext,$request,$response)
    {
        $book_id = XssFilter::filter($request->book_id);  //防止XSS攻击

        XLogKit::logger("rest")->info(__CLASS__.'::'.__FUNCTION__.' ['.__LINE__.'] '."uid=$uid");//记日志

        $response->success("delete ok : " . $request->uid );
    }
}



