<?php

/**
 * @brief
 * XQuery::obj()->get_Author_by_id($author->id()) ;
 * XQuery::arr()->get_Author_by_id($author->id()) ;
 */
class XQuery
{
    /**
     * @brief
     * XQuery::obj()->get_Author_by_id($author->id()) ;
     *
     * @return 实体对象
     */
    static public function obj()
    {
        return  XQueryObj::ins();
    }
    /**
     * @brief
     *
     * @return
     */
    static public function arr()
    {
        return  XQueryArr::ins() ;
    }

    // 添加 sql() 函数
    static public function sql()
    {
        return  XBox::get(XBOx::SQLE);
    }

}
