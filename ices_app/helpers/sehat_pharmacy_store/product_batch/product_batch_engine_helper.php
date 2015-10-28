<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Product_Batch_Engine {

    public static $prefix_id = 'product_batch';
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
                , 'method' => 'product_batch_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Product Batch'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'active'
                , 'text' => 'ACTIVE'
                , 'method' => 'product_batch_active'
                , 'next_allowed_status' => array()
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product Batch'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'inactive'
                , 'text' => 'INACTIVE'
                , 'method' => 'product_batch_inactive'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Product Batch'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'product_batch/'
            , 'product_batch_engine' => ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_engine'
            , 'product_batch_data_support' => ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_data_support'
            , 'product_batch_renderer' => ICES_Engine::$app['app_base_dir'] . 'product_batch/product_batch_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'product_batch/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'product_batch/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_data_support'));
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $product_batch = isset($data['product_batch']) ? Tools::_arr($data['product_batch']) : array();
        $product_batch_id = $product_batch['id'];
        $temp = Product_Batch_Data_Support::product_batch_get($product_batch_id);
        $product_batch_db = isset($temp['product_batch'])?$temp['product_batch']:array();
        
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':            
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                $success = 0;
                $msg[] = Lang::get('Add or Update'). ' '.Lang::get('Product Batch')
                    .' '.Lang::get('invalid');
                
                //</editor-fold>
                break;
            case self::$prefix_method . '_active':
                //<editor-fold defaultstate="collapsed">
                
                //<editor-fold defaultstate="collapsed" desc="Major Validation">
                if (!count($product_batch_db) > 0) {
                    $success = 0;
                    $msg[] = 'Product Batch'
                        .' '.Lang::get('invalid',true,false);
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_update(
                        array(
                            'module' => 'product_batch',
                            'module_name' => Lang::get('Product Batch'),
                            'module_engine' => 'product_batch_engine',
                        ), 
                        $product_batch
                    );
                    $success = $temp_result['success'];
                    $msg = array_merge($msg,$temp_result['msg']);
                }
                //</editor-fold
                
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
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        $db = new DB();
        $result = array();

        $product_batch_data = isset($data['product_batch']) ? $data['product_batch'] : array();
        
        $temp_product_batch = Product_Batch_Data_Support::product_batch_get($product_batch_data['id']);
        $product_batch_db = isset($temp_product_batch['product_batch'])?$temp_product_batch['product_batch']:array();
        
        $product_batch_id = $product_batch_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                
                //</editor-fold>
                break;
            case self::$prefix_method . '_active':
                //<editor-fold defaultstate="collapsed">
                $product_batch = array(
                    'notes' => Tools::empty_to_null(Tools::_str($product_batch_data['notes'])),
                    'product_batch_status'=>'active',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['product_batch'] = $product_batch;
                //</editor-fold>
                break;
            case self::$prefix_method . '_inactive':
                //<editor-fold defaultstate="collapsed">
                
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public static function product_batch_add($db, $final_data){
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        get_instance()->load->helper($path->product_batch_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
                
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');        
        
        $fproduct_batch = $final_data['product_batch'];
        $fproduct_batch_qty_log = $final_data['product_batch_qty_log'];
        $product_batch_id = '';
                
        $new_seq = '';
        $q = '
            select count(1)+1 new_sequence
            from product_batch pb
            where date_format(pb.expired_date,"%Y-%m-%d") 
                = '.$db->escape(Tools::_date($fproduct_batch['expired_date'],'Y-m-d')).'
        ';
        $rs = $db->query_array($q);
        if(!count($rs)>0){
            $success = 0;
            $msg[] = 'Retreive'
                .' '.Lang::get('Product Batch').' '.Lang::get('Sequence')
                .' '.Lang::get('invalid');
            $db->trans_rollback();
        }
        else{
            $new_seq = $rs[0]['new_sequence'];
            for($i = strlen($new_seq);$i<3;$i++){
               $new_seq = '0'.$new_seq;
            }
        }
        
        if($success === 1){
            $fproduct_batch ['batch_number']= Tools::_date($fproduct_batch['expired_date'],'Ymd'.$new_seq);
            
            if (!$db->insert('product_batch', $fproduct_batch)) {
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }
        
        if($success === 1){
            $product_batch_id = $db->fast_get('product_batch',
                array('batch_number' => $fproduct_batch['batch_number'],
                    'status' => 1,
                )
            )[0]['id'];
            $result['trans_id'] = $product_batch_id;
        }
        
        if($success === 1){
            $fproduct_batch_qty_log['product_batch_id'] = $product_batch_id;            
            
            if(!$db->insert('product_batch_qty_log',$fproduct_batch_qty_log)){
                $success = 0;
                $msg[] = $db->_error_message();
                $db->trans_rollback();
            }
        }
        
        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product_batch', $product_batch_id, $fproduct_batch['product_batch_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    public function product_batch_active($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        get_instance()->load->helper($path->product_batch_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fproduct_batch = $final_data['product_batch'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $product_batch_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('product_batch', $fproduct_batch, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'product_batch', $product_batch_id, $fproduct_batch['product_batch_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    public static function product_batch_inactive($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        get_instance()->load->helper($path->product_batch_data_support);
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
                
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');        
        
        $product_batch_id = $id;
        $product_batch_db = Product_Batch_Data_Support::product_batch_get($product_batch_id)['product_batch'];     
        
        $fproduct_batch = $final_data['product_batch'];
        
        $param = array('product_batch'=>$fproduct_batch);
        $temp_result = self::product_batch_active($db, $param, $id);
        if($temp_result['success']!== 1){
            $success = 0;
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    public static function product_batch_qty_add($db, $final_data,$id){
        //<editor-fold defaultstate="collapsed">
        $path = Product_Batch_Engine::path_get();
        get_instance()->load->helper($path->product_batch_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
                
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');        
        
        $fproduct_batch = $final_data['product_batch'];
        $fproduct_batch_qty_log = $final_data['product_batch_qty_log'];
        $product_batch_id = $id;
        
        $old_product_batch_db = array();
        
        //<editor-fold defaultstate="collapsed" desc="Retrieve Product Batch">
        $q = '
            select pb.*
            from product_batch pb
            where pb.id = ' . $db->escape($product_batch_id) . '
        ';
        $rs = $db->query_array($q);
        
        if (!count($rs) > 0) {
            $success = 0;
            $msg[] = Lang::get('Retrieve')
                .' '.Lang::get('Product Batch')
                .' '.Lang::get('invalid');
        }
        else{
            $old_product_batch_db = $rs[0];
        }
        //</editor-fold>
                
        $new_seq = '';
        $q = '
            update product_batch
            set qty = qty+'.$db->escape($fproduct_batch['qty']).'
                ,modid = '.$db->escape($modid).'
                ,moddate = '.$db->escape($moddate).'
            where product_batch.id = '.$db->escape($product_batch_id).'
        ';
        
        if(!$db->query($q)){
            $success = 0;
            $msg[] = $db->_error_message();
            $db->trans_rollback();
        }
        
        if($success === 1){
            $param = array(
                'ref_type'=>$fproduct_batch_qty_log['ref_type'],
                'ref_id'=>$fproduct_batch_qty_log['ref_id'],
                'product_batch_id'=>$product_batch_id,
                'old_qty'=>$old_product_batch_db['qty'],
                'qty'=>$fproduct_batch['qty'],
                'new_qty'=>Tools::_float($old_product_batch_db['qty'])+Tools::_float($fproduct_batch['qty']),
                'description'=>$fproduct_batch_qty_log['description'],
                'modid'=>$modid,
                'moddate'=>$moddate,
            );
            
            if(!$db->insert('product_batch_qty_log',$param)){
                $success = 0;
                $msg[] = $db->_error_message();
                $db->trans_rollback();
            }
        }
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
}

?>
