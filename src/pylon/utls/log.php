<?php
/**
 * @ingroup utls
 * @brief  在多日记对象进行事件记录。
 */
class XScopeLogEvent
{
    public function __construct($event)
    {
        XLogKit::event($event);
    }
    public function __destruct()
    {
        XLogKit::event("");
    }
}




/**
 * @brief  在范围内为日志打tag
 */
class XScopeLogTag
{
    static $allTags = array();
    /**
     * @brief
     *
     * @param $name
     * @param $tag
     *
     * @return
     */
    static function create($name,$tag)
    {
        return new XScopeLogTag($name,$tag) ;
    }
    public function __construct($logName,$tag)
    {

        $this->tag              = $tag ;
        $this->logName          = $logName ;
        self::$allTags[$tag]    = 1 ;
        $tags = implode("," , array_keys( self::$allTags )) ;
        XSetting::logTag($this->logName,$tags);

    }
    public function __destruct()
    {
        unset(self::$allTags[$this->tag]);
        $tags = implode("," , array_keys(self::$allTags) ) ;
        XSetting::logTag($this->logName,$tags);
    }
}
