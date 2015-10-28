<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Category_Engine {

    public static $prefix_id = 'product_category';
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
                , 'method' => 'product_category_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Product Category'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'product_category_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product Category'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'product_category_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product Category'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'product_category/'
            , 'product_category_engine' => ICES_Engine::$app['app_base_dir'] . 'product_category/product_category_engine'
            , 'product_category_data_support' => ICES_Engine::$app['app_base_dir'] . 'product_category/product_category_data_support'
            , 'product_category_renderer' => ICES_Engine::$app['app_base_dir'] . 'product_category/product_category_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'product_category/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'product_category/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Category_Engine::path_get();
        get_instance()->load->helper($path->product_category_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $product_category = isset($data['product_category']) ? Tools::_arr($data['product_category']) : array();
        $product_category_id = $product_category['id'];
        $temp = Product_Category_Data_Support::product_category_get($product_category_id);
        $product_category_db = isset($temp['product_category'])?$temp['product_category']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(
                    isset($product_category['code']) 
                    && isset($product_category['name']) 
                    && isset($product_category['notes']) 
                    && isset($product_category['product_category_status'])
                    && isset($product_category['prnt_product_category_id'])
                )){
                    $success = 0;
                    $msg[] = Lang::get('Product Category')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $product_category_code = Tools::empty_to_null(Tools::_str($product_category['code']));
                    $product_category_name = Tools::empty_to_null(Tools::_str($product_category['name']));
                    $prnt_product_category_id = Tools::empty_to_null(Tools::_str($product_category['prnt_product_category_id']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($product_category_code) || is_null($product_category_name)) {
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
                        from product_category pc
                        where pc.status > 0
                            and (
                            pc.code = ' . $db->escape($product_category_code) . '
                            or pc.name =    ' . $db->escape($product_category_name) . '
                            and pc.id <> ' . $db->escape($product_category_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Code')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false)
                        ;
                    }
                    
                    if(!is_null($prnt_product_category_id)){
                        $q = '
                            select 1
                            from product_category pc
                            where pc.status>0
                                and pc.product_category_status = "active"
                                and pc.id <> '.$db->escape($product_category_id).'
                        ';

                        if(!count($db->query_array($q))>0){
                            $success = 0;
                            $msg[] = Lang::get('Parent')
                                .' '.Lang::get('invalid',true,false)
                            ;
                        }
                    }
                    
                    if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($product_category_db) > 0) {
                            $success = 0;
                            $msg[] = 'Invalid Product Category';
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'product_category',
                                        'module_name' => Lang::get('Product Category'),
                                        'module_engine' => 'product_category_engine',
                                            ), $product_category
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg,$temp_result['msg']);
                        }
                        
                        if($method === self::$prefix_method.'_inactive'){
                            //<editor-fold defaultstate="collapsed">
                            $q = '
                                select pc.code
                                from product_category pc
                                where pc.prnt_product_category_id = '.$db->escape($product_category_id).'
                                    and pc.status > 0
                            ';
                            $rs = $db->query_array($q);
                            if(count($rs)>0){
                                $success = 0;
                                $msg[] = Lang::get('Product Category')
                                    .' '.Lang::get('used by',true,false).' '.Lang::get('Product').' '.$rs[0]['code']
                                ;
                            }
                            
                            $q = '
                                select p.*
                                from product p
                                where p.product_category_id = '.$db->escape($product_category_id).'
                                    and p.status > 0
                            ';
                            $rs = $db->query_array($q);
                            if(count($rs)>0){
                                $success = 0;
                                $msg[] = Lang::get('Product Category')
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

        $product_category_data = isset($data['product_category']) ? $data['product_category'] : array();

        $temp_product_category = Product_Category_Data_Support::product_category_get($product_category_data['id']);
        $product_category_db = isset($temp_product_category['product_category'])?$temp_product_category['product_category']:array();
        
        $product_category_id = $product_category_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $product_category = array(
                    'prnt_product_category_id'=>Tools::empty_to_null($product_category_data['prnt_product_category_id']),
                    'name' => Tools::_str($product_category_data['name']),
                    'code' => Tools::_str($product_category_data['code']),
                    'notes' => Tools::empty_to_null(Tools::_str(isset($product_category_data['notes'])?$product_category_data['notes']:'')),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
               
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $product_category['product_category_status'] = SI::type_default_type_get('Product_Category_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $product_category['product_category_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $product_category['product_category_status'] = 'inactive';
                        break;
                }

                $result['product_category'] = $product_category;

                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function product_category_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Category_Engine::path_get();
        get_instance()->load->helper($path->product_category_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_category = $final_data['product_category'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fproduct_category['code'] = $fproduct_category['code'];

        if (!$db->insert('product_category', $fproduct_category)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $product_category_id = $db->last_insert_id();
            $result['trans_id'] = $product_category_id;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product_category', $product_category_id, $fproduct_category['product_category_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        if($success === 1){
            $temp_result = self::product_category_route_set(array(
                'db'=>$db,
                'product_category_id'=>$product_category_id,
            ));
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function product_category_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Category_Engine::path_get();
        get_instance()->load->helper($path->product_category_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_category = $final_data['product_category'];


        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_category_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('product_category', $fproduct_category, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product_category', $product_category_id, $fproduct_category['product_category_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        if($success === 1){
            $temp_result = self::product_category_route_set(array(
                'db'=>$db,
                'product_category_id'=>$product_category_id,
            ));
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function product_category_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::product_category_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }
    
    public function product_category_route_set($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        
        $db = $param['db'];
        $product_category_id = $param['product_category_id'];
        
        $q ='
            select * from product_category pc where id = '.$db->escape($product_category_id).'
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $product_category = $rs[0];
            $prnt_product_category_id = $product_category['prnt_product_category_id'];
            $route = '/'.$product_category['id'].'/';
            $old_route = $product_category['route'];
            
            //try to get parent route
            $q ='
                select * from product_category pc where id = '.$db->escape($prnt_product_category_id).'
            ';
            $rs = $db->query_array($q);
            if(count($rs)>0){
                $prnt_product_category = $rs[0];
                $route = $prnt_product_category['route'].$product_category_id.'/';            
            }
            
            // update my route
            $q = '
                update product_category pc
                set route = '.$db->escape($route).'
                where pc.id = '.$db->escape($product_category_id).'
            ';
            if(!$db->query($q)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
            
            //update all child route
            if($success === 1){
                $q = '
                    update product_category pc
                    set route = concat('.$db->escape($route).',substr(pc.route,length('.$db->escape($old_route).')+1))
                    where pc.route like '.$db->escape($old_route.'%').'
                        and '.$db->escape($old_route).' != ""
                        and pc.id <> '.$db->escape($product_category_id).'
                        
                ';
                if(!$db->query($q)){
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                }
            }
            
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

}

?>
