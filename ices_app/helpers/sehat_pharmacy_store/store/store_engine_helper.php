<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Store_Engine {

    public static $prefix_id = 'store';
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
                , 'method' => 'store_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Store'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'store_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Store'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'store_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Store'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'store/'
            , 'store_engine' => ICES_Engine::$app['app_base_dir'] . 'store/store_engine'
            , 'store_data_support' => ICES_Engine::$app['app_base_dir'] . 'store/store_data_support'
            , 'store_renderer' => ICES_Engine::$app['app_base_dir'] . 'store/store_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'store/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'store/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Store_Engine::path_get();
        get_instance()->load->helper($path->store_data_support);
        
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'phone_number/phone_number_engine');
        $path = Phone_Number_Engine::path_get();
        get_instance()->load->helper($path->phone_number_data_support);
        
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'address/address_engine');
        $path = Address_Engine::path_get();
        get_instance()->load->helper($path->address_data_support);
        
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $store = isset($data['store']) ? Tools::_arr($data['store']) : array();
        $address = isset($data['address']) ? Tools::_arr($data['address']) : array();
        $phone_number = isset($data['phone_number']) ? Tools::_arr($data['phone_number']) : array();
        $store_id = $store['id'];
        $temp = Store_Data_Support::store_get($store_id);
        $store_db = isset($temp['store'])?$temp['store']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($store['code']) && isset($store['name']) && isset($store['notes']) && isset($store['store_status'] ) && isset($data['address']) && isset($data['phone_number'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Store') 
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Address')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Phone Number')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_code = Tools::empty_to_null(Tools::_str($store['code']));
                    $store_name = Tools::empty_to_null(Tools::_str($store['name']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($store_code) || is_null($store_name)) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Name')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    $q = '
                        select 1
                        from store s
                        where s.status > 0
                            and (
                                s.code = ' . $db->escape($store_code) . '
                                or s.name =    ' . $db->escape($store_name) . '
                            )
                            and s.id <> ' . $db->escape($store_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false);
                    }
                    
                    
                    //<editor-fold defaultstate="collapsed" desc="Address">
                    $temp_result = Address_Data_Support::address_validate($address);

                    if($temp_result['success']!== 1){
                        $success = 0;
                        $msg = array_merge($msg,$temp_result['msg']);
                    }

                    //</editor-fold>

                    //<editor-fold defaultstate="collapsed" desc="Phone Number">
                        $temp_result = Phone_Number_Data_Support::phone_number_validate($phone_number);
                        
                        if($temp_result['success']!== 1){
                            $success = 0;
                            $msg = array_merge($msg,$temp_result['msg']);
                        }

                        //</editor-fold>
                    
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($store_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Store';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'store',
                                        'module_name' => Lang::get('Store'),
                                        'module_engine' => 'store_engine',
                                            ), $store
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

        $store_data = isset($data['store']) ? $data['store'] : array();
        $address_data = Tools::_arr(isset($data['address']) ? $data['address'] : array());
        $phone_number_data = Tools::_arr(isset($data['phone_number']) ? $data['phone_number'] : array());

        $temp_store = Store_Data_Support::store_get($store_data['id']);
        $store_db = isset($temp_store['store'])?$temp_store['store']:array();
        
        $store_id = $store_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $store = array(
                    'name' => Tools::_str($store_data['name']),
                    'notes' => Tools::empty_to_null(Tools::_str(isset($store_data['notes'])?$store_data['notes']:'')),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                $s_address = array();
                foreach ($address_data as $idx => $row) {
                    $s_address[] = array(
                        'store_id' => $store_id,
                        'address' => Tools::_str($row)
                    );
                }
                
                $s_phone_number = array();
                foreach ($phone_number_data as $idx => $row) {
                    $s_phone_number[] = array(
                        'store_id' => $store_id,
                        'phone_number_type_id' => $row['phone_number_type_id'],
                        'phone_number' => Tools::_str(preg_replace('/[^0-9]/', '', $row['phone_number']))
                    );
                }

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $store['store_status'] = SI::type_default_type_get('Store_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $store['store_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $store['store_status'] = 'inactive';
                        break;
                }

                $result['store'] = $store;
                $result['s_address'] = $s_address;
                $result['s_phone_number'] = $s_phone_number;

                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function store_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Store_Engine::path_get();
        get_instance()->load->helper($path->store_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fstore = $final_data['store'];
        $fs_address = $final_data['s_address'];
        $fs_phone_number = $final_data['s_phone_number'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fstore['code'] = SI::code_counter_get($db, 'store');

        if (!$db->insert('store', $fstore)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $store_id = $db->fast_get('store',
                array('code' => $fstore['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $store_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'store', $store_id, $fstore['store_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if ($success === 1) {
            foreach ($fs_address as $idx => $row) {
                $row['store_id'] = $store_id;
                if (!$db->insert('s_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fs_phone_number as $idx => $row) {
                $row['store_id'] = $store_id;
                if (!$db->insert('s_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function store_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Store_Engine::path_get();
        get_instance()->load->helper($path->store_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fstore = $final_data['store'];
        $fs_address = $final_data['s_address'];
        $fc_phone = $final_data['s_phone_number'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $store_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('store', $fstore, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'store', $store_id, $fstore['store_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if (!$db->query('delete from s_address where store_id = ' . $db->escape($store_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fs_address as $idx => $row) {
                if (!$db->insert('s_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from s_phone_number where store_id = ' . $db->escape($store_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_phone as $idx => $row) {
                if (!$db->insert('s_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function store_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::store_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>
