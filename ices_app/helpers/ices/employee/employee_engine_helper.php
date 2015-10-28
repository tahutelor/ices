<?php

class Employee_Engine {

    public static $prefix_id = 'employee';
    public static $prefix_method;
    public static $status_list;

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        self::$prefix_method = self::$prefix_id;

        self::$status_list = array(
            //<editor-fold defaultstate="collapsed">
            array(
                'val' => ''
                , 'text' => ''
                , 'method' => 'employee_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Employee'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'employee_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Employee'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'employee_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Employee'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
                //</editor-fold>
        );

        //</editor-fold>
    }

    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'employee/'
            , 'employee_engine' => ICES_Engine::$app['app_base_dir'] . 'employee/employee_engine'
            , 'employee_data_support' => ICES_Engine::$app['app_base_dir'] . 'employee/employee_data_support'
            , 'employee_renderer' => ICES_Engine::$app['app_base_dir'] . 'employee/employee_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'employee/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'employee/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Employee_Engine::path_get();
        get_instance()->load->helper($path->employee_data_support);

        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $employee = isset($data['employee']) ? Tools::_arr($data['employee']) : array();
        $u_group = isset($data['u_group']) ? Tools::_arr($data['u_group']) : array();
        $employee_id = $employee['id'];
        $temp = Employee_Data_Support::employee_get($employee_id);
        $employee_db = isset($temp['employee'])?$temp['employee']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($employee['firstname']) && isset($employee['lastname']) && isset($employee['username']) && isset($employee['password']) && isset($employee['employee_status'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Employee Parameter') . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $firstname = Tools::empty_to_null(Tools::_str($employee['firstname']));
                    $lastname = Tools::empty_to_null(Tools::_str($employee['lastname']));
                    $username = Tools::empty_to_null(Tools::_str($employee['username']));
                    $password = Tools::empty_to_null(Tools::_str($employee['password']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($firstname) || is_null($lastname) || is_null($username) || is_null($password)) {
                        $success = 0;
                        $msg[] = Lang::get('First Name')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Last Name')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Username')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Password')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if(!count($u_group)>0){
                        $success = 0;
                        $msg[] = 'User Group'.' '.Lang::get('empty',true,false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    $q = '
                        select 1
                        from employee sc
                        where sc.status > 0
                            and sc.username = ' . $db->escape($username) . '
                            and sc.id <> ' . $db->escape($employee_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = 'Username exists';
                    }
                    
                    //<editor-fold defaultstate="collapsed" desc="u_group">
                    if($success === 1){
                        $q_u_group_id = '';
                        foreach($u_group as $idx=>$row){
                            $u_group_id = Tools::_str(isset($row['id'])?$row['id']:'');
                            $q_u_group_id.=($q_u_group_id !== ''?',':'').$db->escape($u_group_id);
                        }
                        $q = '
                            select distinct ug.* 
                            from u_group ug
                            where ug.id in ('.$q_u_group_id.')
                                and ug.status > 0
                        ';
                        $t_u_group = $db->query_array($q);
                        if(count($t_u_group)!= count($u_group)){
                            $success = 0;
                            $msg[] = 'Duplicate User Group';
                        }

                        if($success === 1){
                            foreach($t_u_group as $idx=>$row){
                                foreach($t_u_group as $idx2=>$row2){
                                    if($row['app_name'] === $row2['app_name'] and $idx !== $idx2){
                                        $success = 0;
                                        $msg[] = 'Duplicate User Group in similar APP';
                                    }
                                    if($success !== 1) break;
                                }
                                if($success !== 1) break;
                            }
                        }
                    }
                    
                    
                    //</editor-fold>
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($employee_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Employee';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'employee',
                                        'module_name' => Lang::get('Employee'),
                                        'module_engine' => 'employee_engine',
                                            ), $employee
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg,$temp_result['msg']);
                        }
                        //</editor-fold>
                    }
                }

                //</editor-fold>
                break;
            default:
                $success = 0;
                $msg[] = 'Invalid Method';
                break;
        }
        $result['success'] = $success;
        $result['msg'] = $msg;

        return $result;
        //</editor-fold>
    }

    public static function adjust($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();

        $employee_data = isset($data['employee']) ? $data['employee'] : array();
        $u_group_data = isset($data['u_group']) ? $data['u_group'] : array();

        $temp = Employee_Data_Support::employee_get($employee_data['id']);
        $employee_db = isset($temp['employee'])?$temp['employee']:array();
        
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $pwd = Tools::_str($employee_data['password']);

                if (count($db->query_array_obj('select password from employee where id = ' . $db->escape($employee_data['id']) . ' and password=' . $db->escape($pwd) . '')) === 0) {
                    $pwd = md5($pwd);
                }

                $employee = array(
                    'firstname' => Tools::_str($employee_data['firstname']),
                    'lastname' => Tools::_str($employee_data['lastname']),
                    'username' => Tools::_str($employee_data['username']),
                    'password' => $pwd,
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $employee['employee_status'] = SI::type_default_type_get('Employee_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $employee['employee_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $employee['employee_status'] = 'inactive';
                        break;
                }

                $employee_u_group = array();
                foreach($u_group_data as $idx=>$row){
                    $employee_u_group[] = array(
                        'employee_id'=>$employee_data['id'],
                        'u_group_id'=>$row['id'],
                        
                    );
                }
                
                $result['employee_u_group'] = $employee_u_group;
                $result['employee'] = $employee;

                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }

    public function employee_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Employee_Engine::path_get();
        get_instance()->load->helper($path->employee_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $femployee = $final_data['employee'];
        $femployee_u_group = $final_data['employee_u_group'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('employee', $femployee)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $employee_id = $db->fast_get('employee'
                ,array('username' => $femployee['username'], 'status' => 1)
            )[0]['id'];
            $result['trans_id'] = $employee_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'employee', $employee_id, $femployee['employee_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if($success === 1){
            foreach($femployee_u_group as $idx=>$row){
                $param_eug = $row;
                $param_eug['employee_id'] = $employee_id;
                
                if (!$db->insert('employee_u_group', $param_eug)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;                    
                }
                
                if ($success == 1) {
                    $param_eugl = array(
                        'employee_id'=>$param_eug['employee_id'],
                        'u_group_id'=>$param_eug['u_group_id'],
                        'modid'=>$modid,
                        'moddate'=>$moddate,
                    );
                    if (!$db->insert('employee_u_group_log', $param_eugl)) {
                        $msg[] = $db->_error_message();
                        $db->trans_rollback();
                        $success = 0;

                    }
                }
                
                if($success !== 1) break;
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function employee_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Employee_Engine::path_get();
        get_instance()->load->helper($path->employee_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $femployee = $final_data['employee'];
        $femployee_u_group = $final_data['employee_u_group'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $employee_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('employee', $femployee, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'employee', $employee_id, $femployee['employee_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if($success === 1){
            if(!$db->query('delete from employee_u_group where employee_id = '.$db->escape($employee_id))){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
                
            }
            
            if($success === 1){
                foreach($femployee_u_group as $idx=>$row){
                    $param_eug = $row;

                    if (!$db->insert('employee_u_group', $param_eug)) {
                        $msg[] = $db->_error_message();
                        $db->trans_rollback();
                        $success = 0;

                    }
                    
                    if ($success == 1) {
                        $param_eugl = array(
                            'employee_id'=>$param_eug['employee_id'],
                            'u_group_id'=>$param_eug['u_group_id'],
                            'modid'=>$modid,
                            'moddate'=>$moddate,
                        );
                        if (!$db->insert('employee_u_group_log', $param_eugl)) {
                            $msg[] = $db->_error_message();
                            $db->trans_rollback();
                            $success = 0;

                        }
                    }

                    if($success !== 1) break;
                }
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function employee_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::employee_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>
