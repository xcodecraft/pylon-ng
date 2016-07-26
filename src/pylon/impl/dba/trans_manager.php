<?php
namespace pylon\impl ;

/**\addtogroup DBA
 * @{
 */

/** 
 * @brief 
 */
class TransManager
{
    static $_exectorLoader;
    static function regExectorLoader($funName) 
    {
        self::$_exectorLoader = $funName;
    }
	static function createTrans()
	{
        $exector = call_user_func(self::$_exectorLoader);
		$trans   = new Translation($exector);
		return $trans;
	}
}


/** 
 *  @}
 */
?>
