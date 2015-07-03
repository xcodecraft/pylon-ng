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

    public static function limit($start,$limit)
    {
        return new XDataLimit($start,$limit);
    }

}


/**
 * @ingroup utls
 * @brief  数据页，根据 limit start,limit分页
 */
class XDataLimit extends XProperty
{
    private $maxLimit = 1000; //最大调取条数

    private $default = 20;

    private $limit = 0;

    private $start = 0;

    private $isInit = false;

    public function __construct($start,$limit)
    {
        if(!$start) $start = 0;
        if(!$limit) $limit = $this->default;
        $this->limit = $limit>$this->maxLimit?$this->maxLimit:$limit;
        $this->start = $start;
        $this->totalRows = 0;
        $this->datas = array();
        $this->isInit = false;
    }
    public function initTotalRows($totalRows)
    {
        $this->totalRows= $totalRows;
        $this->isInit = true;
        $this->start = $this->start>$totalRows?$totalRows:$this->start;
        $this->limit = $this->limit>$totalRows?$totalRows:$this->limit;
        $this->limit = $this->limit>$this->maxLimit?$this->maxLimit:$this->limit;
    }

    public function toLimitStr()
    {
        $begin=$this->start;
        $count=$this->limit;
        return " limit $begin, $count ";
    }

    public function setData($datas)
    {
        $this->datas = $datas;
    }
}


