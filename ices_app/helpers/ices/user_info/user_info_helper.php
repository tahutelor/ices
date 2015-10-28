<?php
    class User_Info{
        
        private static $component_security = array();
        
        public static function get(){
            $result = array(
                "user_id"=>""
                ,"username"=>""
                ,"name"=>""
                ,'last_request'=>''
                ,'u_group'=>array()
            );
            
            if(strlen(get_instance()->session->userdata("user_id"))>0){
                $result['user_id'] = get_instance()->session->userdata("user_id");
                $result['username'] = get_instance()->session->userdata("username");
                $result['name'] = get_instance()->session->userdata("name");
                $result['last_request'] = get_instance()->session->userdata("last_request");;
                $result['u_group'] = get_instance()->session->userdata("u_group");;
            }
            
            return $result;
        }
        
        public static function flush(){
            
            get_instance()->session->unset_userdata(
                array("user_id"=>'',
                    "username"=>'',
                    "name"=>'',
                    'last_request'=>'',
                    'u_group'=>array()
                )
            );
        }
        
        public static function set($id){
            self::flush();
            $db = new DB();
            $id = $db->escape($id);
            $q = '
                select t1.id,t1.username
                    ,concat(t1.firstname,\' \',coalesce(t1.lastname,\'\')) name
                from employee t1
                where t1.id = '.$id.'
            ';
            $rs = $db->query_array_obj($q);

            if(count($rs)>0){
                $user = $rs[0];
                $data = array("user_id"=>$user->id
                    ,"username"=>$user->username
                    ,"name"=>$user->name
                    ,'last_request'=>Date('Y-m-d H:i:s')
                    ,'u_group'=>array()

                );                
                
                $q = '
                    select distinct ug.app_name, ug.id, ug.name role
                    from u_group ug
                    inner join employee_u_group eug on ug.id = eug.u_group_id
                    where eug.employee_id = '.$db->escape($user->id).'
                        and ug.status>0
                        and ug.u_group_status="active"
                ';
                $data['u_group'] = $db->query_array($q);
                get_instance()->session->set_userdata($data);            
            }
        }
        
        public function component_security_set(){
            $db_name = SI::type_get('ICES_Engine', 'ices', '$app_list')['app_db_conn_name'];
            $db = new DB(array('db_name'=>$db_name));
            $module = get_instance()->uri->segment(1);
            $q = '
                select t2.* 
                from u_group_security_component t1
                    inner join security_component t2 on t1.security_component_id = t2.id
                    inner join employee_u_group t3 on t1.u_group_id = t3.u_group_id
                where t2.module = '.$db->escape($module).' 
                    and t3.employee_id = '.$db->escape(User_Info::get()['user_id']).'

            ';
            $rs = $db->query_array($q);
            if(count($rs)>0) self::$component_security = $rs;
            
        }
        
        public function component_security_get(){
            return self::$component_security;
        }
        
        public function get_active_role(){
            $result = '';
            $app_name = ICES_Engine::$app['val'];
            foreach(User_Info::get()['u_group'] as $idx=>$row){
                if($app_name === $row['app_name']) $result = $row['role'];
            }
            return $result;
        }
    }
?>
