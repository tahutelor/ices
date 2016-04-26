<?php

class Warehouse_Category_Engine {

    public static $prefix_id = 'warehouse_category';
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
                , 'method' => 'warehouse_category_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Warehouse Category'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'warehouse_category_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Warehouse Category'), true, true, false, false, true))
                        , array('val' => 'success', 'lower_all' => true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'warehouse_category_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Warehouse Category'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'warehouse_category/'
            , 'warehouse_category_engine' => ICES_Engine::$app['app_base_dir'] . 'warehouse_category/warehouse_category_engine'
            , 'warehouse_category_data_support' => ICES_Engine::$app['app_base_dir'] . 'warehouse_category/warehouse_category_data_support'
            , 'warehouse_category_renderer' => ICES_Engine::$app['app_base_dir'] . 'warehouse_category/warehouse_category_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'warehouse_category/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'warehouse_category/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Warehouse_Category_Engine::path_get();
        get_instance()->load->helper($path->warehouse_category_data_support);

        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $warehouse_category = isset($data['warehouse_category']) ? Tools::_arr($data['warehouse_category']) : array();
        $warehouse_category_id = $warehouse_category['id'];
        $temp = Warehouse_Category_Data_Support::warehouse_category_get($warehouse_category_id);
        $warehouse_category_db = count($temp) > 0 ? $temp['warehouse_category'] : array();

        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($warehouse_category['code']) && isset($warehouse_category['name']) && isset($warehouse_category['notes']) && isset($warehouse_category['warehouse_category_status'])
                        )) {
                    $success = 0;
                    $msg[] = Lang::get('Warehouse Category Parameter') . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $name = Tools::empty_to_null(Tools::_str($warehouse_category['name']));
                    $code = Tools::empty_to_null(Tools::_str($warehouse_category['code']));
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
                        from warehouse_category sc
                        where sc.status > 0
                            and (sc.name = ' . $db->escape($name) . ' or sc.code = ' . $db->escape($code) . ')
                            and sc.id <> ' . $db->escape($warehouse_category_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = 'Name or Code exists';
                    }

                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($warehouse_category_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Warehouse Category';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                    array(
                                'module' => 'warehouse_category',
                                'module_name' => Lang::get('Warehouse Category'),
                                'module_engine' => 'warehouse_category_engine',
                                    ), $warehouse_category
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
                                where ccc.warehouse_category_id = ' . $db->escape($warehouse_category_id) . '
                                    and c.status > 0
                                limit 1
                            ';
                            $rs = $db->query_array($q);
                            if (count($rs) > 0) {
                                $success = 0;
                                $msg[] = Lang::get('Warehouse Category') . ' ' . Lang::get('used');
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

        $warehouse_category_data = isset($data['warehouse_category']) ? $data['warehouse_category'] : array();

        $warehouse_category_db = Warehouse_Category_Data_Support::warehouse_category_get($warehouse_category_data['id']);

        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $warehouse_category = array(
                    'name' => Tools::_str(ucwords(strtolower($warehouse_category_data['name']))),
//                    'code' => Tools::_str($warehouse_category_data['code']),
                    'notes' => Tools::empty_to_null(Tools::_str($warehouse_category_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $warehouse_category['warehouse_category_status'] = SI::type_default_type_get('Warehouse_Category_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $warehouse_category['warehouse_category_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $warehouse_category['warehouse_category_status'] = 'inactive';
                        break;
                }

                $result['warehouse_category'] = $warehouse_category;

                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }

    public function warehouse_category_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Warehouse_Category_Engine::path_get();
        get_instance()->load->helper($path->warehouse_category_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fwarehouse_category = $final_data['warehouse_category'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('warehouse_category', $fwarehouse_category)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $warehouse_category_id = $db->last_insert_id();
            $result['trans_id'] = $warehouse_category_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'warehouse_category', $warehouse_category_id, $fwarehouse_category['warehouse_category_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function warehouse_category_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Warehouse_Category_Engine::path_get();
        get_instance()->load->helper($path->warehouse_category_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fwarehouse_category = $final_data['warehouse_category'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $warehouse_category_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('warehouse_category', $fwarehouse_category, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'warehouse_category', $warehouse_category_id, $fwarehouse_category['warehouse_category_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function warehouse_category_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::warehouse_category_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>