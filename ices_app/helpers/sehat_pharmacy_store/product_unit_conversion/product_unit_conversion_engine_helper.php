<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Unit_Conversion_Engine {

    public static $prefix_id = 'product_unit_conversion';
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
                , 'method' => 'product_unit_conversion_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Product Unit Conversion'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'product_unit_conversion_active'
                , 'next_allowed_status' => array('delete')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product Unit Conversion'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'delete'
                , 'text' => 'DELETED'
                , 'method' => 'product_unit_conversion_delete'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Delete')
                        , array('val' => Lang::get(array('Product Unit Conversion'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'product_unit_conversion/'
            , 'product_unit_conversion_engine' => ICES_Engine::$app['app_base_dir'] . 'product_unit_conversion/product_unit_conversion_engine'
            , 'product_unit_conversion_data_support' => ICES_Engine::$app['app_base_dir'] . 'product_unit_conversion/product_unit_conversion_data_support'
            , 'product_unit_conversion_renderer' => ICES_Engine::$app['app_base_dir'] . 'product_unit_conversion/product_unit_conversion_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'product_unit_conversion/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'product_unit_conversion/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Unit_Conversion_Engine::path_get();

        SI::module()->load_class(array('module'=>'unit','class_name'=>'unit_data_support'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
        SI::module()->load_class(array('module'=>'product_unit_conversion','class_name'=>'product_unit_conversion_data_support'));
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $product_unit_conversion = isset($data['product_unit_conversion']) ? Tools::_arr($data['product_unit_conversion']) : array();
        $product_unit_conversion_id = $product_unit_conversion['id'];
        $temp = Product_Unit_Conversion_Data_Support::product_unit_conversion_get($product_unit_conversion_id);
        $product_unit_conversion_db = isset($temp['product_unit_conversion'])?$temp['product_unit_conversion']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
                //<editor-fold defaultstate="collapsed">
                if (!(
                    isset($product_unit_conversion['product_id']) 
                    && isset($product_unit_conversion['qty'])
                    && isset($product_unit_conversion['unit_id'])
                    && isset($product_unit_conversion['qty2'])
                    && isset($product_unit_conversion['unit_id2'])
                )){
                    $success = 0;
                    $msg[] = Lang::get('Product Unit Conversion') 
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $product_id = Tools::empty_to_null(Tools::_str($product_unit_conversion['product_id']));
                    $qty = Tools::empty_to_null(Tools::_str($product_unit_conversion['qty']));
                    $unit_id = Tools::empty_to_null(Tools::_str($product_unit_conversion['unit_id']));
                    $qty2 = Tools::empty_to_null(Tools::_str($product_unit_conversion['qty2']));
                    $unit_id2 = Tools::empty_to_null(Tools::_str($product_unit_conversion['unit_id2']));
                    
                    
                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($product_id) 
                        || is_null($qty) 
                        || is_null($unit_id)
                        || is_null($qty2)
                        || is_null($unit_id2)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Product')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Qty')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Unit')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Qty2')
                            . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Unit2')
                            . ' ' . Lang::get('empty', true, false)
                        ;
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    if(Tools::_float($qty)<Tools::_float('0')){
                        $success = 0;
                        $msg[] = Lang::get('Qty')
                            .' '.Lang::get('invalid',true,false);
                    }
                    
                    if(Tools::_float($qty2)<Tools::_float('0')){
                        $success = 0;
                        $msg[] = Lang::get('Qty2')
                            .' '.Lang::get('invalid',true,false);
                    }
                    
                    if($unit_id === $unit_id2){
                        $success = 0;
                        $msg[] = Lang::get('Unit and Unit 2')
                            .' '.Lang::get('similar',true,false);
                    }
                                 
                    //<editor-fold defaultstate="collapsed" desc="Unit">
                    $temp = Unit_Data_Support::unit_get($unit_id);
                    $t_success = 1;
                    if(!count($temp)>0){
                        $t_success = 0;                        
                    }
                    else{
                        if($temp['unit']['unit_status']!== 'active') $t_success = 0;
                    }
                    
                    if($t_success !== 1){
                        $success = 0;
                            $msg[] = Lang::get('Unit')
                                .' '.Lang::get('invalid')
                            ;
                    }
                    //</editor-fold>
                    
                    //<editor-fold defaultstate="collapsed" desc="Unit 2">
                    $temp = Unit_Data_Support::unit_get($unit_id2);
                    $t_success = 1;
                    if(!count($temp)>0){
                        $t_success = 0;                        
                    }
                    else{
                        if($temp['unit']['unit_status']!== 'active') $t_success = 0;
                    }
                    if($t_success !== 1){
                        $success = 0;
                            $msg[] = Lang::get('Unit')
                                .' '.Lang::get('invalid',true,false);
                            ;
                    }
                    //</editor-fold>
                       
                    //<editor-fold defaultstate="collapsed" desc="Product">
                    $t_product = Product_Data_Support::product_get($product_id);
                    if(count($t_product)>0 === FALSE){
                        $success = 0;
                        $msg[] = Lang::get('Product')
                            .' '.Lang::get('invalid',true,false)
                        ;
                    }
                    //</editor-fold>
                    
                    //<editor-fold defaultstate="collapsed" desc="Product Unit Conversion Exists">
                    $q = '
                        select 1 
                        from p_u_conversion puc 
                        where puc.product_id  = '.$db->escape($product_id).'
                            and (
                                puc.unit_id = '.$db->escape($unit_id).'
                                and puc.unit_id2 = '.$db->escape($unit_id2).'
                            )
                            and puc.id <> '.$db->escape($product_unit_conversion_id).'
                    ';
                    if(count($db->query_array($q))>0){
                        $success = 0;
                        $msg[] = Lang::get('Product Unit Conversion')
                            .' '.Lang::get('exists',true,false)
                        ;
                    }
                    //</editor-fold>
                    
                    
                    if (in_array($method, array(self::$prefix_method . '_active'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($product_unit_conversion_db) > 0) {
                            $success = 0;
                            $msg[] = 'Product Unit Conversion'.
                                ' '.Lang::get('invalid');
                        }
                                                
                        //</editor-fold>
                    }
                }

                //</editor-fold>
                break;
            case self::$prefix_method . '_delete':
                if(count($product_unit_conversion_db)>0 === FALSE){
                    $success = 0;
                    $msg[] = Lang::get('Product Unit Conversion')
                        .' '.Lang::get('invalid',true,false)
                    ;
                }
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

        $product_unit_conversion_data = isset($data['product_unit_conversion']) ? $data['product_unit_conversion'] : array();

        $temp_product_unit_conversion = Product_Unit_Conversion_Data_Support::product_unit_conversion_get($product_unit_conversion_data['id']);
        $product_unit_conversion_db = isset($temp_product_unit_conversion['product_unit_conversion'])?$temp_product_unit_conversion['product_unit_conversion']:array();
        
        $product_unit_conversion_id = $product_unit_conversion_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
                //<editor-fold defaultstate="collapsed">
                $product_unit_conversion = array(
                    'product_id'=>Tools::_str($product_unit_conversion_data['product_id']),
                    'qty' => Tools::_float($product_unit_conversion_data['qty']),
                    'unit_id' => Tools::_str($product_unit_conversion_data['unit_id']),
                    'qty2' => Tools::_float($product_unit_conversion_data['qty2']),
                    'unit_id2' => Tools::_str($product_unit_conversion_data['unit_id2']),
                );
                
                $result['product_unit_conversion'] = $product_unit_conversion;

                //</editor-fold>
                break;
            case self::$prefix_method.'_delete':
                //<editor-fold defaultstate="collapsed">
                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function product_unit_conversion_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Unit_Conversion_Engine::path_get();
        get_instance()->load->helper($path->product_unit_conversion_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_unit_conversion = $final_data['product_unit_conversion'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        if (!$db->insert('p_u_conversion', $fproduct_unit_conversion)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function product_unit_conversion_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Unit_Conversion_Engine::path_get();
        get_instance()->load->helper($path->product_unit_conversion_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_unit_conversion = $final_data['product_unit_conversion'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_unit_conversion_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('p_u_conversion', $fproduct_unit_conversion, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function product_unit_conversion_delete($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Unit_Conversion_Engine::path_get();
        get_instance()->load->helper($path->product_unit_conversion_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_unit_conversion_id = $id;

        $result['trans_id'] = $id;

        if (!$db->query('delete from p_u_conversion where id = '.$db->escape($id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}

?>
