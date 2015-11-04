<?php

    class  SQLConstructTest extends PHPUnit_Framework_TestCase
    {
        public function testSelect()
        {
            $statment = new \Pylon\SQLSelectStatement("user");
            $statment->where(" id = 10");
            $sql = $statment->generateSql();
            $this->assertEquals("select  *  from user where (  id = 10 );" ,$sql);
        }
        public function testInsert()
        {
            $statment = new \Pylon\SQLInsertStatment("user");
            $statment->columns('id,name');
            $statment->datas("'100','test'");
            $sql = $statment->generateSql();
            $this->assertEquals("insert user (id,name) values('100','test');" ,$sql);
        }
        public function testDel()
        {
            $statment = new \Pylon\SQLDelStatement("user");
            $statment->where(" id = 10");
            $sql = $statment->generateSql();
            $this->assertEquals("delete from user where ( id = 10 );" ,$sql);
        }
        public function testUpdate()
        {
            $statment = new \Pylon\SQLUpdateStatment("user");
            $statment->where(" id = 10");
            $statment->updateColumns( "id = 11 , name = 'sagitar'");
            $sql = $statment->generateSql();
            $this->assertEquals("update user set id = 11 , name = 'sagitar'  where (  id = 10 );" ,$sql);

        }

        public function testUpdate1()
        {
            $statment = new \Pylon\SQLUpdateStatment("user");
            $statment->where(" id=10");
            $data =  array("id"=>"11", "name"=>"'sagitar'");
            $statment->updateColumns( \Pylon\JoinUtls::jassoArray(', ','=',$data));
            $sql = $statment->generateSql();
            $this->assertEquals("update user set id=11, name='sagitar'  where (  id=10 );" ,$sql);

        }
        public function testA()
        {
            $arr = array ( "id"=> 3, "ver"=> 1, "product"=>  "sagitar", 
                        "name"=> "bbs stat" ,"rpttype" => 0,"descp"=> "bbs stat-desc", 
                        "dataid"=> 7);
            $statement = new \Pylon\SQLInsertStatment("test");
            $statement->columnArray(array_keys($arr));
            $statement->dataArray(array_values($arr));

        }
        public function  testAnds()
        {
            $subWhere1 = new \Pylon\TxtExpress("id = '1' ");
            $subWhere2 = new \Pylon\TxtExpress("adp_group.name like '%x%' ");
            $where = \Pylon\Express::ands("status = 1 ",$subWhere1 ,$subWhere2);
            $sql = $where->generateSql(new \Pylon\NULLStg());
            $this->assertEquals($sql,"status = 1  and id = '1'  and adp_group.name like '%x%' ");
        }
        private function mutiCondQuery($args)
        {
            $where = \Pylon\Express::ands(
                \Pylon\Express::eq('name',$args['name']),
                \Pylon\Express::ge('age',$args['age']),
                \Pylon\Express::eq('address',$args['address']));

            $statment = new \Pylon\SQLSelectStatement("user");
            $statment->where($where->generateSql(\Pylon\StgUtls::filterEmpty()));
            return  $statment->generateSql();
        }
        public function testMutiCondQuery()
        {
            $args['name'] = "google";
            $args['age']=18;
            $args['address']="beijing";
            $sql = $this->mutiCondQuery($args);
            $this->assertEquals($sql,'select  *  from user where ( (name = "google") and (age >= "18") and (address = "beijing") );');

            $args=null;
            $args['name'] = "";
            $args['age']=18;
            $args['address']="beijing";
            $sql = $this->mutiCondQuery($args);
            $this->assertEquals($sql,'select  *  from user where ( (age >= "18") and (address = "beijing") );');

            $args=null;
            $args['name'] = "";
            $args['age']=null;
            $args['address']="beijing";
            $sql = $this->mutiCondQuery($args);
            $this->assertEquals($sql,'select  *  from user where ( (address = "beijing") );');
        }

    }
