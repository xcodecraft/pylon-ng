<?php
//@REST_RULE: /mygame/$uid/,/mygame/$uid
class MyGameSvc extends XSimpleService implements XService
{
    public function _post($xcontext,$request,$response)
    {
        XLogKit::logger("rest")->debug(__FUNCTION__,"his");
        XLogKit::logger("rest")->debug(__FUNCTION__);
        $response->error("post error",XErrCode::UNKNOW,404);
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
//@REST_RULE: /game/$gkey
class GameSvc extends XSimpleService implements XService
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



//@REST_RULE: /books/$uid,/books/,/books
class BooksSvc extends XSimpleService implements XService
{
    public function _post($xcontext,$request,$response)
    {
        $response->error("post error ",XErrCode::SYS_UNKNOW,404);
    }

    public function _put($xcontext,$request,$response)
    {
        throw new XBizException("put error :", 20001);//XBizException(错误描述，错误号),错误返回:json格式
    }

    public function _get($xcontext,$request,$response)
    {
        $response->success("get ok: " . $request->uid );
    }

    public function _delete($xcontext,$request,$response)
    {
        $response->success("delete ok : " . $request->uid );
    }
}



// /books/lists?auth=zuowenjian&date=[2010-1-1,2012-2-1]&limit=[0,2]
//@REST_RULE: /books/lists
class Books2Svc extends XRuleService implements XService
{
    public function lists($xcontext,$request,$response)
    {
        // list($auth,$data,$limit)= XInput::safeArr($reuest,'auth,date,limit');
        $dict = XInput::safeDict($reuest,'auth,date,limit,order');
        $sql  = XSql::where("select * from apple " ,$dict);
        $data = XQuery::sql($sql) ;
        $response->success(['data' => $data, 'page' => $page]);
    }

    public function page($xcontext,$request,$response)
    {
        // list($auth,$data,$limit)= XInput::safeArr($reuest,'auth,date,limit');
        $dict = XInput::safeDict($reuest,'auth,date');
        $sql  = XSql::where("select count(* )from apple " ,$dict);
        $data = XQuery::sql($sql) ;
        $response->success(['data' => $data, 'page' => $page]);
    }
}

