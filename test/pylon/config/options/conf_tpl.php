<?php

class DBconf
{
    public $host='${DB_HOST}';
    public $name='${DB_NAME}';
    public $user='${DB_USER}';
    public $password='${DB_PWD}';
}
class Conf
{
    const PRJ_NAME = "${PRJ_NAME}";
    static public function getDBConf()
    {
        $conf = new DBconf();
        $conf->name = str_replace("-", "_", $conf->name);
        return $conf;
    }
}
?>
