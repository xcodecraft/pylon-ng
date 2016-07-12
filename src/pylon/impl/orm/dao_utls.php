<?php
namespace pylon\impl ;
use XProperty ;

/**\addtogroup Ent
 * @{
 */
class FilterProp extends XProperty
{
    static public function create($arr=array())
    {
        if(empty($arr))
        {
            return new FilterProp();
        }
        $lowerArr = array_change_key_case($arr) ;
        return new FilterProp($lowerArr);
    }
}

class CondProp
{
    static public function make($key,$value)
    {
        $prop = XProperty::fromArray();
        $prop->$key=$value;
        return $prop;
    }
    static public function makeByObj($obj)
    {
        $prop = XProperty::fromArray();
        $key= strtolower(get_class($obj)) . "__id";
        $prop->$key=$obj->id();
        return $prop;
    }
}


class DynCallParser
{
    static private function separatorOfBy($by)
    {
        $seps['by']  = '/([a-z\d])_([a-z\d])/i';
        $seps['by2'] = '/([a-z\d])__([a-z\d])/i';
        $seps['by3'] = '/([a-z\d])___([a-z\d])/i';
        return $seps[$by];
    }
    static public function condObjParse($callName)
    {
        $matchs=array();
        if(strpos($callName,'_by'))
        {
            preg_match('/(\S+)_(\S+)_(by\d?)_(\S+)/',$callName,$matchs);
            list($all,$op,$cls,$by, $condnames)=$matchs;
            $condnames = preg_replace(static::separatorOfBy($by),'$1#$2',$condnames);
            $result['condnames']=explode('#',$condnames);
        }
        else
        {
            preg_match('/([^_\s]+)_(\S+)/',$callName,$matchs);
            list($all,$op,$cls)=$matchs;
            $result['condnames']=array();
        }

        $result['op']  = $op;
        $result['cls'] = $cls;
        return $result;
    }
    static public function buildCondProp($condnames,$params,&$extraParams)
    {
        $cnt = count($condnames);
        $extraParams = array_splice($params,$cnt);
        if($cnt == 0 )
        {
            return FilterProp::create();
        }

        $first = count($condnames);
        $second = count($params);
        if($first  != $second)
        {
            $names  = JoinUtls::jarray(",", $condnames);
            $values  = JoinUtls::jarray(",", $params);
            DBC::unExpect(null,"count of params name not match value! names is [ $names] value is [$values]");
        }
        $condArr = array_combine($condnames,$params);
        if(in_array('Prop',$condnames))
        {
            $userDefprop = $condArr['Prop'];
            unset($condArr['Prop']);
            $prop = FilterProp::create($condArr);
            if(!empty($userDefprop))
            {
                $prop->merge($userDefprop);
            }
        }
        else
        {
            $prop = FilterProp::create($condArr);
        }
        return $prop;
    }

    static public function buildUpdateArray($updatenames,$condnames,$params)
    {
        $ucnt                 = count($updatenames);
        $condParams           = array_splice($params,$ucnt);
        $props['updateArray'] = array_combine($updatenames,$params);
        if(count($condnames) >0)
        {
            $condArr = array_combine($condnames,$condParams);
            if(in_array('Prop',$condnames))
            {
                $userDefprop = $condArr['Prop'];
                $userDefArr = $userDefprop->getPropArray();
                unset($condArr['Prop']);
                $condArr = array_merge($condArr,$userDefArr);
            }
        }
        else
        {
            $condArr = array();
        }
        $props['condArray']= $condArr;
        return $props;
    }

    static private function separatorOfSet($by)
    {
        $seps['set']    = '_';
        $seps['set2']   = '__';
        $seps['set3']   = '___';
        return $seps[$by];
    }

    static public function condUpdateObjParse($callName)
    {
        $matchs=array();
        $set="set";
        if(strpos($callName,'_by'))
        {
            preg_match('/update_(\S+)_(set[1-9]?)_(\S+)_(by\d?)_(\S+)/',$callName,$matchs);
            list($all,$cls,$set,$updates,$by, $condnames)=$matchs;
            $condnames = preg_replace(static::separatorOfBy($by),'$1#$2',$condnames);
            $result['condnames']=explode('#',$condnames);
        }
        elseif(strpos($callName,'_set'))
        {
            preg_match('/update_(\S+)_(set[1-9]?)_(\S+)/',$callName,$matchs);
            list($all,$cls,$set,$updates,)=$matchs;
            $by="";
            $result['condnames']=array();

        }
        else
        {
            preg_match('/update_(\S+)/',$callName,$matchs);
            list($all,$cls) = $matchs;
        }

        $updateKeys = explode(static::separatorOfSet($set),$updates);
        $result['op']="set";
        $result['cls']=$cls;
        $result['by']=$by;
        $result['updatenames']=$updateKeys;
        return $result;

    }
}


/**
 *  @}
 */
