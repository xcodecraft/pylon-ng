

## FAQ

#### 如何使得某些自动生成的ID小并且连续?

设计 step为 1
``` sql
drop table if exists id_genter;
create table  id_genter
(
  id int(11)  not null
  obj varchar(30),
  step int(11)
) engine = innodb ;

insert into id_genter(id, obj, step) values(1,'other',10) ;
insert into id_genter(id, obj, step) values(1,'product',1) ;
```
