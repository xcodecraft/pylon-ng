#

## 数据页
```
$pageno = 1 ;
$page = new DataPage(20); 
$page->gotoPage($pageno);
$books = XQuery::obj()->list_book($page); 
```
