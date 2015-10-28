<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supplier_Engine {

    public static $prefix_id = 'supplier';
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
                , 'method' => 'supplier_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Supplier'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'supplier_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Supplier'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'supplier_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Supplier'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'supplier/'
            , 'supplier_engine' => ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_engine'
            , 'supplier_data_support' => ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_data_support'
            , 'supplier_renderer' => ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'supplier/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'supplier/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Supplier_Engine::path_get();
        get_instance()->load->helper($path->supplier_data_support);
        
        SI::module()->load_class(array('module'=>'phone_number','class_name'=>'phone_number_data_support'));
        SI::module()->load_class(array('module'=>'address','class_name'=>'address_data_support'));
        SI::module()->load_class(array('module'=>'mail_address','class_name'=>'mail_address_data_support'));
        
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $supplier = isset($data['supplier']) ? Tools::_arr($data['supplier']) : array();
        $address = isset($data['address']) ? Tools::_arr($data['address']) : array();
        $phone_number = isset($data['phone_number']) ? Tools::_arr($data['phone_number']) : array();
        $mail_address = isset($data['mail_address']) ? Tools::_arr($data['mail_address']) : array();
        $supplier_id = $supplier['id'];
        $temp = Supplier_Data_Support::supplier_get($supplier_id);
        $supplier_db = isset($temp['supplier'])?$temp['supplier']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($supplier['code']) 
                        && isset($supplier['name']) 
                        && isset($supplier['notes']) 
                        && isset($supplier['supplier_status'] ) 
                        && isset($data['address']) 
                        && isset($data['phone_number'])
                        && isset($data['mail_address'])
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Supplier') 
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Address')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Phone Number')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Mail Address')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $supplier_code = Tools::empty_to_null(Tools::_str($supplier['code']));
                    $supplier_name = Tools::empty_to_null(Tools::_str($supplier['name']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($supplier_code) 
                        || is_null($supplier_name)
                    ) {
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
                        from supplier c
                        where c.status > 0
                            and (
                                c.name =    ' . $db->escape($supplier_name) . '
                            )
                            and c.id <> ' . $db->escape($supplier_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Name')
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
                    
                    //<editor-fold defaultstate="collapsed" desc="Mail Address">
                    $temp_result = Mail_Address_Data_Support::mail_address_validate($mail_address);

                    if($temp_result['success']!== 1){
                        $success = 0;
                        $msg = array_merge($msg,$temp_result['msg']);
                    }




                    //</editor-fold>
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($supplier_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Supplier';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'supplier',
                                        'module_name' => Lang::get('Supplier'),
                                        'module_engine' => 'supplier_engine',
                                            ), $supplier
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

        $supplier_data = isset($data['supplier']) ? $data['supplier'] : array();
        $address_data = Tools::_arr(isset($data['address']) ? $data['address'] : array());
        $phone_number_data = Tools::_arr(isset($data['phone_number']) ? $data['phone_number'] : array());
        $mail_address_data = Tools::_arr(isset($data['mail_address']) ? $data['mail_address'] : array());

        $temp_supplier = Supplier_Data_Support::supplier_get($supplier_data['id']);
        $supplier_db = isset($temp_supplier['supplier'])?$temp_supplier['supplier']:array();
        
        $supplier_id = $supplier_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $supplier = array(
                    'name' => Tools::_str($supplier_data['name']),
                    'birthdate'=>is_null($supplier_data['birthdate'])?null:(Tools::_date($supplier_data['birthdate'])),
                    'notes' => Tools::empty_to_null(Tools::_str($supplier_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                $sup_address = array();
                foreach ($address_data as $idx => $row) {
                    $sup_address[] = array(
                        'supplier_id' => $supplier_id,
                        'address' => Tools::_str($row)
                    );
                }
                
                $sup_phone_number = array();
                foreach ($phone_number_data as $idx => $row) {
                    $sup_phone_number[] = array(
                        'supplier_id' => $supplier_id,
                        'phone_number_type_id' => $row['phone_number_type_id'],
                        'phone_number' => Tools::_str(preg_replace('/[^0-9]/', '', $row['phone_number']))
                    );
                }
                
                $sup_mail_address = array();
                foreach ($mail_address_data as $idx => $row) {
                    $sup_mail_address[] = array(
                        'supplier_id' => $supplier_id,
                        'mail_address' => Tools::_str($row)
                    );
                }

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $supplier['supplier_debit_amount'] = '0.00';
                        $supplier['supplier_credit_amount'] = '0.00';
                        $supplier['supplier_status'] = SI::type_default_type_get('Supplier_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $supplier['supplier_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $supplier['supplier_status'] = 'inactive';
                        break;
                }

                $result['supplier'] = $supplier;
                $result['sup_address'] = $sup_address;
                $result['sup_phone_number'] = $sup_phone_number;
                $result['sup_mail_address'] = $sup_mail_address;
                
                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function supplier_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Supplier_Engine::path_get();
        get_instance()->load->helper($path->supplier_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsupplier = $final_data['supplier'];
        $fsup_address = $final_data['sup_address'];
        $fsup_phone_number = $final_data['sup_phone_number'];
        $fsup_mail_address = $final_data['sup_mail_address'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fsupplier['code'] = SI::code_counter_get($db, 'supplier');

        if (!$db->insert('supplier', $fsupplier)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $supplier_id = $db->fast_get('supplier',
                array('code' => $fsupplier['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $supplier_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'supplier', $supplier_id, $fsupplier['supplier_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if ($success === 1) {
            foreach ($fsup_address as $idx => $row) {
                $row['supplier_id'] = $supplier_id;
                if (!$db->insert('sup_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fsup_phone_number as $idx => $row) {
                $row['supplier_id'] = $supplier_id;
                if (!$db->insert('sup_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }
        
        if ($success === 1) {
            foreach ($fsup_mail_address as $idx => $row) {
                $row['supplier_id'] = $supplier_id;
                if (!$db->insert('sup_mail_address', $row)) {
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

    public function supplier_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Supplier_Engine::path_get();
        get_instance()->load->helper($path->supplier_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsupplier = $final_data['supplier'];
        $fsup_address = $final_data['sup_address'];
        $fsup_phone = $final_data['sup_phone_number'];
        $fsup_mail_address = $final_data['sup_mail_address'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $supplier_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('supplier', $fsupplier, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'supplier', $supplier_id, $fsupplier['supplier_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if (!$db->query('delete from sup_address where supplier_id = ' . $db->escape($supplier_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fsup_address as $idx => $row) {
                if (!$db->insert('sup_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from sup_phone_number where supplier_id = ' . $db->escape($supplier_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fsup_phone as $idx => $row) {
                if (!$db->insert('sup_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from sup_mail_address where supplier_id = ' . $db->escape($supplier_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fsup_mail_address as $idx => $row) {
                if (!$db->insert('sup_mail_address', $row)) {
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

    public function supplier_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::supplier_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

    public function supplier_debit_credit_amount_add($db, $final_data,$module_type=''){
        //<editor-fold defaultstate="collapsed">
        $path = Supplier_Engine::path_get();
        get_instance()->load->helper($path->supplier_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
                
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        
        $fsupplier = $final_data['supplier'];
        $fsupplier_amount_log = $final_data['supplier_amount_log'];
        $supplier_id = $fsupplier['supplier_id'];
        $old_supplier_db = Supplier_Data_Support::supplier_get($supplier_id)['supplier'];
        
        if(!in_array($module_type,array('debit','credit'))){
            $success = 0;
            $msg[] = 'Supplier Debit Credit Amount Type'
                .' '.Lang::get('invalid');
        }
            
        if($success === 1){
            $q = '
                update supplier
                set supplier_'.$module_type.'_amount = 
                    supplier_'.$module_type.'_amount 
                    + '.$db->escape($fsupplier['supplier_'.$module_type.'_amount']).'
                where supplier.id = '.$db->escape($supplier_id).'
            ';

            if (!$db->query($q)) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        if($success === 1){
            $sal_param = array(
                'ref_type'=>$fsupplier_amount_log['ref_type'],
                'ref_id'=>$fsupplier_amount_log['ref_id'],
                'supplier_id'=>$supplier_id,
                'old_amount'=>$old_supplier_db['supplier_'.$module_type.'_amount'],
                'amount'=>$fsupplier['supplier_'.$module_type.'_amount'],
                'new_amount'=>Tools::_float($old_supplier_db['supplier_'.$module_type.'_amount'])
                    + Tools::_float($fsupplier['supplier_'.$module_type.'_amount']),
                'description'=>$fsupplier['description'],
                'modid'=>$modid,
                'moddate'=>$moddate
            );
            if (!$db->insert('supplier_'.$module_type.'_amount_log', $sal_param)) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    
}

?>
