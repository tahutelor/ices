<?php

class Company_Engine {

    public static $prefix_id = 'company';
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
                , 'method' => 'company_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Company'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'company_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Company'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'company_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Company'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
                //</editor-fold>
        );

        //</editor-fold>
    }

    public static function path_get() {
        $path = array(
            'index' => ICES_Engine::$app['app_base_url'] . 'company/'
            , 'company_engine' => ICES_Engine::$app['app_base_dir'] . 'company/company_engine'
            , 'company_data_support' => ICES_Engine::$app['app_base_dir'] . 'company/company_data_support'
            , 'company_renderer' => ICES_Engine::$app['app_base_dir'] . 'company/company_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'company/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'company/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Company_Engine::path_get();
        get_instance()->load->helper($path->company_data_support);

        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $company = isset($data['company']) ? Tools::_arr($data['company']) : array();
        $company_id = $company['id'];
        $temp = Company_Data_Support::company_get($company_id);
        $company_db = count($temp) > 0 ? $temp['company'] : array();

        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($company['code']) && isset($company['name']) && isset($company['notes']) && isset($company['company_status'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Company Parameter') . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $name = Tools::empty_to_null(Tools::_str($company['name']));
                    $code = Tools::empty_to_null(Tools::_str($company['code']));
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
                        from company sc
                        where sc.status > 0
                            and (sc.name = ' . $db->escape($name) . ' or sc.code = ' . $db->escape($code) . ')
                            and sc.id <> ' . $db->escape($company_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = 'Name or Code exists';
                    }

                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($company_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Company';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                    array(
                                'module' => 'company',
                                'module_name' => Lang::get('Company'),
                                'module_engine' => 'company_engine',
                                    ), $company
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg, $temp_result['msg']);
                        }

                        if ($method === self::$prefix_method . '_inactive') {
                            //<editor-fold defaultstate="collapsed">
                            $q = '
                                select 1
                                from c_company ccc
                                    inner join contact c
                                where ccc.company_id = ' . $db->escape($company_id) . '
                                    and c.status > 0
                                limit 1
                            ';
                            $rs = $db->query_array($q);
                            if (count($rs) > 0) {
                                $success = 0;
                                $msg[] = Lang::get('Company') . ' ' . Lang::get('used');
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

        $company_data = isset($data['company']) ? $data['company'] : array();

        $company_db = Company_Data_Support::company_get($company_data['id']);

        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $company = array(
                    'name' => Tools::_str($company_data['name']),
                    'code' => Tools::_str($company_data['code']),
                    'notes' => Tools::empty_to_null(Tools::_str($company_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $company['company_status'] = SI::type_default_type_get('Company_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $company['company_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $company['company_status'] = 'inactive';
                        break;
                }

                $result['company'] = $company;

                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }

    public function company_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Company_Engine::path_get();
        get_instance()->load->helper($path->company_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcompany = $final_data['company'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('company', $fcompany)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $company_id = $db->last_insert_id();
            $result['trans_id'] = $company_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'company', $company_id, $fcompany['company_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function company_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Company_Engine::path_get();
        get_instance()->load->helper($path->company_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcompany = $final_data['company'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $company_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('company', $fcompany, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'company', $company_id, $fcompany['company_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function company_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::company_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>