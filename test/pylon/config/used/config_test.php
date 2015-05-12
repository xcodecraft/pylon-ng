<?php

class DBconf
{
    public $host='ayidev02.mysql.rds.aliyuncs.com';
    public $name='pylon_zuowenjian';
    public $user='pylon';
    public $password='pylon123';
}
class Conf
{
    const PRJ_NAME = "pylon";
    static public function getDBConf()
    {
        $conf = new DBconf();
        $conf->name = str_replace("-", "_", $conf->name);
        return $conf;
    }
}
?>
