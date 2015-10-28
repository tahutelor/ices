<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class BOS_Bank_Account_Engine {

    public static $prefix_id = 'bos_bank_account';
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
                , 'method' => 'bos_bank_account_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('BOS Bank Account'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'bos_bank_account_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('BOS Bank Account'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'bos_bank_account_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('BOS Bank Account'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'bos_bank_account/'
            , 'bos_bank_account_engine' => ICES_Engine::$app['app_base_dir'] . 'bos_bank_account/bos_bank_account_engine'
            , 'bos_bank_account_data_support' => ICES_Engine::$app['app_base_dir'] . 'bos_bank_account/bos_bank_account_data_support'
            , 'bos_bank_account_renderer' => ICES_Engine::$app['app_base_dir'] . 'bos_bank_account/bos_bank_account_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'bos_bank_account/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'bos_bank_account/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = BOS_Bank_Account_Engine::path_get();
        get_instance()->load->helper($path->bos_bank_account_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $bos_bank_account = isset($data['bos_bank_account']) ? Tools::_arr($data['bos_bank_account']) : array();
        $bos_bank_account_id = $bos_bank_account['id'];
        $temp = BOS_Bank_Account_Data_Support::bos_bank_account_get($bos_bank_account_id);
        $bos_bank_account_db = isset($temp['bos_bank_account'])?$temp['bos_bank_account']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($bos_bank_account['code']) 
                        && isset($bos_bank_account['bank_account']) 
                        && isset($bos_bank_account['notes']) 
                        && isset($bos_bank_account['bos_bank_account_status'] ) 
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('BOS Bank Account') 
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $bos_bank_account_code = Tools::empty_to_null(Tools::_str($bos_bank_account['code']));
                    $bank_account = Tools::empty_to_null(Tools::_str($bos_bank_account['bank_account']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($bos_bank_account_code) 
                        || is_null($bank_account)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Bank Account')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    $q = '
                        select 1
                        from bos_bank_account bba
                        where bba.status > 0
                            and (
                                bba.code =    ' . $db->escape($bos_bank_account_code) . '
                                or bba.bank_account =    ' . $db->escape($bank_account) . '
                            )
                            and bba.id <> ' . $db->escape($bos_bank_account_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                            .' '.Lang::get('or',true,false).' '.Lang::get('Bank Account')
                            . ' ' . Lang::get('exists', true, false);
                    }
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($bos_bank_account_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid BOS Bank Account';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'bos_bank_account',
                                        'module_name' => Lang::get('BOS Bank Account'),
                                        'module_engine' => 'bos_bank_account_engine',
                                            ), $bos_bank_account
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

        $bos_bank_account_data = isset($data['bos_bank_account']) ? $data['bos_bank_account'] : array();

        $temp_bos_bank_account = BOS_Bank_Account_Data_Support::bos_bank_account_get($bos_bank_account_data['id']);
        $bos_bank_account_db = isset($temp_bos_bank_account['bos_bank_account'])?$temp_bos_bank_account['bos_bank_account']:array();
        
        $bos_bank_account_id = $bos_bank_account_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $bos_bank_account = array(
                    'code' => Tools::_str($bos_bank_account_data['code']),
                    'bank_account' => Tools::_str($bos_bank_account_data['bank_account']),
                    'notes' => Tools::empty_to_null(Tools::_str($bos_bank_account_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $bos_bank_account['bos_bank_account_status'] = SI::type_default_type_get('BOS_Bank_Account_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $bos_bank_account['bos_bank_account_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $bos_bank_account['bos_bank_account_status'] = 'inactive';
                        break;
                }

                $result['bos_bank_account'] = $bos_bank_account;
                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function bos_bank_account_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = BOS_Bank_Account_Engine::path_get();
        get_instance()->load->helper($path->bos_bank_account_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fbos_bank_account = $final_data['bos_bank_account'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('bos_bank_account', $fbos_bank_account)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $bos_bank_account_id = $db->fast_get('bos_bank_account',
                array('code' => $fbos_bank_account['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $bos_bank_account_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'bos_bank_account', $bos_bank_account_id, $fbos_bank_account['bos_bank_account_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function bos_bank_account_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = BOS_Bank_Account_Engine::path_get();
        get_instance()->load->helper($path->bos_bank_account_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fbos_bank_account = $final_data['bos_bank_account'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $bos_bank_account_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('bos_bank_account', $fbos_bank_account, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'bos_bank_account', $bos_bank_account_id, $fbos_bank_account['bos_bank_account_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function bos_bank_account_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::bos_bank_account_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>
