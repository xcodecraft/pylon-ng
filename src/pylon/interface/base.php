<?php
/**
 * @brief   属性对象
 */
class XProperty
{
	public $attr=null;
	public function __construct($arr=array())
	{
		$this->attr= &$arr;
	}
	/**
	 * @brief  判断值是否存在
	 *
	 * @param $name
	 *
	 * @return  true|false
	 */
	public function have($name)
	{
		if(!is_string($name)) return false;
        DBC::requireNotNull($this->attr,'attr');
		return array_key_exists(strtolower($name),$this->attr);
	}
	/**
	 * @brief  获取不存在的值，会报错
	 *
	 * @param $name
	 *
	 * @return
	 */
	public function __get($name)
	{

		return $this->attr[strtolower($name)];
	}
	public function __set($name,$val)
	{
		return $this->attr[strtolower($name)]=$val;
	}
	public function merge($other)
	{
		$this->attr = array_merge($this->attr,$other->attr);
	}

	public function mergeArray($other)
	{
		$this->attr = array_merge($this->attr,array_change_key_case($other));
	}
	/**
	 * @brief 当获取不存在的值时，不报错，会返回设定的默认值(null)
	 *
	 * @param $name
	 * @param $default
	 *
	 * @return
	 */
	public function get($name,$default=null)
	{
		return $this->have($name)?$this->attr[$name]:$default;
	}

    static public function fromArray($arr=array())
    {
        if(empty($arr))
            return new XProperty();
        $lowerArr = array_change_key_case($arr) ;
        return new XProperty($lowerArr);
    }
    public function getPropArray()
    {
        return $this->attr;
    }

    public function isEmpty()
    {
        return empty($this->attr);
    }
    public function remove($name)
    {
        $name=strtolower($name);
        unset( $this->attr[$name]);
    }
    // public function filter($value=null,$fun=null)
    // {
    //     if($value)
    //         $fun = create_function('$v','return  $v != '.$value.'; ');
    //     if($fun)
    //         $this->attr= array_filter($this->attr,$fun);
    //     else
    //         $this->attr= array_filter($this->attr);
    // }

}

