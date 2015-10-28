<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Unit_Engine {

    public static $prefix_id = 'unit';
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
                , 'method' => 'unit_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Unit'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'unit_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Unit'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'unit_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Unit'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'unit/'
            , 'unit_engine' => ICES_Engine::$app['app_base_dir'] . 'unit/unit_engine'
            , 'unit_data_support' => ICES_Engine::$app['app_base_dir'] . 'unit/unit_data_support'
            , 'unit_renderer' => ICES_Engine::$app['app_base_dir'] . 'unit/unit_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'unit/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'unit/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Unit_Engine::path_get();
        get_instance()->load->helper($path->unit_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $unit = isset($data['unit']) ? Tools::_arr($data['unit']) : array();
        $unit_id = $unit['id'];
        $temp = Unit_Data_Support::unit_get($unit_id);
        $unit_db = isset($temp['unit'])?$temp['unit']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(isset($unit['code']) 
                        && isset($unit['name']) 
                        && isset($unit['notes']) 
                        && isset($unit['unit_status'] ) 
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Unit') 
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $unit_code = Tools::empty_to_null(Tools::_str($unit['code']));
                    $unit_name = Tools::empty_to_null(Tools::_str($unit['name']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($unit_code) 
                        || is_null($unit_name)
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
                        from unit c
                        where c.status > 0
                            and (
                                c.code =    ' . $db->escape($unit_code) . '
                                or c.name =    ' . $db->escape($unit_name) . '
                            )
                            and c.id <> ' . $db->escape($unit_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                            .' '.Lang::get('or',true,false).' '.Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false);
                    }
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($unit_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Unit';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'unit',
                                        'module_name' => Lang::get('Unit'),
                                        'module_engine' => 'unit_engine',
                                            ), $unit
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

        $unit_data = isset($data['unit']) ? $data['unit'] : array();

        $temp_unit = Unit_Data_Support::unit_get($unit_data['id']);
        $unit_db = isset($temp_unit['unit'])?$temp_unit['unit']:array();
        
        $unit_id = $unit_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $unit = array(
                    'code' => Tools::_str($unit_data['code']),
                    'name' => Tools::_str($unit_data['name']),
                    'notes' => Tools::empty_to_null(Tools::_str($unit_data['notes'])),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );

                switch ($method) {
                    case self::$prefix_method . '_add':
                        $unit['unit_status'] = SI::type_default_type_get('Unit_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $unit['unit_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $unit['unit_status'] = 'inactive';
                        break;
                }

                $result['unit'] = $unit;
                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function unit_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Unit_Engine::path_get();
        get_instance()->load->helper($path->unit_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $funit = $final_data['unit'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('unit', $funit)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $unit_id = $db->fast_get('unit',
                array('code' => $funit['code'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $unit_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'unit', $unit_id, $funit['unit_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function unit_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Unit_Engine::path_get();
        get_instance()->load->helper($path->unit_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $funit = $final_data['unit'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $unit_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('unit', $funit, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'unit', $unit_id, $funit['unit_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function unit_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::unit_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }

}

?>
