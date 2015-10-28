<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment_Type_Engine {

    public static $prefix_id = 'payment_type';
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
                , 'method' => 'payment_type_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Payment Type'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'payment_type_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Payment Type'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'payment_type_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Payment Type'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'payment_type/'
            , 'payment_type_engine' => ICES_Engine::$app['app_base_dir'] . 'payment_type/payment_type_engine'
            , 'payment_type_data_support' => ICES_Engine::$app['app_base_dir'] . 'payment_type/payment_type_data_support'
            , 'payment_type_renderer' => ICES_Engine::$app['app_base_dir'] . 'payment_type/payment_type_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'payment_type/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'payment_type/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Payment_Type_Engine::path_get();
        get_instance()->load->helper($path->payment_type_data_support);
        SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $payment_type = isset($data['payment_type']) ? Tools::_arr($data['payment_type']) : array();
        $payment_type_id = $payment_type['id'];
        $temp = Payment_Type_Data_Support::payment_type_get($payment_type_id);
        $payment_type_db = isset($temp['payment_type'])?$temp['payment_type']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($payment_type['code']) 
                        && isset($payment_type['name']) 
                        && isset($payment_type['notes'])
                        && isset($payment_type['customer_bank_account'])
                        && isset($payment_type['supplier_bank_account'])
                        && isset($payment_type['bos_bank_account_id_default'])
                        && isset($payment_type['payment_type_status'] ) 
                        && isset($payment_type['change_amount'] )
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Payment Type') 
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $payment_type_code = Tools::empty_to_null(Tools::_str($payment_type['code']));
                    $payment_type_name = Tools::empty_to_null(Tools::_str($payment_type['name']));
                    $bos_bank_account_id_default = Tools::empty_to_null(Tools::_str($payment_type['bos_bank_account_id_default']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($payment_type_code) 
                        || is_null($payment_type_name)
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
                        from payment_type c
                        where c.status > 0
                            and (
                                c.code =    ' . $db->escape($payment_type_code) . '
                                or c.name =    ' . $db->escape($payment_type_name) . '
                            )
                            and c.id <> ' . $db->escape($payment_type_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                            .' '.Lang::get('or',true,false).' '.Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false);
                    }
                    
                    if(!is_null($bos_bank_account_id_default)){
                        //<editor-fold defaultstate="collapsed">
                        $t_bos_bank_account = BOS_Bank_Account_Data_Support::bos_bank_account_get($bos_bank_account_id_default);
                        $local_status = 1;
                        if(!count($t_bos_bank_account)>0){
                            $local_status = 0;
                        }
                        else{
                            if($t_bos_bank_account['bos_bank_account']['bos_bank_account_status']!== 'active'){
                                $local_status = 0;
                            }
                        }
                        
                        if($local_status !== 1){
                            $success = 0;
                            $msg[] = Lang::get('BOS Bank Account Default')
                                .' '.Lang::get('invalid');
                        }
                        //</editor-fold>
                    }
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($payment_type_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Payment Type';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'payment_type',
                                        'module_name' => Lang::get('Payment Type'),
                                        'module_engine' => 'payment_type_engine',
                                            ), $payment_type
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

        $payment_type_data = isset($data['payment_type']) ? $data['payment_type'] : array();

        $temp_payment_type = Payment_Type_Data_Support::payment_type_get($payment_type_data['id']);
        $payment_type_db = isset($temp_payment_type['payment_type'])?$temp_payment_type['payment_type']:array();
        
        $payment_type_id = $payment_type_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $payment_type = array(
                    'code' => Tools::_str($payment_type_data['code']),
                    'name' => Tools::_str($payment_type_data['name']),
                    'supplier_bank_account' => Tools::_bool($payment_type_data['supplier_bank_account']),
                    'customer_bank_account' => Tools::_bool($payment_type_data['customer_bank_account']),
                    'bos_bank_account_id_default' => Tools::empty_to_null($payment_type_data['bos_bank_account_id_default']),
                    'change_amount' => Tools::_bool($payment_type_data['change_amount']),
                    'notes' => Tools::empty_to_null(Tools::_str($payment_type_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $payment_type['payment_type_status'] = SI::type_default_type_get('Payment_Type_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $payment_type['payment_type_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $payment_type['payment_type_status'] = 'inactive';
                        break;
                }

                $result['payment_type'] = $payment_type;
                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function payment_type_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Payment_Type_Engine::path_get();
        get_instance()->load->helper($path->payment_type_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpayment_type = $final_data['payment_type'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('payment_type', $fpayment_type)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $payment_type_id = $db->fast_get('payment_type',
                array('code' => $fpayment_type['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $payment_type_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'payment_type', $payment_type_id, $fpayment_type['payment_type_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function payment_type_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Payment_Type_Engine::path_get();
        get_instance()->load->helper($path->payment_type_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpayment_type = $final_data['payment_type'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $payment_type_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('payment_type', $fpayment_type, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'payment_type', $payment_type_id, $fpayment_type['payment_type_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function payment_type_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::payment_type_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>
