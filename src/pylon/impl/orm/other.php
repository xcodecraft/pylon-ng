<?php
namespace pylon\impl ;
use XEntity ;

class NullEntity extends XEntity
{
    private $className;
    public function __construct($clsName)
    {
        $this->className=$clsName;
    }
    public function getClass()
    {
        return $this->className;
    }
    public function __call($name,$params)
    {
        throw new LogicException("Call [$name] failed! This is NullEnitity of ".$this->className);
    }

}

/**
 * @ingroup entity
 * @brief Pylon 实体框架控制
 */

class ApiStyle
{
    /**
        * @brief ruby style
        * list_Author_by_id(1)
        * update_Author_set_name_by_id("xp",1)
     */
    const RUBY  = 1;
    /**
        * @brief mongo style
        * list_Author(array("id"=>1))
        * update_Author(array("name"=>"xp"),array("id"=>1))
     */
    const MONGO = 2;
}
