<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Purchase_Return_Engine {

    public static $prefix_id = 'purchase_return';
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
                , 'method' => 'purchase_return_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Purchase Return'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'returned'
                , 'text' => 'RETURNED'
                , 'method' => 'purchase_return_returned'
                , 'next_allowed_status' => array('X')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Purchase Return'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'X'
                , 'text' => 'CANCELED'
                , 'method' => 'purchase_return_canceled'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Cancel')
                        , array('val' => Lang::get(array('Purchase Return'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'purchase_return/'
            , 'purchase_return_engine' => ICES_Engine::$app['app_base_dir'] . 'purchase_return/purchase_return_engine'
            , 'purchase_return_data_support' => ICES_Engine::$app['app_base_dir'] . 'purchase_return/purchase_return_data_support'
            , 'purchase_return_renderer' => ICES_Engine::$app['app_base_dir'] . 'purchase_return/purchase_return_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'purchase_return/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'purchase_return/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Return_Engine::path_get();
        get_instance()->load->helper($path->purchase_return_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $purchase_return = isset($data['purchase_return']) ? Tools::_arr($data['purchase_return']) : array();
        $pr_product = isset($data['pr_product']) ? Tools::_arr($data['pr_product']) : array();
        $purchase_return_id = $purchase_return['id'];
        $temp = Purchase_Return_Data_Support::purchase_return_get($purchase_return_id);
        $purchase_return_db = isset($temp['purchase_return'])?$temp['purchase_return']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
                SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
                
                if (!(isset($purchase_return['store_id']) 
                        && isset($purchase_return['ref_type']) 
                        && isset($purchase_return['ref_id']) 
                        && isset($purchase_return['purchase_return_status'])
                        && isset($purchase_return['total_discount_amount'])
                        && isset($purchase_return['notes'])
                        && isset($data['pr_product'])
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Purchase Return')
                            . ' ' . Lang::get('or',true, false). ' ' . Lang::get('Purchase Return Product')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_id = Tools::empty_to_null(Tools::_str($purchase_return['store_id']));
                    $ref_type = Tools::empty_to_null(Tools::_str($purchase_return['ref_type']));
                    $ref_id = Tools::empty_to_null(Tools::_str($purchase_return['ref_id']));
                    $total_discount_amount= Tools::_float($purchase_return['total_discount_amount']);

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($store_id) 
                        || is_null($ref_type)
                        || is_null($ref_id)
                        || (!count($pr_product)>0)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Store')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Reference')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Purchase Return Product')
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
                    
                    $total_amount = Tools::_float('0');
                    $available_outstanding_amount = Tools::_float('0');
                    $grand_total_amount = Tools::_float('0');
                    $ref_product = array();
                    switch($ref_type){
                        case 'purchase_invoice':
                            //<editor-fold defaultstate="collapsed">
                            $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($ref_id);
                            $purchase_invoice = $temp['purchase_invoice'];
                            $available_outstanding_amount = $purchase_invoice['outstanding_grand_total_amount'];
                           
                            //</editor-fold>
                            break;
                    }
                    
                    $temp = Purchase_Return_Data_Support::reference_dependency_get(
                        array('ref_type'=>$ref_type, 'ref_id'=>$ref_id)
                    );
                    $ref_product = $temp['ref_product'];
                    
                    //<editor-fold defaultstate="collapsed" desc="Product Validation">
                    foreach($ref_product as $idx=>$row){
                        $ref_product[$idx]['exists'] = false;
                    }
                    $local_success = 1;
                    foreach($pr_product as $idx=>$row){
                        $ref_exists = false;
                        $p_ref_type = Tools::_str(isset($row['ref_type'])?$row['ref_type']:'');
                        $p_ref_id = Tools::_str(isset($row['ref_id'])?$row['ref_id']:'');
                        $p_qty = Tools::_float(isset($row['qty'])?$row['qty']:'');
                        $p_amount = Tools::_float('0');
                        foreach($ref_product as $idx2=>$row2){
                            if($p_ref_type === $row2['ref_type']
                                && $p_ref_id === $row2['ref_id']
                                && $row2['exists'] === false
                                && Tools::_float($p_qty)<=Tools::_float($row2['available_qty'])
                            ){
                                $ref_product[$idx2]['exists'] = true;
                                $ref_exists = true;
                                $p_amount = Tools::_float($row2['amount']);
                            }
                        }
                        $total_amount += Tools::_float($p_amount) * Tools::_float($p_qty);
                        
                        if(!$ref_exists){
                            $local_success = 0;
                        }
                        
                        if($local_success !== 1){
                            $success = 0;
                            $msg[] = Lang::get('Purchase Return Product')
                                .' '.Lang::get('invalid');
                            break;
                        }
                    }
                    //</editor-fold>
                    $grand_total_amount = Tools::_float($total_amount) - Tools::_float($total_discount_amount);
                    if(Tools::_float($grand_total_amount) > Tools::_float($available_outstanding_amount)){
                        $success = 0;
                        $msg[] = Lang::get('Grand Total Amount')
                            .' '.Lang::get('limit',true,false). ' '
                            .' '.Lang::get(Tools::thousand_separator($available_outstanding_amount),true,false). ' '
                        ;                                
                    }
                    
                    
                }
                
                //</editor-fold>
                break;
            case self::$prefix_method . '_returned':
                //<editor-fold defaultstate="collapsed">
                
                //<editor-fold defaultstate="collapsed" desc="Major Validation">
                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_update(
                        array(
                            'module' => 'purchase_return',
                            'module_name' => Lang::get('Purchase Return'),
                            'module_engine' => 'purchase_return_engine',
                        ), 
                        $purchase_return
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
                if (!count($purchase_return_db) > 0) {
                    $success = 0;
                    $msg[] = 'Invalid Purchase Return';
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_cancel(
                                    array(
                                'module' => 'purchase_return',
                                'module_name' => Lang::get('Purchase Return'),
                                'module_engine' => 'purchase_return_engine',
                                    ), $purchase_return
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
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        $db = new DB();
        $result = array();

        $purchase_return_data = isset($data['purchase_return']) ? $data['purchase_return'] : array();
        $pr_product_data = isset($data['pr_product']) ? $data['pr_product'] : array();
        
        $temp_purchase_return = Purchase_Return_Data_Support::purchase_return_get($purchase_return_data['id']);
        $purchase_return_db = isset($temp_purchase_return['purchase_return'])?$temp_purchase_return['purchase_return']:array();
        
        $purchase_return_id = $purchase_return_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($purchase_return_data['ref_id']);
                $purchase_invoice = isset($temp['purchase_invoice'])?$temp['purchase_invoice']:array();
                $pi_product = isset($temp['pi_product'])?$temp['pi_product']:array();

                $purchase_return = array(
                    'store_id' => Tools::_str($purchase_return_data['store_id']),
                    'ref_type' => Tools::_str($purchase_return_data['ref_type']),
                    'ref_id' => Tools::_str($purchase_return_data['ref_id']),
                    'purchase_return_date'=>Tools::_date(),
                    'notes' => Tools::empty_to_null(Tools::_str($purchase_return_data['notes'])),
                    'purchase_return_status'=>'returned',
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                
                $total_amount = Tools::_float('0');
                $total_discount_amount = Tools::_float(Tools::_str($purchase_return_data['total_discount_amount']));
                $additional_cost_amount = Tools::_float(Tools::_str($purchase_return_data['additional_cost_amount']));
                $grand_total_amount = Tools::_float('0');
                $pr_product = array();
                
                foreach($pr_product_data as $idx=>$row){
                    $amount = Tools::_float('0');
                    $p_ref_type = $row['ref_type'];
                    
                    switch($row['ref_type']){
                        case 'pi_product':
                            //<editor-fold defaultstate="collapsed">
                            foreach($pi_product as $idx2=>$row2){
                                if($row['ref_id'] === $row2['id']){
                                    $amount = $row2['amount'];
                                    break;
                                }                            
                            }
                        //</editor-fold>
                        break;
                    }
                    
                    $subtotal_amount = Tools::_float($row['qty']) * Tools::_float($amount);
                    $total_amount+= $subtotal_amount;
                    $pr_product[] = array(
                        'ref_type'=>Tools::_str($row['ref_type']),
                        'ref_id'=>Tools::_str($row['ref_id']),
                        'qty'=>Tools::_str($row['qty']),
                        'outstanding_movement_qty'=>'0',
                        'amount'=>Tools::_str($amount),
                        'subtotal_amount'=>$subtotal_amount,
                    );
                }
                
                $grand_total_amount = $total_amount - $total_discount_amount + $additional_cost_amount;
                $purchase_return['total_amount'] = $total_amount;
                $purchase_return['total_discount_amount'] = $total_discount_amount;
                $purchase_return['additional_cost_amount'] = $additional_cost_amount;
                $purchase_return['grand_total_amount'] = $grand_total_amount;
                
                $result['purchase_return'] = $purchase_return;
                $result['pr_product'] = $pr_product;
                //</editor-fold>
                break;
            case self::$prefix_method . '_returned':
                //<editor-fold defaultstate="collapsed">
                $purchase_return = array(
                    'notes' => Tools::empty_to_null(Tools::_str($purchase_return_data['notes'])),
                    'purchase_return_status'=>'returned',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['purchase_return'] = $purchase_return;
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                $purchase_return = array(
                    'purchase_return_status'=>'X',
                    'cancellation_reason'=>Tools::_str($purchase_return_data['cancellation_reason']),
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $result['purchase_return'] = $purchase_return;
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public function purchase_return_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Return_Engine::path_get();
        get_instance()->load->helper($path->purchase_return_data_support);
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_return = $final_data['purchase_return'];
        $fpr_product = $final_data['pr_product'];

        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($fpurchase_return['ref_id']);
        $purchase_invoice = isset($temp['purchase_invoice'])?$temp['purchase_invoice']:array();
        $pi_product = isset($temp['pi_product'])?$temp['pi_product']:array();
        $warehouse_id = '';
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        
        $ref_type = $fpurchase_return['ref_type'];
        $ref_id = $fpurchase_return['ref_id'];

        $fpurchase_return['code'] = SI::code_counter_store_get($db, 
            $fpurchase_return['store_id'],
            'purchase_return'
        );
        
        if (!$db->insert('purchase_return', $fpurchase_return)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            $purchase_return_id = $db->last_insert_id();
            $result['trans_id'] = $purchase_return_id;
            if(is_null($purchase_return_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
        }

        if ($success === 1) {
            $temp_result = SI::status_log_add($db, 'purchase_return', $purchase_return_id, $fpurchase_return['purchase_return_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        //<editor-fold defaultstate="collapsed" desc="Insert Purchase Return Product">
        if($success === 1){
            foreach($fpr_product as $idx=>$row){
                $row['purchase_return_id'] = $purchase_return_id;
                $pr_product_id = '';
                $product_batch_id = '';
                $p_ref_type = $row['ref_type'];
                $p_ref_id = $row['ref_id'];
                if (!$db->insert('pr_product', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                }
                
                if($success === 1){
                    $pr_product_id = $db->last_insert_id();
                    if(is_null($pr_product_id)){
                        $msg[] = $db->_error_message();
                        $db->trans_rollback();
                        $success = 0;                        
                    }
                }
                
                switch($p_ref_type){
                    case 'pi_product':
                        //<editor-fold defaultstate="collapsed">
                        $pi_product_row = array();
                        foreach($pi_product as $idx2=>$row2){
                            if($row['ref_id'] === $row2['id']){
                                $pi_product_row = $row2;
                                break;
                            }
                        }
                        
                        
                        //<editor-fold defaultstate="collapsed" desc="PI Product Outstanding Movement Qty">
                        if($success === 1){
                            $param = array(
                                'pi_product'=>array(
                                    'outstanding_movement_qty'=>$row['qty'],
                                ),
                            );
                            $temp_result = Purchase_Invoice_Engine::pi_product_outstanding_movement_qty_add(
                                $db, 
                                $param, 
                                $row['ref_id']
                            );
                            if($temp_result['success']!== 1){
                                $success = 0;
                                $msg = array_merge($msg, $temp_result['msg']);
                            }
                        }
                        //</editor-fold>
                        
                        //<editor-fold defaultstate="collapsed" desc="Retreive Warehouse">
                        if($success === 1){
                            $warehouse_list = Warehouse_Data_Support::warehouse_list_get(array('warehouse_status'=>'active'));
                            if(!count($warehouse_list)>0){
                                $success = 0;
                                $db->trans_rollback();
                                $msg[] = Lang::get('Retreive')
                                    .' '.Lang::get('Warehouse List')
                                    .' '.Lang::get('failed');
                            }
                            $warehouse_id = $warehouse_list[0]['warehouse']['id'];
                        }
                        //</editor-fold>

                        //<editor-fold defaultstate="collapsed" desc="Add Product Stock">
                        if($success === 1){                    

                            $temp_result = Product_Stock_Engine::stock_good_add(
                                $db,
                                'pr_product',
                                $pr_product_id,
                                $warehouse_id,
                                $pi_product_row['product_batch_id'],
                                -1 * Tools::_float($row['qty']),
                                'Purchase Return: <a target="_blank" href="{base_url}/purchase_return/view/'.$purchase_return_id.'">'.$fpurchase_return['code'].'</a>'
                                    .' '.SI::type_get('purchase_return_engine', 
                                    $fpurchase_return['purchase_return_status'],'$status_list'
                                )['text'],
                                $moddate

                            );
                            if($temp_result['success']!== 1){
                                $success = $temp_result['success'];
                                $msg = array_merge($msg, $temp_result['msg']);
                            }
                        }
                        //</editor-fold>

                        //</editor-fold>
                        break;
                }
                
                if($success !== 1) break;
            }
        }
        //</editor-fold>
        
        switch($ref_type){
            case 'purchase_invoice':
                //<editor-fold defaultstate="collapsed">                
                
                //<editor-fold defaultstate="collapsed" desc="Purchase Invoice Outstanding Grand Total Amount">
                if($success === 1){
                    $param = array(
                        'purchase_invoice'=>array(
                            'outstanding_grand_total_amount'=>-1 * Tools::_float($fpurchase_return['grand_total_amount']),
                        ),
                    );
                    Purchase_Invoice_Engine::purchase_invoice_outstanding_grand_total_amount_add($db, $param, $fpurchase_return['ref_id']);
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Add Supplier Debit">
                if($success === 1){
                    $param = array(
                        'supplier'=>array(
                            'supplier_id'=>$purchase_invoice['supplier_id'],
                            'supplier_debit_amount'=>-1 * Tools::_float($fpurchase_return['grand_total_amount']),
                            'description'=>'Purchase Return: <a href="{base_url}/purchase_return/view/'.$purchase_return_id.'" target="_blank">'.$fpurchase_return['code'].'</a>'
                                .' '.SI::type_get('purchase_return_engine', $fpurchase_return['purchase_return_status'],'$status_list')['text']
                        ),
                        'supplier_amount_log'=>array(
                            'ref_type'=>'purchase_return',
                            'ref_id'=>$purchase_return_id
                        ),
                    );
                    
                    $temp_result = Supplier_Engine::supplier_debit_credit_amount_add($db, $param, 'debit');
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>        
                
                //</editor-fold>
                break;
        }
        
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function purchase_return_returned($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Return_Engine::path_get();
        get_instance()->load->helper($path->purchase_return_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_return = $final_data['purchase_return'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_return_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('purchase_return', $fpurchase_return, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'purchase_return', $purchase_return_id, $fpurchase_return['purchase_return_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function purchase_return_canceled($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_return = $final_data['purchase_return'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_return_id = $id;
        $warehouse_id = '';

        $result['trans_id'] = $id;
        
        $temp = Purchase_Return_Data_Support::purchase_return_get($id);
        $purchase_return_db = $temp['purchase_return'];
        $pr_product_db = $temp['pr_product'];
        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($purchase_return_db['ref_id']);
        $purchase_invoice = isset($temp['purchase_invoice'])?$temp['purchase_invoice']:array();
        $pi_product = isset($temp['pi_product'])?$temp['pi_product']:array();
        
        $temp_result = self::purchase_return_returned($db, $final_data, $id);
        if($temp_result['success'] !== 1){
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        //<editor-fold defaultstate="collapsed" desc="Insert Purchase Return Product">
        if($success === 1){
            foreach($pr_product_db as $idx=>$row){
                $pr_product_id = $row['id'];
                $product_batch_id = $row['product_batch_id'];
                $p_ref_type = $row['ref_type'];
                $p_ref_id = $row['ref_id'];
                
                switch($p_ref_type){
                    case 'pi_product':
                        //<editor-fold defaultstate="collapsed">
                                                
                        //<editor-fold defaultstate="collapsed" desc="PI Product Outstanding Movement Qty">
                        if($success === 1){
                            $param = array(
                                'pi_product'=>array(
                                    'outstanding_movement_qty'=>$row['qty'],
                                ),
                            );
                            $temp_result = Purchase_Invoice_Engine::pi_product_outstanding_movement_qty_add(
                                $db, 
                                $param, 
                                $p_ref_id
                            );
                            if($temp_result['success']!== 1){
                                $success = 0;
                                $msg = array_merge($msg, $temp_result['msg']);
                            }
                        }
                        //</editor-fold>
                        
                        //<editor-fold defaultstate="collapsed" desc="Retreive Warehouse">
                        if($success === 1){
                            $warehouse_list = Warehouse_Data_Support::warehouse_list_get(array('warehouse_status'=>'active'));
                            if(!count($warehouse_list)>0){
                                $success = 0;
                                $db->trans_rollback();
                                $msg[] = Lang::get('Retreive')
                                    .' '.Lang::get('Warehouse List')
                                    .' '.Lang::get('failed');
                            }
                            $warehouse_id = $warehouse_list[0]['warehouse']['id'];
                        }
                        //</editor-fold>

                        //<editor-fold defaultstate="collapsed" desc="Add Product Stock">
                        if($success === 1){                    

                            $temp_result = Product_Stock_Engine::stock_good_add(
                                $db,
                                'pr_product',
                                $pr_product_id,
                                $warehouse_id,
                                $row['product_batch_id'],
                                Tools::_float($row['qty']),
                                'Purchase Return: <a target="_blank" href="{base_url}/purchase_return/view/'.$purchase_return_id.'">'.$purchase_return_db['code'].'</a>'
                                    .' '.SI::type_get('purchase_return_engine', 
                                    $fpurchase_return['purchase_return_status'],'$status_list'
                                )['text'],
                                $moddate

                            );
                            if($temp_result['success']!== 1){
                                $success = $temp_result['success'];
                                $msg = array_merge($msg, $temp_result['msg']);
                            }
                        }
                        //</editor-fold>
                        
                        //</editor-fold>
                        break;
                }
                
                if($success !== 1) break;
            }
        }
        //</editor-fold>
        
        switch($purchase_return_db['ref_type']){
            case 'purchase_invoice':
                
                //<editor-fold defaultstate="collapsed" desc="Purchase Invoice Outstanding Grand Total Amount">
                if($success === 1){
                    $param = array(
                        'purchase_invoice'=>array(
                            'outstanding_grand_total_amount'=>Tools::_float($purchase_return_db['grand_total_amount']),
                        ),
                    );
                    Purchase_Invoice_Engine::purchase_invoice_outstanding_grand_total_amount_add($db, $param, $purchase_return_db['ref_id']);
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Add Supplier Debit">
                if($success === 1){
                    $param = array(
                        'supplier'=>array(
                            'supplier_id'=>$purchase_invoice['supplier_id'],
                            'supplier_debit_amount'=>$purchase_return_db['grand_total_amount'],
                            'description'=>'Purchase Return: <a href="{base_url}/purchase_return/view/'.$purchase_return_id.'" target="_blank">'.$purchase_return_db['code'].'</a>'
                                .' '.SI::type_get('purchase_return_engine', $fpurchase_return['purchase_return_status'],'$status_list')['text']
                        ),
                        'supplier_amount_log'=>array(
                            'ref_type'=>'purchase_return',
                            'ref_id'=>$purchase_return_id
                        ),
                    );
                    
                    $temp_result = Supplier_Engine::supplier_debit_credit_amount_add($db, $param, 'debit');
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>        

                break;
        }
        
        
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

}

?>
