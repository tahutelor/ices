<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Engine {

    public static $prefix_id = 'product';
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
                , 'method' => 'product_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Product'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'product_active'
                , 'next_allowed_status' => array('inactive')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'product_inactive'
                , 'next_allowed_status' => array('active')
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'product/'
            , 'product_engine' => ICES_Engine::$app['app_base_dir'] . 'product/product_engine'
            , 'product_data_support' => ICES_Engine::$app['app_base_dir'] . 'product/product_data_support'
            , 'product_renderer' => ICES_Engine::$app['app_base_dir'] . 'product/product_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'product/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'product/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        get_instance()->load->helper($path->product_data_support);
        
        SI::module()->load_class(array('module'=>'unit','class_name'=>'unit_data_support'));
        SI::module()->load_class(array('module'=>'product_unit_conversion','class_name'=>'product_unit_conversion_data_support'));
        SI::module()->load_class(array('module'=>'product_category','class_name'=>'product_category_data_support'));
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $product = isset($data['product']) ? Tools::_arr($data['product']) : array();
        $product_id = $product['id'];
        $temp = Product_Data_Support::product_get($product_id);
        $product_db = isset($temp['product'])?$temp['product']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                if (!(
                    isset($product['name']) 
                    && isset($product['notes'])
                    && isset($product['barcode'])
                    && isset($product['sales_formula'])
                    && isset($product['unit_id'])
                    && isset($product['unit_sales_id'])
                    && isset($product['product_status'])
                    && isset($product['product_category_id'])
                    && isset($product['purchase_amount'])
                )){
                    $success = 0;
                    $msg[] = Lang::get('Product') 
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $product_name = Tools::empty_to_null(Tools::_str($product['name']));
                    $product_category_id = Tools::empty_to_null(Tools::_str($product['product_category_id']));
                    $sales_formula = Tools::empty_to_null(Tools::_str($product['sales_formula']));
                    $unit_id = Tools::empty_to_null(Tools::_str($product['unit_id']));
                    $unit_sales_id = Tools::empty_to_null(Tools::_str($product['unit_sales_id']));
                    $purchase_amount = Tools::_float($product['purchase_amount']);
                    
                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($product_name) 
                        || is_null($product_category_id) 
                        || is_null($unit_id)
                        || is_null($unit_sales_id)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Name')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Unit')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Product Category')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>

                    if(Tools::_float($purchase_amount)<Tools::_float('0')){
                        $success = 0;
                        $msg[] = Lang::get('Purchase Amount')
                            .' '.Lang::get('invalid');
                    }
                    
                    $q = '
                        select 1
                        from product p
                        where pstatus > 0
                            and (
                            pname =    ' . $db->escape($product_name) . '
                            )
                            and pid <> ' . $db->escape($product_id) . '
                    ';

                    if (count($db->query_array($q)) > 0) {
                        $success = 0;
                        $msg[] = Lang::get('Name')
                            . ' ' . Lang::get('exists', true, false)
                        ;
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
                    
                    //<editor-fold defaultstate="collapsed" desc="Product Category">
                    $temp = Product_Category_Data_Support::product_category_get($product_category_id);
                    $t_success = 1;
                    if(!count($temp)>0){
                        $t_success = 0;                        
                    }
                    else{
                        if($temp['product_category']['product_category_status']!== 'active') $t_success = 0;
                    }
                    if($t_success !== 1){
                        $success = 0;
                            $msg[] = Lang::get('Product_Category')
                                .' '.Lang::get('invalid')
                            ;
                    }
                    //</editor-fold>
                    
                    
                    if(!is_null($sales_formula)){
                        //<editor-fold defaultstate="collapsed">

                        $t_val = Tools::script_math_get(array(
                            'script'=>$sales_formula,
                            'type'=>'value'                                
                        ));
                        $lsuccess = 1;
                        if(is_null($t_val)){
                            $lsuccess = 0;
                            
                        }
                        else if(Tools::_float($t_val)< Tools::_float('0')){
                            $lsuccess = 0;                            
                        }
                        if($lsuccess !== 1){
                            $success = 0;
                            $msg[] = Lang::get('Sales Formula')
                                .' '.Lang::get('invalid',true,false)
                            ;
                        }
                        //</editor-fold>
                    }
                    
                    if($unit_id !== $unit_sales_id){
                        //<editor-fold defaultstate="collapsed">
                        $puc_exists = FALSE;
                        $t_puc_list = Product_Unit_Conversion_Data_Support::product_unit_conversion_get_by_product_id($product_id);
                        if(count($t_puc_list)>0){
                            foreach($t_puc_list['product_unit_conversion'] as $idx=>$row){
                                if($row['unit_id'] === $unit_sales_id) $puc_exists = TRUE;
                            }
                        }
                        if($puc_exists === FALSE){
                            $success = 0;
                            $msg[] = Lang::get('Sales Unit')
                                .' '.Lang::get('invalid',true,false)
                            ;
                        }
                        //</editor-fold>
                    }   
                    
                    else if (in_array($method, array(self::$prefix_method . '_active', self::$prefix_method . '_inactive'))) {
                        //<editor-fold defaultstate="collapsed">
                        if (!count($product_db) > 0) {
                            $success = 0;
                            $msg[] = 'Product'.
                                ' '.Lang::get('invalid');
                        }

                        if ($success === 1) {
                            $temp_result = SI::data_validator()->validate_on_update(
                                            array(
                                        'module' => 'product',
                                        'module_name' => Lang::get('Product'),
                                        'module_engine' => 'product_engine',
                                            ), $product
                            );
                            $success = $temp_result['success'];
                            $msg = array_merge($msg,$temp_result['msg']);
                        }
                        
                        if($method === self::$prefix_method.'_inactive'){
                            //<editor-fold defaultstate="collapsed">
                           
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

        $product_data = isset($data['product']) ? $data['product'] : array();

        $temp_product = Product_Data_Support::product_get($product_data['id']);
        $product_db = isset($temp_product['product'])?$temp_product['product']:array();
        
        $product_id = $product_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
            case self::$prefix_method . '_active':
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $product = array(
                    'product_category_id'=>Tools::_str($product_data['product_category_id']),
                    'name' => Tools::_str($product_data['name']),
                    'barcode' => Tools::empty_to_null(Tools::_str($product_data['barcode'])),
                    'notes' => Tools::empty_to_null(Tools::_str(isset($product_data['notes'])?$product_data['notes']:'')),
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
               
                switch ($method) {
                    case self::$prefix_method . '_add':
                        $product['product_status'] = SI::type_default_type_get('Product_Engine', '$status_list')['val'];
                        break;
                    case self::$prefix_method . '_active':
                        $product['product_status'] = 'active';
                        break;
                    case self::$prefix_method . '_inactive':
                        $product['product_status'] = 'inactive';
                        break;
                }

                $p_u = array(
                    'unit_id'=>Tools::_str($product_data['unit_id']),
                    'purchase_amount'=>Tools::_str($product_data['purchase_amount']),
                    'sales_formula' => Tools::script_math_get(array(
                            'script'=>is_null(Tools::empty_to_null($product_data['sales_formula']))?'c':Tools::_str($product_data['sales_formula']),
                            'type'=>'script'
                    )),
                );
                
                $p_u_sales = array(
                    'unit_id'=>Tools::_str($product_data['unit_sales_id']),
                );
                
                $result['p_u'] = $p_u;
                $result['p_u_sales'] = $p_u_sales;
                $result['product'] = $product;

                //</editor-fold>
                break;
        }





        return $result;
        //</editor-fold>
    }
    
    public function product_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        get_instance()->load->helper($path->product_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct = $final_data['product'];
        $fp_u = $final_data['p_u'];
        $fp_u_sales = $final_data['p_u_sales'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');

        $fproduct['code'] = SI::code_counter_get($db, 'product');

        if (!$db->insert('product', $fproduct)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $product_id = $db->last_insert_id();
            $result['trans_id'] = $product_id;
            if(is_null($product_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product', $product_id, $fproduct['product_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        if($success === 1){
            $fp_u['product_id'] = $product_id;
            if (!$db->insert('p_u', $fp_u)) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        
        if($success === 1){
            $fp_u_sales['product_id'] = $product_id;
            if (!$db->insert('p_u_sales', $fp_u_sales)) {
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

    public function product_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        get_instance()->load->helper($path->product_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct = $final_data['product'];
        $fp_u = $final_data['p_u'];
        $fp_u_sales = $final_data['p_u_sales'];


        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('product', $fproduct, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product', $product_id, $fproduct['product_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        if($success === 1){
            if (!$db->query('delete from p_u where product_id = ' . $db->escape($product_id))) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        
        if($success === 1){
            $fp_u['product_id'] = $product_id;
            if (!$db->insert('p_u', $fp_u)) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        
        if($success === 1){
            if (!$db->query('delete from p_u_sales where product_id = ' . $db->escape($product_id))) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        
        if($success === 1){
            $fp_u_sales['product_id'] = $product_id;
            if (!$db->insert('p_u_sales', $fp_u_sales)) {
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

    public function product_inactive($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = self::product_active($db, $final_data, $id);
        return $result;
        //</editor-fold>
    }
    
    public function purchase_amount_recalculate($db, $final_data){
        //<editor-fold defaultstate="collapsed">
        $path = Product_Engine::path_get();
        get_instance()->load->helper($path->product_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fp_u = $final_data['p_u'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        
        $product_id = $fp_u['product_id'];
        $unit_id = $fp_u['unit_id'];
        
        $q = '
            select coalesce(max(amount),0) amount
            from pi_product pip
            inner join purchase_invoice pi 
                on pip.purchase_invoice_id = pi.id and pi.purchase_invoice_status = "invoiced"
            where pip.product_id = '.$db->escape($product_id).'
                and pip.unit_id = '.$db->escape($unit_id).'            
        ';
        
        $rs = $db->query_array($q);        
        
        $purchase_amount = $rs[0]['amount'];
        
        
        $q = '
            update p_u
            set p_u.purchase_amount = '.$db->escape($purchase_amount).'
            where p_u.product_id = '.$db->escape($product_id).'
                and p_u.unit_id = '.$db->escape($unit_id).'
                and p_u.purchase_amount <= '.$db->escape($purchase_amount).'
        ';
        if (!$db->query($q)) {
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
