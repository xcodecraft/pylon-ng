<?php
/**\addtogroup utls
 * @{
 */
/** 
* @brief  系统版本
 */
class SysVersion
{
    public $structNo=0;
    public $featureNo=0;
    public $fixbugNo=0;
    public $commitNo=0;
    static public $versionFile="";
    static public $instance=null;
    /** 
        * @brief 
        * 
        * @param $file  版本文件
        * 
        * @return 
     */
    static public function init($file)
    {
        self::$versionFile = $file;
    }
    static public function  ins()
    {
        if(self::$instance != null)
            return self::$instance;
        $version=@file_get_contents(self::$versionFile);
        list($structNo,$featureNo,$fixbugNo,$commitNo) = explode(".",$version);
        return new SysVersion($structNo,$featureNo,$fixbugNo,$commitNo);
    }
    public function safe_int($val )
    {
        return empty($val) ? 0 : $val ;
    }
    public function __construct($struct,$feature,$fixbug,$commitNo)
    {
        $struct  = empty($struct) ?  0:$struct;
        $feature = empty($feature) ? 0:$feature;
        $fixbug  = empty($fixbug) ?  0:$fixbug;
        $commitNo= empty($commitNo) ? 0:$commitNo;
        
        $this->structNo  = safe_int($struct  );
        $this->featureNo = safe_int($feature );
        $this->fixbugNo  = safe_int($fixbug  );
        $this->commitNo  = safe_int($commitNo );
    }
    public function save()
    {
        $version = "{$this->structNo}.{$this->featureNo}.{$this->fixbugNo}.{$this->commitNo}";
        file_put_contents(self::$versionFile,$version);

    }
    /** 
        * @brief 提交，版本的 build号加1 
        * 
        * @return 
     */
    public function commit()
    {
        $this->commitNo +=1;
    }
    /** 
        * @brief fixbug 1.0.0.100 -> 1.0.1.101
        * 
        * @return 
     */
    public function fixbug()
    {
        $this->fixbugNo  += 1;
    }
    /** 
        * @brief  1.0.1.100 -> 1.1.0.101
        * 
        * @return 
     */
    public function featureUpgrade()
    {
        $this->featureNo +=1 ;
        $this->fixbugNo =0 ;
    }
    /** 
        * @brief 1.1.1.100 -> 2.0.0.101
        * 
        * @return 
     */
    public function structUpgrade()
    {
        $this->featureNo = 0;
        $this->fixbugNo  = 0;
        $this->structNo  += 1;
    }
    public function verinfo()
    {
        $version = "{$this->structNo}.{$this->featureNo}.{$this->fixbugNo}.{$this->commitNo}";
        return $version;
    }
}

/** 
 *  @}
 */
