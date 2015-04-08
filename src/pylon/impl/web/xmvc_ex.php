<?php
/** 
* @brief 支持Smarty模板的Renderer
 */
class SmartyRenderer implements XRenderer
{
    public $smarty=null;
    /** 
	    * @brief 构造函数
	    * 
	    * @param $smartyRoot   smarty 模板的根路径
	    * @param $templarRoot 
	    * 
	    * @return 
     */
    public function __construct($smartyRoot,$templarRoot)
    {
        require('smarty/Smarty.class.php');
        $smarty = new Smarty();
        $smarty->template_dir = $templarRoot;
        $smarty->compile_dir  = "$smartyRoot/templates_c";
        $smarty->cache_dir    = "$smartyRoot/cache";
        $smarty->config_dir   = "$smartyRoot/configs";
        $smarty->config_dir   = "$smartyRoot/configs";
        $this->smarty = $smarty;

    }
    public function _draw($xcontext)
    {
        $_datas= $xcontext->attr;
        foreach($_datas as $key=>$value){
            $$key = $value;    
            $this->smarty->assign($key,$value);
        }
        if($xcontext->have("debug") && $xcontext->debug==2)
            echo "<br>Smarty TPL:{$xcontext->_view}<br>";
        $this->smarty->display($xcontext->_view);
    }

}

/** 
 * @brief  Debug支持的拦截器
 */
class DebugSupportInter implements XScopeInterceptor
{
    private $debugLogger = NULL;
    /** 
        * @brief  支持sql语句的记录
        * 
        * @param $request 
        * @param $xcontext 
        * 
        * @return 
     */
    public function _before($request,$xcontext)
    {
        $sqlExec = XBox::get('SQLExecuter')  ;
        if($sqlExec !== null)
        {
            $this->debugLogger = ScopeSqlLog::echoLog($sqlExec);
        }
        
    }
    public function _after($request,$xcontext)
    {
        $this->debugLogger = NULL;
    }
}
