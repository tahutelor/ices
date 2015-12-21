<?php

class Contact_Category_Engine {

    public static $prefix_id = 'contact_category';
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
                , 'method' => 'contact_category_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Contact Category'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'contact_category_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Contact Category'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'contact_category_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Contact Category'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'contact_category/'
            , 'contact_category_engine' => ICES_Engine::$app['app_base_dir'] . 'contact_category/contact_category_engine'
            , 'contact_category_data_support' => ICES_Engine::$app['app_base_dir'] . 'contact_category/contact_category_data_support'
            , 'contact_category_renderer' => ICES_Engine::$app['app_base_dir'] . 'contact_category/contact_category_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'contact_category/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'contact_category/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Contact_Category_Engine::path_get();
        get_instance()->load->helper($path->contact_category_data_support);

        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $contact_category = isset($data['contact_category']) ? Tools::_arr($data['contact_category']) : array();
        $contact_category_id = $contact_category['id'];
        $temp = Contact_Category_Data_Support::contact_category_get($contact_category_id);
        $contact_category_db = count($temp) > 0 ? $temp['contact_category'] : array();

        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($contact_category['code']) && isset($contact_category['name']) && isset($contact_category['notes']) && isset($contact_category['contact_category_status'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Contact Category Parameter') . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $name = Tools::empty_to_null(Tools::_str($contact_category['name']));
                    $code = Tools::empty_to_null(Tools::_str($contact_category['code']));
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
                        from contact_category sc
                        where sc.status > 0
                            and (sc.name = ' . $db->escape($name) . ' or sc.code = ' . $db->escape($code) . ')
                            and sc.id <> ' . $db->escape($contact_category_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = 'Name or Code exists';
                    }

                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($contact_category_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Contact Category';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                    array(
                                'module' => 'contact_category',
                                'module_name' => Lang::get('Contact Category'),
                                'module_engine' => 'contact_category_engine',
                                    ), $contact_category
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg, $temp_result['msg']);
                        }

                        if ($method === self::$prefix_method . '_inactive') {
                            //<editor-fold defaultstate="collapsed">
                            $q = '
                                select 1
                                from c_cc ccc
                                    inner join contact c
                                where ccc.contact_category_id = ' . $db->escape($contact_category_id) . '
                                    and c.status > 0
                                limit 1
                            ';
                            $rs = $db->query_array($q);
                            if (count($rs) > 0) {
                                $success = 0;
                                $msg[] = Lang::get('Contact Category') . ' ' . Lang::get('used');
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

        $contact_category_data = isset($data['contact_category']) ? $data['contact_category'] : array();

        $contact_category_db = Contact_Category_Data_Support::contact_category_get($contact_category_data['id']);

        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $contact_category = array(
                    'name' => Tools::_str(ucwords(strtolower($contact_category_data['name']))),
                    'code' => Tools::_str($contact_category_data['code']),
                    'notes' => Tools::empty_to_null(Tools::_str($contact_category_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $contact_category['contact_category_status'] = SI::type_default_type_get('Contact_Category_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $contact_category['contact_category_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $contact_category['contact_category_status'] = 'inactive';
                        break;
                }

                $result['contact_category'] = $contact_category;

                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }

    public function contact_category_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Contact_Category_Engine::path_get();
        get_instance()->load->helper($path->contact_category_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcontact_category = $final_data['contact_category'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('contact_category', $fcontact_category)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $contact_category_id = $db->last_insert_id();
            $result['trans_id'] = $contact_category_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'contact_category', $contact_category_id, $fcontact_category['contact_category_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function contact_category_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Contact_Category_Engine::path_get();
        get_instance()->load->helper($path->contact_category_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fcontact_category = $final_data['contact_category'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $contact_category_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('contact_category', $fcontact_category, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'contact_category', $contact_category_id, $fcontact_category['contact_category_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function contact_category_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::contact_category_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>