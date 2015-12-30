<?php

//@REST_RULE: /err/$method
class ERRSvc extends XRuleService implements XService   
{
    public function unauth($xcontext,$request,$response)
    {

        // throw new XUnAuthorized("for test") ;
        //
        throw new XUnAuthorized() ;
        // throw new XUnAuthorized() ;
    }

}
