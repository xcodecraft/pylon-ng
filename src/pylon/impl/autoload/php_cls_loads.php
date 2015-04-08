<?php
/**\addtogroup Autoload
 * @{
 */
/**
 * @brief  用于提示
 */
class Prompt
{
    /**
     * @brief  from keys list recommend  list same as $find;
     *
     * @param $find
     * @param $keys
     *
     * @return  array;
     * eg:
     *    Prompt::recommend('xihu',array('xihoo','msn','google','xihooo'));
     *    return  array('xihoo','xihooo')
     */
    static public function recommend($find,$keys)
    {
        $len = strlen($find);
        $wordlen =3 ;
        if($len >=13 )$wordlen=5;
        if($len >=9 )$wordlen=4;
        $finds = str_split($find,$wordlen);
        $recommend = array();
        foreach($finds as $f)
        {
            if(strlen($f)< 2) continue;
            $recommend = array_merge($recommend,self::match($f,$keys));
        }
        return array_unique($recommend);
    }
    static private function match($find,$keys)
    {
        $match=array();
        if(!empty($find) && ! empty($keys))
        {
            foreach($keys as $key)
            {
                if(preg_match("/$find/",$key))
                    $match[]=$key;
            }
        }
        return $match;
    }
}

/**
 *  @}
 */
