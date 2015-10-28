<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Warehouse_Engine {

    public static $prefix_id = 'warehouse';
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
                , 'method' => 'warehouse_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Warehouse'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'warehouse_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Warehouse'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'warehouse_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Warehouse'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'warehouse/'
            , 'warehouse_engine' => ICES_Engine::$app['app_base_dir'] . 'warehouse/warehouse_engine'
            , 'warehouse_data_support' => ICES_Engine::$app['app_base_dir'] . 'warehouse/warehouse_data_support'
            , 'warehouse_renderer' => ICES_Engine::$app['app_base_dir'] . 'warehouse/warehouse_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'warehouse/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'warehouse/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Warehouse_Engine::path_get();
        get_instance()->load->helper($path->warehouse_data_support);
        
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'phone_number/phone_number_engine');
        $path = Phone_Number_Engine::path_get();
        get_instance()->load->helper($path->phone_number_data_support);
        
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'address/address_engine');
        $path = Address_Engine::path_get();
        get_instance()->load->helper($path->address_data_support);
        
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $warehouse = isset($data['warehouse']) ? Tools::_arr($data['warehouse']) : array();
        $address = isset($data['address']) ? Tools::_arr($data['address']) : array();
        $phone_number = isset($data['phone_number']) ? Tools::_arr($data['phone_number']) : array();
        $warehouse_id = $warehouse['id'];
        $temp = Warehouse_Data_Support::warehouse_get($warehouse_id);
        $warehouse_db = isset($temp['warehouse'])?$temp['warehouse']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($warehouse['code']) && isset($warehouse['name']) && isset($warehouse['notes']) && isset($warehouse['warehouse_status'] ) && isset($data['address']) && isset($data['phone_number'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Warehouse') 
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Address')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Phone Number')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $warehouse_code = Tools::empty_to_null(Tools::_str($warehouse['code']));
                    $warehouse_name = Tools::empty_to_null(Tools::_str($warehouse['name']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($warehouse_code) || is_null($warehouse_name)) {
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
                        from warehouse w
                        where w.status > 0
                            and (
                            w.code = ' . $db->escape($warehouse_code) . '
                            or w.name =    ' . $db->escape($warehouse_name) . '
                            )
                            and w.id <> ' . $db->escape($warehouse_id) . '
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
                        if (!count($warehouse_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Warehouse';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'warehouse',
                                        'module_name' => Lang::get('Warehouse'),
                                        'module_engine' => 'warehouse_engine',
                                            ), $warehouse
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

        $warehouse_data = isset($data['warehouse']) ? $data['warehouse'] : array();
        $address_data = Tools::_arr(isset($data['address']) ? $data['address'] : array());
        $phone_number_data = Tools::_arr(isset($data['phone_number']) ? $data['phone_number'] : array());

        $temp_warehouse = Warehouse_Data_Support::warehouse_get($warehouse_data['id']);
        $warehouse_db = isset($temp_warehouse['warehouse'])?$temp_warehouse['warehouse']:array();
        
        $warehouse_id = $warehouse_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $warehouse = array(
                    'name' => Tools::_str($warehouse_data['name']),
                    'notes' => Tools::empty_to_null(Tools::_str(isset($warehouse_data['notes'])?$warehouse_data['notes']:'')),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                $w_address = array();
                foreach ($address_data as $idx => $row) {
                    $w_address[] = array(
                        'warehouse_id' => $warehouse_id,
                        'address' => Tools::_str($row)
                    );
                }
                
                $w_phone_number = array();
                foreach ($phone_number_data as $idx => $row) {
                    $w_phone_number[] = array(
                        'warehouse_id' => $warehouse_id,
                        'phone_number_type_id' => $row['phone_number_type_id'],
                        'phone_number' => Tools::_str(preg_replace('/[^0-9]/', '', $row['phone_number']))
                    );
                }

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $warehouse['warehouse_status'] = SI::type_default_type_get('Warehouse_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $warehouse['warehouse_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $warehouse['warehouse_status'] = 'inactive';
                        break;
                }

                $result['warehouse'] = $warehouse;
                $result['w_address'] = $w_address;
                $result['w_phone_number'] = $w_phone_number;

                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function warehouse_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Warehouse_Engine::path_get();
        get_instance()->load->helper($path->warehouse_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fwarehouse = $final_data['warehouse'];
        $fw_address = $final_data['w_address'];
        $fw_phone_number = $final_data['w_phone_number'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fwarehouse['code'] = SI::code_counter_get($db, 'warehouse');

        if (!$db->insert('warehouse', $fwarehouse)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $warehouse_id = $db->fast_get('warehouse',
                array('code' => $fwarehouse['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $warehouse_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'warehouse', $warehouse_id, $fwarehouse['warehouse_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if ($success === 1) {
            foreach ($fw_address as $idx => $row) {
                $row['warehouse_id'] = $warehouse_id;
                if (!$db->insert('w_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fw_phone_number as $idx => $row) {
                $row['warehouse_id'] = $warehouse_id;
                if (!$db->insert('w_phone_number', $row)) {
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

    public function warehouse_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Warehouse_Engine::path_get();
        get_instance()->load->helper($path->warehouse_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fwarehouse = $final_data['warehouse'];
        $fw_address = $final_data['w_address'];
        $fc_phone = $final_data['w_phone_number'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $warehouse_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('warehouse', $fwarehouse, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'warehouse', $warehouse_id, $fwarehouse['warehouse_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if (!$db->query('delete from w_address where warehouse_id = ' . $db->escape($warehouse_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fw_address as $idx => $row) {
                if (!$db->insert('w_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from w_phone_number where warehouse_id = ' . $db->escape($warehouse_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_phone as $idx => $row) {
                if (!$db->insert('w_phone_number', $row)) {
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

    public function warehouse_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::warehouse_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>
