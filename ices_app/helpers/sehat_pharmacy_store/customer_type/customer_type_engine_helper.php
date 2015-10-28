<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_Type_Engine {

    public static $prefix_id = 'customer_type';
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
                , 'method' => 'customer_type_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Customer Type'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'customer_type_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Customer Type'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'customer_type_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Customer Type'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'customer_type/'
            , 'customer_type_engine' => ICES_Engine::$app['app_base_dir'] . 'customer_type/customer_type_engine'
            , 'customer_type_data_support' => ICES_Engine::$app['app_base_dir'] . 'customer_type/customer_type_data_support'
            , 'customer_type_renderer' => ICES_Engine::$app['app_base_dir'] . 'customer_type/customer_type_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'customer_type/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'customer_type/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Type_Engine::path_get();
        get_instance()->load->helper($path->customer_type_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $customer_type = isset($data['customer_type']) ? Tools::_arr($data['customer_type']) : array();
        $customer_type_id = $customer_type['id'];
        $temp = Customer_Type_Data_Support::customer_type_get($customer_type_id);
        $customer_type_db = isset($temp['customer_type'])?$temp['customer_type']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(
                    isset($customer_type['code']) 
                    && isset($customer_type['name']) 
                    && isset($customer_type['notes']) 
                    && isset($customer_type['customer_type_status'])
                    && isset($customer_type['customer_type_default'])
                    && isset($customer_type['notif_si_outstanding'])
                )){
                    $success = 0;
                    $msg[] = Lang::get('Customer Type') 
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $customer_type_code = Tools::empty_to_null(Tools::_str($customer_type['code']));
                    $customer_type_name = Tools::empty_to_null(Tools::_str($customer_type['name']));
                    $customer_type_default = Tools::_bool(Tools::_str($customer_type['customer_type_default']));
                    $notif_si_outstanding = Tools::_bool(Tools::_str($customer_type['notif_si_outstanding']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($customer_type_code) || is_null($customer_type_name)) {
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
                        from customer_type ct
                        where ct.status > 0
                            and (
                                ct.code = ' . $db->escape($customer_type_code) . '
                                or ct.name =    ' . $db->escape($customer_type_name) . '
                            )
                            and ct.id <> ' . $db->escape($customer_type_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false)
                        ;
                    }
                    
                    if($customer_type_default){
                        $q = '
                            select ct.code
                            from customer_type ct
                            where ct.status>0
                                and ct.customer_type_status = "active"
                                and ct.id <> '.$db->escape($customer_type_id).'
                                and ct.customer_type_default = 1
                        ';
                        $rs = $db->query_array($q);
                        if(count($rs)>0){
                            $success = 0;
                            $msg[] = Lang::get('Customer Type Default')
                                .' '.Lang::get('exists',true,false).' '.'<strong>'.$rs[0]['code'].'</strong>'
                            ;
                        }
                    }
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($customer_type_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Customer Type';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'customer_type',
                                        'module_name' => Lang::get('Customer Type'),
                                        'module_engine' => 'customer_type_engine',
                                            ), $customer_type
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg,$temp_result['msg']);
                        }
                        
                        if($method === self::$prefix_method.'_inactive'){
                            //<editor-fold defaultstate="collapsed">
                            
                            $q = '
                                select p.*
                                from product p
                                where p.customer_type_id = '.$db->escape($customer_type_id).'
                                    and p.status > 0
                            ';
                            $rs = $db->query_array($q);
                            if(count($rs)>0){
                                $success = 0;
                                $msg[] = Lang::get('Customer Type')
                                    .' '.Lang::get('used by',true,false).' '.Lang::get('Product').' <strong>'.$rs[0]['code'].'</strong>'
                                ;
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

        $customer_type_data = isset($data['customer_type']) ? $data['customer_type'] : array();

        $temp_customer_type = Customer_Type_Data_Support::customer_type_get($customer_type_data['id']);
        $customer_type_db = isset($temp_customer_type['customer_type'])?$temp_customer_type['customer_type']:array();
        
        $customer_type_id = $customer_type_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $customer_type = array(
                    'notif_si_outstanding'=>Tools::_bool(Tools::_str($customer_type_data['notif_si_outstanding'])),
                    'customer_type_default'=>Tools::_bool(Tools::_str($customer_type_data['customer_type_default'])),
                    'name' => Tools::_str($customer_type_data['name']),
                    'code' => Tools::_str($customer_type_data['code']),
                    'notes' => Tools::empty_to_null(Tools::_str(isset($customer_type_data['notes'])?$customer_type_data['notes']:'')),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
               
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $customer_type['customer_type_status'] = SI::type_default_type_get('Customer_Type_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $customer_type['customer_type_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $customer_type['customer_type_status'] = 'inactive';
                        break;
                }

                $result['customer_type'] = $customer_type;

                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function customer_type_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Type_Engine::path_get();
        get_instance()->load->helper($path->customer_type_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcustomer_type = $final_data['customer_type'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fcustomer_type['code'] = $fcustomer_type['code'];

        if (!$db->insert('customer_type', $fcustomer_type)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $customer_type_id = $db->fast_get('customer_type',
                array('code' => $fcustomer_type['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $customer_type_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'customer_type', $customer_type_id, $fcustomer_type['customer_type_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function customer_type_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Type_Engine::path_get();
        get_instance()->load->helper($path->customer_type_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcustomer_type = $final_data['customer_type'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $customer_type_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('customer_type', $fcustomer_type, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'customer_type', $customer_type_id, $fcustomer_type['customer_type_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function customer_type_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::customer_type_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }
    

}

?>
