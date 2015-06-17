set names utf8;

drop table if exists id_genter;
create table id_genter
(
    id          int(11) not null,
    obj         varchar(30),
    step        int(11)
) ;
insert into id_genter(id, obj, step) values(1, 'other', 10);

drop table if exists `hero`;
CREATE TABLE `hero` (
    id          int(11)         default null,
    ver         int(11)         default null,
    createtime  datetime        default null,
    updatetime  datetime        default null,

    name        varchar(50)     not null ,

    PRIMARY     KEY (id),
) ENGINE=InnoDB DEFAULT CHARSET=utf8

