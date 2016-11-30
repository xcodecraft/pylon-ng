<?php
/**
 * /user/profile/$method
 *
 * @author yangwm
 */
class user_profile extends XRuleService implements XService
{
    /**
     * get?uid=$uid
     */
    public function get($xcontext,$request,$response)
    {
        $response->success("user_profile get user: " . $request->uid);
    }
    /**
     * gets?uids=$uids_str
     */
    public function gets($xcontext,$request,$response)
    {
        $response->success("user_profile gets users: " . $request->uids);
    }
    /**
     * save -d uid=$uid&name=$name&type=$type
     */
    public function save($xcontext,$request,$response)
    {
        $response->success("user_profile save user: " . $request->uid
            . ", " . $request->name
            . ", " . $request->type);
    }
}

/**
 * /guser/user/profile/$method
 *
 * @author yangwm
 */
class guser_user_profile extends XRuleService implements XService
{
    /**
     * get?uid=$uid
     */
    public function get($xcontext,$request,$response)
    {
        $response->success("guser_user_profile get user: " . $request->uid);
    }
}

//@REST_RULE: /gexample/test2/$gkey/$method
class gexample_test2 extends XRuleService implements XService   
{
    public function score($xcontext,$request,$response)
    {
        $gkey = $request->gkey;
        $uid  = $request->uid;
        $response->success("$gkey 1000, uid $uid");
    }
    public function stop($xcontext,$request,$response)
    {
        $gkey = $request->gkey;
        $response->success("$gkey OK");
    }
    public function start($xcontext,$request,$response)
    {
        $gkey = $request->gkey;
        $response->success("$gkey OK");
    }
}

