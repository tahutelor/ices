<?php

class Phone_Number_Type_Engine {

    public static $prefix_id = 'phone_number_type';
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
                , 'method' => 'phone_number_type_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Contact Category'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'phone_number_type_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Contact Category'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'phone_number_type_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Contact Category'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'phone_number_type/'
            , 'phone_number_type_engine' => ICES_Engine::$app['app_base_dir'] . 'phone_number_type/phone_number_type_engine'
            , 'phone_number_type_data_support' => ICES_Engine::$app['app_base_dir'] . 'phone_number_type/phone_number_type_data_support'
            , 'phone_number_type_renderer' => ICES_Engine::$app['app_base_dir'] . 'phone_number_type/phone_number_type_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'phone_number_type/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'phone_number_type/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Phone_Number_Type_Engine::path_get();
        get_instance()->load->helper($path->phone_number_type_data_support);

        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $phone_number_type = isset($data['phone_number_type']) ? Tools::_arr($data['phone_number_type']) : array();
        $phone_number_type_id = $phone_number_type['id'];
        $temp = Phone_Number_Type_Data_Support::phone_number_type_get($phone_number_type_id);
        $phone_number_type_db = count($temp)>0?$temp['phone_number_type']:array();

        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($phone_number_type['code']) && isset($phone_number_type['name']) && isset($phone_number_type['notes']) && isset($phone_number_type['phone_number_type_status'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Contact Category Parameter').' '.Lang::get('invalid',true,false);
                }
                if ($success === 1) {

                    $name = Tools::empty_to_null(Tools::_str($phone_number_type['name']));
                    $code = Tools::empty_to_null(Tools::_str($phone_number_type['code']));
                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($name) || is_null($code)) {
                        $success = 0;
                        $msg[] = 'Name' . ' ' . Lang::get('or') . ' ' . 'Code ' . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    $q = '
                        select 1
                        from phone_number_type sc
                        where sc.status > 0
                            and (sc.name = ' . $db->escape($name) . ' or sc.code = ' . $db->escape($code) . ')
                            and sc.id <> ' . $db->escape($phone_number_type_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = 'Name or Code exists';
                    }

                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($phone_number_type_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Contact Category';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'phone_number_type',
                                        'module_name' => Lang::get('Contact Category'),
                                        'module_engine' => 'phone_number_type_engine',
                                            ), $phone_number_type
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg,$temp_result['msg']);
                        }
                        
                        if($method === self::$prefix_method.'_inactive'){
                            //<editor-fold defaultstate="collapsed">
                            $q = '
                                select 1
                                from c_cc ccc
                                    inner join contact c
                                where ccc.phone_number_type_id = '.$db->escape($phone_number_type_id).'
                                    and c.status > 0
                                limit 1
                            ';
                            $rs = $db->query_array($q);
                            if(count($rs)>0){
                                $success = 0;
                                $msg[] = Lang::get('Contact Category').' '.Lang::get('used');
                            }
                            //</editor-fold>
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

        $phone_number_type_data = isset($data['phone_number_type']) ? $data['phone_number_type'] : array();

        $phone_number_type_db = Phone_Number_Type_Data_Support::phone_number_type_get($phone_number_type_data['id']);

        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $phone_number_type = array(
                    'name' => Tools::_str($phone_number_type_data['name']),
                    'code' => Tools::_str($phone_number_type_data['code']),
                    'notes' => Tools::empty_to_null(Tools::_str($phone_number_type_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $phone_number_type['phone_number_type_status'] = SI::type_default_type_get('Phone_Number_Type_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $phone_number_type['phone_number_type_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $phone_number_type['phone_number_type_status'] = 'inactive';
                        break;
                }

                $result['phone_number_type'] = $phone_number_type;
                
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }

    public function phone_number_type_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Phone_Number_Type_Engine::path_get();
        get_instance()->load->helper($path->phone_number_type_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fphone_number_type = $final_data['phone_number_type'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('phone_number_type', $fphone_number_type)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $phone_number_type_id = $db->last_insert_id();
            $result['trans_id'] = $phone_number_type_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'phone_number_type', $phone_number_type_id, $fphone_number_type['phone_number_type_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function phone_number_type_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Phone_Number_Type_Engine::path_get();
        get_instance()->load->helper($path->phone_number_type_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fphone_number_type = $final_data['phone_number_type'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $phone_number_type_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('phone_number_type', $fphone_number_type, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'phone_number_type', $phone_number_type_id, $fphone_number_type['phone_number_type_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function phone_number_type_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::phone_number_type_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>