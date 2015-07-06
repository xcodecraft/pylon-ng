#

## 数据页
```
$pageno = 1 ;
$page = new DataPage(20); 
$page->gotoPage($pageno);
$books = XQuery::obj()->list_book($page); 
```

## 复杂查询

``` php
//列出加个大于10小于10.5的书籍
$books  = XQuery::obj()->list_Book_by_price(QL('? > 10 and ? < 10.5 ')); 
//列出书名以c开头的书籍
$books2 = XQuery::obj()->list_Book_by_name(QL('? like "c%"')); 
//列出书名以c开头并价格小于100的书
$books3 = XQuery::obj()->list_Book_by_name_price(QL('? like "c%"'),QL('? < 100'));
```
