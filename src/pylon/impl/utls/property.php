<?php
/**\addtogroup utls
 * @{
 */
/**
 * @brief  属生对象
 */
// class PropertyObj
// {
//     protected $_attrs = array();
//     protected $hasDefault=false;
//     protected $defaultVal=null;
//     protected function __construct($arr=array())
//     {
//         $this->_attrs = $arr;
//     }
//     static public function tolower(&$item1,$key)
//     {
//         $item1 = strtolower($item1);
//     }
//     /**
//         * @brief 通过数组创建
//         *
//         * @param $arr
//         * @param
//         *
//         * @return
//      */
//     static public function fromArray($arr=array())
//     {
//         if(empty($arr))
//             return new PropertyObj();
//         $newarr=array();
//         $keys = array_keys($arr);
//         array_walk($keys,array("PropertyObj","tolower"));
//         $newarr = array_combine($keys,array_values($arr));
//         return new PropertyObj($newarr);
//     }
//     /**
//         * @brief 设置无此key 返回的默认值
//         *
//         * @param $hasDefault
//         * @param $value
//         *
//         * @return
//      */
//     public function defaultValue($hasDefault=true, $value="")
//     {
//         $this->hasDefault = $hasDefault;
//         $this->defaultVal = $value;
//     }
//     public function init($name,$val)
//     {
//         if(!$this->have($name))
//             $this->__set($name,$val);
//     }
//     public function __set($name,$val)
//     {
//         $name=strtolower($name);
//         $this->_attrs[$name]=$val;
//     }
//     /**
//         * @brief 是否有值
//         *
//         * @param $name
//         *
//         * @return
//      */
//     public function haveSet($name)
//     {
//         $name=strtolower($name);
//         return array_key_exists($name,$this->_attrs);
//     }
//     /**
//         * @brief  是否有值 同 haveSet()
//         *
//         * @param $name
//         *
//         * @return
//      */
//     public function have($name)
//     {
//         return $this->haveSet($name);
//     }
//     public function __get($name)
//     {
//         $name=strtolower($name);
//         if(!$this->hasDefault)
//         {
//             DBC::requireTrue(array_key_exists($name,$this->_attrs),"[$name] not exist!");
//             return $this->_attrs[$name];
//         }
//         else
//         {
//             if(isset($this->_attrs[$name]) )
//                 return $this->_attrs[$name];
//             else
//                 return $this->defaultVal;
//         }
//     }
//     public function remove($name)
//     {
//         $name=strtolower($name);
//         unset( $this->_attrs[$name]);
//     }
//     public function removeAll()
//     {
//         $this->_attrs =  array();
//     }
//     public function isEmpty()
//     {
//         return empty($this->_attrs);
//     }
//     public function filter($value=null,$fun=null)
//     {
//         if($value)
//             $fun = create_function('$v','return  $v != '.$value.'; ');
//         if($fun)
//             $this->_attrs= array_filter($this->_attrs,$fun);
//         else
//             $this->_attrs= array_filter($this->_attrs);
//     }
//     /**
//      * @brief
//      *
//      * @return array
//      */
//     public function getPropArray()
//     {
//         return $this->_attrs;
//     }
//     public function merge($other)
//     {
//         $this->_attrs = array_merge($this->_attrs,$other->_attrs);
//     }
//     public function merges($others)
//     {
//         foreach($others as $item)
//         {
//             $this->merge($item);
//         }
//     }
// }

/**
 *  @}
 */
