<?php

class XEnv
{
    static public function get($key)
    {
        if ( isset($_SERVER[$key] )  ) {
            return $_SERVER[ $key ] ;
        }
        throw new XLogicException( "_SERVER[$key] not exists ",500) ;
    }
    static public function priority_get($pval,$key)
    {
        if ($pval !== null )
            return $pval ;
        if ( isset($_SERVER[$key] )  ) {
            return $_SERVER[ $key ] ;
        }
        throw new XLogicException( "_SERVER[$key] not exists ",500) ;
    }
}
