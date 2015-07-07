<?php
/**
 * @ingroup utls
 * @brief  数据页
 */
class XDataPage extends XProperty
{
    public function __construct($pageRows=20)
    {
        $this->pageRows= $pageRows;
        $this->curPage = 1;
        $this->totalRows = 0;
        $this->totalPages= 1;
        $this->isInit = false;
    }
    public function initTotalRows($totalRows)
    {
        $this->totalRows= $totalRows;
        $this->totalPages = ceil($totalRows/$this->pageRows );
        if($this->totalPages == 0 )
            $this->totalPages = 1;
        $this->isInit = true;
        if($this->curPage > $this->totalPages)
            $this->curPage = $this->totalPages;
    }
    /**
        * @brief
        *
        * @param $pageNum
        *
        * @return
     */
    public function gotoPage($pageNum)
    {
        if(empty($pageNum))
            $this->curPage = 1;
        $this->curPage= intval($pageNum);
        if($this->isInit && $this->curPage > $this->totalPages)
        {
            $this->curPage = $this->totalPages;
            return false;
        }
        return true;
    }
    /**
        * @brief 下一页
        *
        * @return
     */
    public function nextPage()
    {
        return $this->gotoPage($this->curPage + 1);
    }
    public function getRowRange(&$begin,&$count)
    {
        $begin = ($this->curPage -1 ) * $this->pageRows ;
        $count = $this->pageRows;
    }
    /**
     * @brief  clear DataPage data.
     *
     * @return void
     */
    public function clear()
    {

        $this->curPage = 1;
        $this->totalRows = 0;
        $this->totalPages= 1;
        $this->isInit = false;
    }
    public function toLimitStr()
    {
        $begin=0;
        $count=0;
        $this->getRowRange($begin,$count);
        return " limit $begin, $count ";
    }
}




