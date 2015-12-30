<?php
namespace Pylon ;

/**\addtogroup utls
 * @{
 */

/** 
* @brief 类似DBC 类， 但失败后的行为，为抛出 UserInputException
 */
class ArgsChecker
{
    public static  $exceptionClass='UserInputException';
    static private function dofailAction($msg)
    {
        throw new self::$exceptionClass($msg);
    }

    static private function nullMsg($val,$type)
    {
        return  "type is $type, Object is null !";
    }

    /** 
     * @brief 
     * 
     * @param $first 
     * @param $second 
     * @param $firstName 
     * 
     * @return  $first
     */
    static public function requireEquals($first,$second,$msg= "first value != second value")
    {
        if($first != $second)
            self::dofailAction($msg);
        return $first;
    }
    /** 
     * @brief 
     * 
     * @param $first 
     * @param $parentClass 
     * 
     * @return $first
     */
    static public function requireIsA($first,$parentClass,$msg="value is not subclass of ")
    {
        if(!is_a($first,$parentClass))
            self::dofailAction($msg);
        return $first;
    }
    static public function requireNotEquals($first,$second,$msg="first value == second value")
    {
        if($first == $second )
            self::dofailAction($msg);
        return $first;
    }
    /** 
        * @brief  ÓÃÓÚ¶ÏÑÔ´ËÌØÐÔÃ»ÓÐÊµÏÖ¡£
        * 
        * @param $funName 
        * 
        * @return void  
     */
    static public function requireNull($obj,$msg="value is not null")
    {
        if(is_null($obj)) return $obj;
        self::dofailAction($msg);

    }
    /** 
     * @brief 
     * 
     * @param $obj 
     * @param $msg 
     * 
     * @return $obj 
     */
    static public function requireNotNull($obj,$msg="value is  null ")
    {
        if(!is_null($obj)) return $obj;
        self::dofailAction($msg);
    }

    /** 
     * @brief 
     * 
     * @param $obj 
     * @param $msg 
     * 
     * @return $obj 
     */
    static public function requireObj($obj,$msg="value is not object")
    {
        if(is_object($obj)) return $obj;
        self::dofailAction($msg);
    }
    /** 
     * @brief 
     * 
     * @param $obj 
     * @param $msg 
     * 
     * @return $obj 
     */
    static public function requireTrue($obj,$msg="require true ,but is false")
    {
        if($obj) return $obj;
        self::dofailAction($msg);
    } 
    static public function requireArray($arr,$msg="value is not  Array")
    {
        if(is_array($arr)) return $arr;
        self::dofailAction($msg);
    }


}


/** 
 *  @}
 */
