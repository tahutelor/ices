<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_Engine {

    public static $prefix_id = 'customer';
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
                , 'method' => 'customer_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Customer'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'customer_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Customer'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'customer_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Customer'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'customer/'
            , 'customer_engine' => ICES_Engine::$app['app_base_dir'] . 'customer/customer_engine'
            , 'customer_data_support' => ICES_Engine::$app['app_base_dir'] . 'customer/customer_data_support'
            , 'customer_renderer' => ICES_Engine::$app['app_base_dir'] . 'customer/customer_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'customer/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'customer/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();
        get_instance()->load->helper($path->customer_data_support);
        
        SI::module()->load_class(array('module'=>'phone_number','class_name'=>'phone_number_data_support'));
        SI::module()->load_class(array('module'=>'address','class_name'=>'address_data_support'));
        SI::module()->load_class(array('module'=>'mail_address','class_name'=>'mail_address_data_support'));
        
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $customer = isset($data['customer']) ? Tools::_arr($data['customer']) : array();
        $address = isset($data['address']) ? Tools::_arr($data['address']) : array();
        $phone_number = isset($data['phone_number']) ? Tools::_arr($data['phone_number']) : array();
        $mail_address = isset($data['mail_address']) ? Tools::_arr($data['mail_address']) : array();
        $customer_id = $customer['id'];
        $temp = Customer_Data_Support::customer_get($customer_id);
        $customer_db = isset($temp['customer'])?$temp['customer']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($customer['code']) 
                        && isset($customer['name']) 
                        && isset($customer['notes']) 
                        && isset($customer['customer_status'] ) 
                        && isset($customer['customer_type_id'] )
                        && isset($customer['si_customer_default'] )
                        && isset($data['address']) 
                        && isset($data['phone_number'])
                        && isset($data['mail_address'])
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Customer') 
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Address')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Phone Number')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Mail Address')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $customer_code = Tools::empty_to_null(Tools::_str($customer['code']));
                    $customer_name = Tools::empty_to_null(Tools::_str($customer['name']));
                    $customer_type_id = Tools::empty_to_null(Tools::_str($customer['customer_type_id']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($customer_code) 
                        || is_null($customer_name) 
                        || is_null($customer_type_id)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Name')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Customer Type')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    $q = '
                        select 1
                        from customer c
                        where c.status > 0
                            and (
                                c.name =    ' . $db->escape($customer_name) . '
                            )
                            and c.id <> ' . $db->escape($customer_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false);
                    }
                    
                    if(Tools::_bool($customer['si_customer_default'])){
                        $temp = Customer_Data_Support::si_customer_default_get();
                        if(count($temp)>0){
                            if($temp['customer']['id'] !== $customer_id){
                                $success = 0;
                                $msg[] = Lang::get('Sales Invoice - Customer Default')
                                    .' '.Lang::get('exists',true,false).' '.Tools::html_tag('strong',$temp['customer']['code']);
                            }
                        }
                    }
                    
                    
                    //<editor-fold defaultstate="collapsed" desc="Cutomer Type">
                    $q = '
                        select 1
                        from customer_type ct
                        where ct.status > 0
                            and ct.customer_type_status = "active"
                            and ct.id = '.$db->escape($customer_type_id).'
                    ';

                    if (!count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Customer Type')
                            . ' ' . Lang::get('invalid', true, false);
                    }
                    //</editor-fold>
                    
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
                        if (!count($customer_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Customer';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'customer',
                                        'module_name' => Lang::get('Customer'),
                                        'module_engine' => 'customer_engine',
                                            ), $customer
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

        $customer_data = isset($data['customer']) ? $data['customer'] : array();
        $address_data = Tools::_arr(isset($data['address']) ? $data['address'] : array());
        $phone_number_data = Tools::_arr(isset($data['phone_number']) ? $data['phone_number'] : array());
        $mail_address_data = Tools::_arr(isset($data['mail_address']) ? $data['mail_address'] : array());

        $temp_customer = Customer_Data_Support::customer_get($customer_data['id']);
        $customer_db = isset($temp_customer['customer'])?$temp_customer['customer']:array();
        
        $customer_id = $customer_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $customer = array(
                    'name' => Tools::_str($customer_data['name']),
                    'si_customer_default'=>Tools::_bool($customer_data['si_customer_default']),
                    'customer_type_id'=>Tools::_str($customer_data['customer_type_id']),
                    'birthdate'=>is_null($customer_data['birthdate'])?null:(Tools::_date($customer_data['birthdate'])),
                    'notes' => Tools::empty_to_null(Tools::_str($customer_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                $c_address = array();
                foreach ($address_data as $idx => $row) {
                    $c_address[] = array(
                        'customer_id' => $customer_id,
                        'address' => Tools::_str($row)
                    );
                }
                
                $c_phone_number = array();
                foreach ($phone_number_data as $idx => $row) {
                    $c_phone_number[] = array(
                        'customer_id' => $customer_id,
                        'phone_number_type_id' => $row['phone_number_type_id'],
                        'phone_number' => Tools::_str(preg_replace('/[^0-9]/', '', $row['phone_number']))
                    );
                }
                
                $c_mail_address = array();
                foreach ($mail_address_data as $idx => $row) {
                    $c_mail_address[] = array(
                        'customer_id' => $customer_id,
                        'mail_address' => Tools::_str($row)
                    );
                }

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $customer['customer_debit_amount'] = '0.00';
                        $customer['customer_credit_amount'] = '0.00';
                        $customer['customer_status'] = SI::type_default_type_get('Customer_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $customer['customer_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $customer['customer_status'] = 'inactive';
                        break;
                }

                $result['customer'] = $customer;
                $result['c_address'] = $c_address;
                $result['c_phone_number'] = $c_phone_number;
                $result['c_mail_address'] = $c_mail_address;
                
                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function customer_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();
        get_instance()->load->helper($path->customer_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcustomer = $final_data['customer'];
        $fc_address = $final_data['c_address'];
        $fc_phone_number = $final_data['c_phone_number'];
        $fc_mail_address = $final_data['c_mail_address'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fcustomer['code'] = SI::code_counter_get($db, 'customer');

        if (!$db->insert('customer', $fcustomer)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $customer_id = $db->fast_get('customer',
                array('code' => $fcustomer['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $customer_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'customer', $customer_id, $fcustomer['customer_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if ($success === 1) {
            foreach ($fc_address as $idx => $row) {
                $row['customer_id'] = $customer_id;
                if (!$db->insert('c_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if ($success === 1) {
            foreach ($fc_phone_number as $idx => $row) {
                $row['customer_id'] = $customer_id;
                if (!$db->insert('c_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }
        
        if ($success === 1) {
            foreach ($fc_mail_address as $idx => $row) {
                $row['customer_id'] = $customer_id;
                if (!$db->insert('c_mail_address', $row)) {
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

    public function customer_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();
        get_instance()->load->helper($path->customer_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcustomer = $final_data['customer'];
        $fc_address = $final_data['c_address'];
        $fc_phone = $final_data['c_phone_number'];
        $fc_mail_address = $final_data['c_mail_address'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $customer_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('customer', $fcustomer, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'customer', $customer_id, $fcustomer['customer_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if (!$db->query('delete from c_address where customer_id = ' . $db->escape($customer_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_address as $idx => $row) {
                if (!$db->insert('c_address', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from c_phone_number where customer_id = ' . $db->escape($customer_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_phone as $idx => $row) {
                if (!$db->insert('c_phone_number', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                    break;
                }
            }
        }

        if (!$db->query('delete from c_mail_address where customer_id = ' . $db->escape($customer_id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            foreach ($fc_mail_address as $idx => $row) {
                if (!$db->insert('c_mail_address', $row)) {
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

    public function customer_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::customer_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }
    
    public function customer_debit_credit_amount_add($db, $final_data,$module_type=''){
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();
        get_instance()->load->helper($path->customer_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
                
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        
        $fcustomer = $final_data['customer'];
        $fcustomer_amount_log = $final_data['customer_amount_log'];
        $customer_id = $fcustomer['customer_id'];
        $old_customer_db = Customer_Data_Support::customer_get($customer_id)['customer'];
        
        if(!in_array($module_type,array('debit','credit'))){
            $success = 0;
            $msg[] = 'Customer Debit Credit Amount Type'
                .' '.Lang::get('invalid');
        }
            
        if($success === 1){
            $q = '
                update customer
                set customer_'.$module_type.'_amount = 
                    customer_'.$module_type.'_amount 
                    + '.$db->escape($fcustomer['customer_'.$module_type.'_amount']).'
                where customer.id = '.$db->escape($customer_id).'
            ';

            if (!$db->query($q)) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        if($success === 1){
            $sal_param = array(
                'ref_type'=>$fcustomer_amount_log['ref_type'],
                'ref_id'=>$fcustomer_amount_log['ref_id'],
                'customer_id'=>$customer_id,
                'old_amount'=>$old_customer_db['customer_'.$module_type.'_amount'],
                'amount'=>$fcustomer['customer_'.$module_type.'_amount'],
                'new_amount'=>Tools::_float($old_customer_db['customer_'.$module_type.'_amount'])
                    + Tools::_float($fcustomer['customer_'.$module_type.'_amount']),
                'description'=>$fcustomer['description'],
                'modid'=>$modid,
                'moddate'=>$moddate
            );
            if (!$db->insert('customer_'.$module_type.'_amount_log', $sal_param)) {
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
