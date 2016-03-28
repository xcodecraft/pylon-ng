<?php
    class  SQLConstructTest extends PHPUnit_Framework_TestCase
    {
        public function testSelect()
        {
            $statment = new SQLSelectStatement("user");
            $statment->where(" id = 10");
            $sql = $statment->generateSql();
            $this->assertEquals("select  *  from user where (  id = 10 );" ,$sql);
        }
        public function testInsert()
        {
            $statment = new SQLInsertStatment("user");
            $statment->columns('id,name');
            $statment->datas("'100','test'");
            $sql = $statment->generateSql();
            $this->assertEquals("insert user (id,name) values('100','test');" ,$sql);
        }
        public function testDel()
        {
            $statment = new SQLDelStatement("user");
            $statment->where(" id = 10");
            $sql = $statment->generateSql();
            $this->assertEquals("delete from user where ( id = 10 );" ,$sql);
        }
        public function testUpdate()
        {
            $statment = new SQLUpdateStatment("user");
            $statment->where(" id = 10");
            $statment->updateColumns( "id = 11 , name = 'sagitar'");
            $sql = $statment->generateSql();
            $this->assertEquals("update user set id = 11 , name = 'sagitar'  where (  id = 10 );" ,$sql);

        }

        public function testUpdate1()
        {
            $statment = new SQLUpdateStatment("user");
            $statment->where(" id=10");
            $data =  array("id"=>"11", "name"=>"'sagitar'");
            $statment->updateColumns( JoinUtls::jassoArray(', ','=',$data));
            $sql = $statment->generateSql();
            $this->assertEquals("update user set id=11, name='sagitar'  where (  id=10 );" ,$sql);

        }
        public function testA()
        {
            $arr = array ( "id"=> 3, "ver"=> 1, "product"=>  "sagitar", 
                        "name"=> "bbs stat" ,"rpttype" => 0,"descp"=> "bbs stat-desc", 
                        "dataid"=> 7);
            $statement = new SQLInsertStatment("test");
            $statement->columnArray(array_keys($arr));
            $statement->dataArray(array_values($arr));

        }

    }


