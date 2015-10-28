<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Stock_Opname_Engine {

    public static $prefix_id = 'product_stock_opname';
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
                , 'method' => 'product_stock_opname_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Product Stock Opname'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'done'
                , 'text' => 'DONE'
                , 'method' => 'product_stock_opname_done'
                , 'next_allowed_status' => array()
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product Stock Opname'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'X'
                , 'text' => 'CANCELED'
                , 'method' => 'product_stock_opname_canceled'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Cancel')
                        , array('val' => Lang::get(array('Product Stock Opname'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'product_stock_opname/'
            , 'product_stock_opname_engine' => ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_engine'
            , 'product_stock_opname_data_support' => ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_data_support'
            , 'product_stock_opname_renderer' => ICES_Engine::$app['app_base_dir'] . 'product_stock_opname/product_stock_opname_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'product_stock_opname/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'product_stock_opname/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        get_instance()->load->helper($path->product_stock_opname_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $product_stock_opname = isset($data['product_stock_opname']) ? Tools::_arr($data['product_stock_opname']) : array();
        $pso_product = isset($data['pso_product']) ? Tools::_arr($data['pso_product']) : array();
        $product_stock_opname_id = $product_stock_opname['id'];
        $temp = Product_Stock_Opname_Data_Support::product_stock_opname_get($product_stock_opname_id);
        $product_stock_opname_db = isset($temp['product_stock_opname'])?$temp['product_stock_opname']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
                SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
                SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_data_support'));
                
                if (!(isset($product_stock_opname['store_id']) 
                        && isset($product_stock_opname['warehouse_id']) 
                        && isset($product_stock_opname['product_stock_opname_status'])
                        && isset($product_stock_opname['notes'])
                        && isset($product_stock_opname['checker'])
                        && isset($data['product_stock_opname_product'])
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Product Stock Opname')
                            . ' ' . Lang::get('or',true, false). ' ' . Lang::get('Product Stock Opname Product')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_id = Tools::empty_to_null(Tools::_str($product_stock_opname['store_id']));
                    $warehouse_id = Tools::empty_to_null(Tools::_str($product_stock_opname['warehouse_id']));
                    $checker = Tools::empty_to_null(Tools::_str($product_stock_opname['checker']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($store_id) 
                        || is_null($warehouse_id)
                        || is_null($checker)
                        || (!count($pso_product)>0)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Store')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Warehouse')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Checker')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Product Stock Opname Product')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>
                    
                    $temp_result = Store_Data_Support::store_validate($store_id);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    $temp_result = Warehouse_Data_Support::warehouse_validate($warehouse_id);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    
                    //<editor-fold defaultstate="collapsed" desc="Product Batch Validation">
                    $local_success = 1;
                    $local_msg = array();
                    $temp_result = Product_Batch_Data_Support::product_batch_list_validate($pso_product);
                    if($temp_result['success']!== 1){
                        $local_success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    if($local_success === 1){
                        foreach($pso_product as $idx=>$row){
                            foreach($pso_product as $idx2=>$row2){
                                if($row['product_batch_id'] === $row2['product_batch_id']
                                    && $idx !== $idx2
                                ){
                                    $local_success = 0;
                                    $msg[] = Lang::get('Product Batch')
                                        .' '.Lang::get('duplicate',true,false);
                                    break;
                                }
                            }
                            
                            if($local_success!== 1) break;
                        }
                    }
                    
                    if($local_success !== 1) $success = $local_success;
                    //</editor-fold>

                    
                    
                }
                
                //</editor-fold>
                break;
            case self::$prefix_method . '_done':
                //<editor-fold defaultstate="collapsed">
                
                //<editor-fold defaultstate="collapsed" desc="Major Validation">
                if (!count($product_stock_opname_db) > 0) {
                    $success = 0;
                    $msg[] = 'Product Stock Opname'
                        .' '.Lang::get('invalid',true,false);
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_update(
                        array(
                            'module' => 'product_stock_opname',
                            'module_name' => Lang::get('Product Stock Opname'),
                            'module_engine' => 'product_stock_opname_engine',
                        ), 
                        $product_stock_opname
                    );
                    $success = $temp_result['success'];
                    $msg = array_merge($msg,$temp_result['msg']);
                }
                //</editor-fold>
                
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                
                //<editor-fold defaultstate="collapsed" desc="Major Validation">
                if (!count($product_stock_opname_db) > 0) {
                    $success = 0;
                    $msg[] = 'Invalid Product Stock Opname';
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_cancel(
                                    array(
                                'module' => 'product_stock_opname',
                                'module_name' => Lang::get('Product Stock Opname'),
                                'module_engine' => 'product_stock_opname_engine',
                                    ), $product_stock_opname
                    );
                    $success = $temp_result['success'];
                    $msg = array_merge($msg,$temp_result['msg']);
                }
                
                if($success !== 1) break;
                //</editor-fold>
                
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
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_data_support'));
        $db = new DB();
        $result = array();

        $product_stock_opname_data = isset($data['product_stock_opname']) ? $data['product_stock_opname'] : array();
        $pso_product_data = isset($data['pso_product']) ? $data['pso_product'] : array();

        $temp_product_stock_opname = Product_Stock_Opname_Data_Support::product_stock_opname_get($product_stock_opname_data['id']);
        $product_stock_opname_db = isset($temp_product_stock_opname['product_stock_opname'])?$temp_product_stock_opname['product_stock_opname']:array();
        
        $product_stock_opname_id = $product_stock_opname_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                $product_stock_opname = array(
                    'store_id' => Tools::_str($product_stock_opname_data['store_id']),
                    'warehouse_id' => Tools::_str($product_stock_opname_data['warehouse_id']),
                    'product_stock_opname_date'=>Tools::_date(),
                    'checker'=>Tools::_str($product_stock_opname_data['checker']),
                    'notes' => Tools::empty_to_null(Tools::_str($product_stock_opname_data['notes'])),
                    'product_stock_opname_status'=>'done',
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $pso_product = array();
                
                $param_product_batch = array();
                foreach($pso_product_data as $idx=>$row){
                    $param_product_batch[] = $row['product_batch_id'];
                }                
                $param = array(
                    'module'=>'stock_good',
                    'warehouse'=>array($product_stock_opname_data['warehouse_id']),
                    'product_batch'=>$param_product_batch,
                );
                $t_product_stock = Product_Stock_Data_Support::product_stock_mass_get($param);
                
                foreach($pso_product_data as $idx=>$row){
                    $old_qty = Tools::_float('0');
                    foreach($t_product_stock as $idx2=>$row2){
                        if($row['product_batch_id'] === $row2['product_batch_id']){
                            $old_qty = Tools::_float($row2['qty']);
                        }
                    }
                    $new_qty = $old_qty + Tools::_float($row['qty']);
                    
                    $pso_product[] = array(
                        'module'=>'stock_good',
                        'ref_type'=>'product_batch',
                        'ref_id'=>Tools::_str($row['product_batch_id']),
                        'qty'=>Tools::_str($row['qty']),
                        'old_qty'=>$old_qty,
                        'new_qty'=>$new_qty,
                    );
                }
                
                
                                
                $result['product_stock_opname'] = $product_stock_opname;
                $result['pso_product'] = $pso_product;
                //</editor-fold>
                break;
            case self::$prefix_method . '_done':
                //<editor-fold defaultstate="collapsed">
                $product_stock_opname = array(
                    'notes' => Tools::empty_to_null(Tools::_str($product_stock_opname_data['notes'])),
                    'product_stock_opname_status'=>'done',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['product_stock_opname'] = $product_stock_opname;
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                $product_stock_opname = array(
                    'product_stock_opname_status'=>'X',
                    'cancellation_reason'=>Tools::_str($product_stock_opname_data['cancellation_reason']),
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $result['product_stock_opname'] = $product_stock_opname;
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public function product_stock_opname_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        get_instance()->load->helper($path->product_stock_opname_data_support);
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_stock_opname = $final_data['product_stock_opname'];
        $fpso_product = $final_data['pso_product'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $warehouse_id = $fproduct_stock_opname['warehouse_id'];

        $fproduct_stock_opname['code'] = SI::code_counter_store_get($db, 
            $fproduct_stock_opname['store_id'],
            'product_stock_opname'
        );
        
        if (!$db->insert('product_stock_opname', $fproduct_stock_opname)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            $product_stock_opname_id = $db->last_insert_id();
            $result['trans_id'] = $product_stock_opname_id;
            if(is_null($product_stock_opname_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
            
        }

        if ($success === 1) {
            $temp_result = SI::status_log_add($db, 'product_stock_opname', $product_stock_opname_id, $fproduct_stock_opname['product_stock_opname_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        //<editor-fold defaultstate="collapsed" desc="Insert Product Stock Opname Product">
        if($success === 1){
            foreach($fpso_product as $idx=>$row){
                $row['product_stock_opname_id'] = $product_stock_opname_id;
                $pso_product_id = '';
                
                if (!$db->insert('pso_product', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                }
                
                //<editor-fold defaultstate="collapsed" desc="Retreive Product Stock Opname Product">
                if($success === 1){
                    $pso_product_id = $db->last_insert_id();
                    if(is_null($pso_product_id)){
                        $success = 0;
                        $msg[] = 'Retrieve'
                            .' '.Lang::get('Product Stock Opname Product')
                            .' '.Lang::get('failed',true,false)
                        ;
                        $db->trans_rollback();
                    }
                    
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Add Product Stock">
                if($success === 1 && $row['ref_type']==='product_batch'){                    

                    $temp_result = Product_Stock_Engine::stock_good_add(
                        $db,
                        'pso_product',
                        $pso_product_id,
                        $warehouse_id,
                        $row['ref_id'],
                        $row['qty'],
                        'Product Stock Opname: <a target="_blank" href="{base_url}/product_stock_opname/view/'.$product_stock_opname_id.'">'.$fproduct_stock_opname['code'].'</a>'
                            .' '.SI::type_get('product_stock_opname_engine', 
                            $fproduct_stock_opname['product_stock_opname_status'],'$status_list'
                        )['text'],
                        $moddate

                    );
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>

                if($success !== 1) break;
            }
        }
        //</editor-fold>
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function product_stock_opname_done($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        get_instance()->load->helper($path->product_stock_opname_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_stock_opname = $final_data['product_stock_opname'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_stock_opname_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('product_stock_opname', $fproduct_stock_opname, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product_stock_opname', $product_stock_opname_id, $fproduct_stock_opname['product_stock_opname_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function product_stock_opname_canceled($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Stock_Opname_Engine::path_get();
        get_instance()->load->helper($path->product_stock_opname_data_support);
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_stock_opname = $final_data['product_stock_opname'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_stock_opname_id = $id;

        $result['trans_id'] = $id;
        
        $temp = Product_Stock_Opname_Data_Support::product_stock_opname_get($id);
        $product_stock_opname_db = $temp['product_stock_opname'];
        $pi_product_db = $temp['pi_product'];
        
        $temp_result = self::product_stock_opname_done($db, $final_data, $id);
        if($temp_result['success'] !== 1){
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

}

?>
