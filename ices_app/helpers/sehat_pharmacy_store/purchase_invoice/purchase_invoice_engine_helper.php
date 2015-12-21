<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Purchase_Invoice_Engine {

    public static $prefix_id = 'purchase_invoice';
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
                , 'method' => 'purchase_invoice_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Purchase Invoice'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'invoiced'
                , 'text' => 'INVOICED'
                , 'method' => 'purchase_invoice_invoiced'
                , 'next_allowed_status' => array('X')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Purchase Invoice'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'X'
                , 'text' => 'CANCELED'
                , 'method' => 'purchase_invoice_canceled'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Cancel')
                        , array('val' => Lang::get(array('Purchase Invoice'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'purchase_invoice/'
            , 'purchase_invoice_engine' => ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_engine'
            , 'purchase_invoice_data_support' => ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_data_support'
            , 'purchase_invoice_renderer' => ICES_Engine::$app['app_base_dir'] . 'purchase_invoice/purchase_invoice_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'purchase_invoice/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'purchase_invoice/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        get_instance()->load->helper($path->purchase_invoice_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $purchase_invoice = isset($data['purchase_invoice']) ? Tools::_arr($data['purchase_invoice']) : array();
        $purchase_invoice_product = isset($data['purchase_invoice_product']) ? Tools::_arr($data['purchase_invoice_product']) : array();
        $purchase_invoice_id = $purchase_invoice['id'];
        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($purchase_invoice_id);
        $purchase_invoice_db = isset($temp['purchase_invoice'])?$temp['purchase_invoice']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
                SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_data_support'));
                SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
                
                if (!(isset($purchase_invoice['store_id']) 
                        && isset($purchase_invoice['supplier_id']) 
                        && isset($purchase_invoice['supplier_si_code']) 
                        && isset($purchase_invoice['purchase_invoice_status'])
                        && isset($purchase_invoice['total_discount_amount'])
                        && isset($purchase_invoice['additional_cost_amount'])
                        && isset($purchase_invoice['notes'])
                        && isset($data['purchase_invoice_product'])
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Purchase Invoice')
                            . ' ' . Lang::get('or',true, false). ' ' . Lang::get('Purchase Invoice Product')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_id = Tools::empty_to_null(Tools::_str($purchase_invoice['store_id']));
                    $supplier_id = Tools::empty_to_null(Tools::_str($purchase_invoice['supplier_id']));
                    $supplier_si_code = Tools::empty_to_null(Tools::_str($purchase_invoice['supplier_si_code']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($store_id) 
                        || is_null($supplier_id)
                        || is_null($supplier_si_code)
                        || (!count($purchase_invoice_product)>0)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Store')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Supplier')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Sales Invoice').' '.Lang::get('Supplier')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Purchase Invoice Product')
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
                    
                    $temp_result = Supplier_Data_Support::supplier_validate($supplier_id);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    $total_amount = Tools::_float('0');                    
                    //<editor-fold defaultstate="collapsed" desc="Product Validation">
                    $local_success = 1;
                    $local_msg = array();
                    $temp_result = Product_Data_Support::product_list_validate($purchase_invoice_product);
                    if($temp_result['success']!== 1){
                        $local_success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    if($local_success === 1){
                        foreach($purchase_invoice_product as $idx=>$row){
                            $expired_date = Tools::empty_to_null(isset($row['expired_date'])?$row['expired_date']:'');
                            $amount = Tools::_float(isset($row['amount'])?$row['amount']:'');
                            $qty = Tools::_float(isset($row['qty'])?$row['qty']:'');
                            $total_amount+=Tools::_float($amount) * Tools::_float($qty);
                            
                            if(is_null($expired_date)
                                || Tools::_float($amount) < Tools::_float('0')
                                || Tools::_float($qty) <= Tools::_float('0')
                            ){
                                $local_success = 0;
                                $msg[] = Lang::get('Expired Date')
                                    .' '.Lang::get('or',true,false).' '.Lang::get('Qty')
                                    .' '.Lang::get('empty',true,false);
                            }
                            
                            if(strtotime(Tools::_date($expired_date)) < strtotime(Tools::_date(''))){
                                $local_success = 0;
                                $msg[] = Lang::get('Expired Date')
                                    .' '.Lang::get('must be greater than',true,false,false,true)
                                    .' '.Lang::get(Tools::_date('','F d, Y H:i'));
                            }
                            
                            foreach($purchase_invoice_product as $idx2=>$row2){
                                if($row['product_id'] === $row2['product_id']
                                    && $row['unit_id'] === $row2['unit_id']
                                    && Tools::_date($row['expired_date'],'Y-m-d') === Tools::_date($row2['expired_date'],'Y-m-d')
                                    && Tools::_float($row['amount']) === Tools::_float($row2['amount'])
                                    && $idx !== $idx2
                                ){
                                    $local_success = 0;
                                    $msg[] = Lang::get('Product, Amount, '.Lang::get('and',true, false).' Expired Date')
                                        .' '.Lang::get('duplicate',true,false);
                                    break;
                                }
                            }
                            
                            if($local_success!== 1) break;
                        }
                    }
                    
                    if($local_success !== 1) $success = $local_success;
                    //</editor-fold>

                    $total_discount_amount= Tools::_float($purchase_invoice['total_discount_amount']);
                    if(Tools::_float($total_discount_amount)> Tools::_float($total_amount)){
                        $success = 0;
                        $msg[] = Lang::get('Total Disc. Amount')
                            .' '.Lang::get('invalid',true,false);
                                
                    }
                    
                    $additional_cost_amount= Tools::_float($purchase_invoice['additional_cost_amount']);
                    if(Tools::_float($additional_cost_amount)> Tools::_float($total_amount)){
                        $success = 0;
                        $msg[] = Lang::get('Total Additonal Cost Amount')
                            .' '.Lang::get('invalid',true,false);
                                
                    }
                    
                    
                }
                //</editor-fold>
                break;
            case self::$prefix_method . '_invoiced':
                //<editor-fold defaultstate="collapsed">
                
                //<editor-fold defaultstate="collapsed" desc="Major Validation">
               
                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_update(
                        array(
                            'module' => 'purchase_invoice',
                            'module_name' => Lang::get('Purchase Invoice'),
                            'module_engine' => 'purchase_invoice_engine',
                        ), 
                        $purchase_invoice
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
                if (!count($purchase_invoice_db) > 0) {
                    $success = 0;
                    $msg[] = 'Invalid Purchase Invoice';
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_cancel(
                                    array(
                                'module' => 'purchase_invoice',
                                'module_name' => Lang::get('Purchase Invoice'),
                                'module_engine' => 'purchase_invoice_engine',
                                    ), $purchase_invoice
                    );
                    $success = $temp_result['success'];
                    $msg = array_merge($msg,$temp_result['msg']);
                }
                
                if($success !== 1) break;
                //</editor-fold>
                
                if(Tools::_float($purchase_invoice_db['outstanding_grand_total_amount'])
                    !== Tools::_float($purchase_invoice_db['grand_total_amount']) ){
                    $success = 0;
                    $msg[] = Lang::get('Purchase Receipt or Return')
                        .' '.Lang::get('exists',true,false)
                    ;
                }
                
                $q = '
                    select distinct 1
                    from product_batch pb
                    inner join pi_product pip 
                        on pb.ref_type = "pi_product" and pb.ref_id = pip.id
                        and pb.qty <> pip.qty
                    where pip.purchase_invoice_id = '.$db->escape($purchase_invoice_id).'
                ';
                
                $rs = $db->query_array($q);
                if(count($rs)>0){
                    $success = 0;
                    $msg[] = Lang::get('Purchase Invoice Product Qty')
                        .' '.Lang::get('different from',true,false,false,true)
                        .' '.Lang::get('Product Batch Qty')
                    ;
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

        $purchase_invoice_data = isset($data['purchase_invoice']) ? $data['purchase_invoice'] : array();
        $purchase_invoice_product_data = isset($data['purchase_invoice_product']) ? $data['purchase_invoice_product'] : array();

        $temp_purchase_invoice = Purchase_Invoice_Data_Support::purchase_invoice_get($purchase_invoice_data['id']);
        $purchase_invoice_db = isset($temp_purchase_invoice['purchase_invoice'])?$temp_purchase_invoice['purchase_invoice']:array();
        
        $purchase_invoice_id = $purchase_invoice_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                $purchase_invoice = array(
                    'purchase_invoice_type'=>'back_office',
                    'store_id' => Tools::_str($purchase_invoice_data['store_id']),
                    'supplier_id' => Tools::_str($purchase_invoice_data['supplier_id']),
                    'purchase_invoice_date'=>Tools::_date(),
                    'supplier_si_code'=>Tools::_str($purchase_invoice_data['supplier_si_code']),
                    'notes' => Tools::empty_to_null(Tools::_str($purchase_invoice_data['notes'])),
                    'purchase_invoice_status'=>'invoiced',
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $total_amount = Tools::_float('0');
                $total_discount_amount = Tools::_float(Tools::_str($purchase_invoice_data['total_discount_amount']));
                $additional_cost_amount = Tools::_float(Tools::_str($purchase_invoice_data['additional_cost_amount']));
                $grand_total_amount = Tools::_float('0');
                $pi_product = array();
                
                foreach($purchase_invoice_product_data as $idx=>$row){
                    $subtotal_amount = Tools::_float($row['qty']) * Tools::_float($row['amount']);
                    $total_amount+= $subtotal_amount;
                    $pi_product[] = array(
                        'product_type'=>Tools::_str($row['product_type']),
                        'product_id'=>Tools::_str($row['product_id']),
                        'unit_id'=>Tools::_str($row['unit_id']),
                        'expired_date'=>Tools::_date($row['expired_date']),
                        'qty'=>Tools::_str($row['qty']),
                        'outstanding_movement_qty'=>'0',
                        'amount'=>Tools::_str($row['amount']),
                        'subtotal_amount'=>$subtotal_amount,
                    );
                }
                
                $grand_total_amount = $total_amount - $total_discount_amount + $additional_cost_amount;
                $purchase_invoice['total_amount'] = $total_amount;
                $purchase_invoice['total_discount_amount'] = $total_discount_amount;
                $purchase_invoice['additional_cost_amount'] = $additional_cost_amount;
                $purchase_invoice['grand_total_amount'] = $grand_total_amount;
                $purchase_invoice['outstanding_grand_total_amount'] = $grand_total_amount;
                
                $result['purchase_invoice'] = $purchase_invoice;
                $result['pi_product'] = $pi_product;
                //</editor-fold>
                break;
            case self::$prefix_method . '_invoiced':
                //<editor-fold defaultstate="collapsed">
                $purchase_invoice = array(
                    'notes' => Tools::empty_to_null(Tools::_str($purchase_invoice_data['notes'])),
                    'purchase_invoice_status'=>'invoiced',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['purchase_invoice'] = $purchase_invoice;
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                $purchase_invoice = array(
                    'purchase_invoice_status'=>'X',
                    'cancellation_reason'=>Tools::_str($purchase_invoice_data['cancellation_reason']),
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $result['purchase_invoice'] = $purchase_invoice;
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public function purchase_invoice_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        get_instance()->load->helper($path->purchase_invoice_data_support);
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_invoice = $final_data['purchase_invoice'];
        $fpi_product = $final_data['pi_product'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $warehouse_id = '';

        $fpurchase_invoice['code'] = SI::code_counter_store_get($db, 
            $fpurchase_invoice['store_id'],
            'purchase_invoice'
        );
        
        if (!$db->insert('purchase_invoice', $fpurchase_invoice)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            $purchase_invoice_id = $db->last_insert_id();
            $result['trans_id'] = $purchase_invoice_id;
            if(is_null($purchase_invoice_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
            
        }

        if ($success === 1) {
            $temp_result = SI::status_log_add($db, 'purchase_invoice', $purchase_invoice_id, $fpurchase_invoice['purchase_invoice_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        //<editor-fold defaultstate="collapsed" desc="Insert Purchase Invoice Product">
        if($success === 1){
            foreach($fpi_product as $idx=>$row){
                $row['purchase_invoice_id'] = $purchase_invoice_id;
                $pi_product_id = '';
                $product_batch_id = '';
                if (!$db->insert('pi_product', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                }
                
                //<editor-fold defaultstate="collapsed" desc="Retreive Purchase Invoice Product">
                if($success === 1){
                    $pi_product_id = $db->last_insert_id();
                    if(is_null($pi_product_id)){
                        $success = 0;
                        $msg[] = 'Retrieve'
                            .' '.Lang::get('Purchase Invoice Product')
                            .' '.Lang::get('failed',true,false)
                        ;
                        $db->trans_rollback();
                    }
                    
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Add Product Batch">
                if($success === 1){                    
                    
                    $param = array(
                        'product_batch'=>array(
                            'ref_type'=>'pi_product',
                            'ref_id'=>$pi_product_id,
                            'product_type'=>$row['product_type'],
                            'product_id'=>$row['product_id'],
                            'unit_id'=>$row['unit_id'],
                            'expired_date'=>$row['expired_date'],
                            'product_batch_status'=>SI::type_default_type_get('product_batch_engine','$status_list')['val'],
                            'purchase_amount'=>$row['amount'],
                            'qty'=>'0',
                            'modid'=>$modid,
                            'moddate'=>$moddate,
                            'status'=>'1'
                        ),
                        'product_batch_qty_log'=>array(
                            'old_qty' => '0',
                            'qty' => '0',
                            'new_qty' => '0',
                            'ref_type'=>'pi_product',
                            'ref_id'=>$pi_product_id,
                            'description'=>'Purchase Invoice: <a target="_blank" href="{base_url}/purchase_invoice/view/'.$purchase_invoice_id.'">'.$fpurchase_invoice['code'].'</a>'
                            .' '.SI::type_get('purchase_invoice_engine', 
                                $fpurchase_invoice['purchase_invoice_status'],'$status_list'
                            )['text'],
                            'modid'=>$modid,
                            'moddate'=>$moddate
                        ),
                    );
                    
                    $temp_result = Product_Batch_Engine::product_batch_add($db, $param);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    else{
                        $product_batch_id = $temp_result['trans_id'];
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
                        'pi_product',
                        $pi_product_id,
                        $warehouse_id,
                        $product_batch_id,
                        $row['qty'],
                        'Purchase Invoice: <a target="_blank" href="{base_url}/purchase_invoice/view/'.$purchase_invoice_id.'">'.$fpurchase_invoice['code'].'</a>'
                            .' '.SI::type_get('purchase_invoice_engine', 
                            $fpurchase_invoice['purchase_invoice_status'],'$status_list'
                        )['text'],
                        $moddate

                    );
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Set Product Purchase Amount">
                if($success ===1){
                    $param =array(
                        'p_u'=>array(
                            'product_id'=>$row['product_id'],
                            'unit_id'=>$row['unit_id']
                        )
                    );
                    $temp_result = Product_Engine::purchase_amount_recalculate($db,$param);
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
        
        //<editor-fold defaultstate="collapsed" desc="Add Supplier Debit">
        if($success === 1){
            $param = array(
                'supplier'=>array(
                    'supplier_id'=>$fpurchase_invoice['supplier_id'],
                    'supplier_debit_amount'=>$fpurchase_invoice['grand_total_amount'],
                    'description'=>'Purchase Invoice: <a href="{base_url}/purchase_invoice/view/'.$purchase_invoice_id.'" target="_blank">'.$fpurchase_invoice['code'].'</a>'
                        .' '.SI::type_get('purchase_invoice_engine', $fpurchase_invoice['purchase_invoice_status'],'$status_list')['text']
                ),
                'supplier_amount_log'=>array(
                    'ref_type'=>'purchase_invoice',
                    'ref_id'=>$purchase_invoice_id
                ),
            );
            
            $temp_result = Supplier_Engine::supplier_debit_credit_amount_add($db, $param, 'debit');
            if($temp_result['success']!== 1){
                $success = $temp_result['success'];
                $msg = array_merge($msg, $temp_result['msg']);
            }
        }
        //</editor-fold>        
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function purchase_invoice_invoiced($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        get_instance()->load->helper($path->purchase_invoice_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_invoice = $final_data['purchase_invoice'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_invoice_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('purchase_invoice', $fpurchase_invoice, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'purchase_invoice', $purchase_invoice_id, $fpurchase_invoice['purchase_invoice_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function purchase_invoice_canceled($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Invoice_Engine::path_get();
        get_instance()->load->helper($path->purchase_invoice_data_support);
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_invoice = $final_data['purchase_invoice'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_invoice_id = $id;

        $result['trans_id'] = $id;
        
        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($id);
        $purchase_invoice_db = $temp['purchase_invoice'];
        $pi_product_db = $temp['pi_product'];
        
        $temp_result = self::purchase_invoice_invoiced($db, $final_data, $id);
        if($temp_result['success'] !== 1){
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        if($success === 1){
            foreach($pi_product_db as $idx=>$row){
                $product_batch_id = $row['product_batch_id'];
                
                //<editor-fold defaultstate="collapsed" desc="Product Movement Outstanding">
                $t_param = array(
                    'pi_product'=>array(
                        'outstanding_movement_qty'=>$row['qty']
                    )
                );
                $temp_result = self::pi_product_outstanding_movement_qty_add($db, $t_param, $row['id']);
                if($temp_result['success']!== 1){
                    $success = 0;
                    $msg = array_merge($msg, $temp_result['msg']);
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Add Product Stock">
                if($success === 1){
                    $psg_list = array();
                    $q = '
                        select psg.*
                        from product_stock_good psg
                        where psg.product_batch_id = '.$db->escape($product_batch_id).'
                            and psg.status > 0
                    ';
                    $rs = $db->query_array($q);
                    if(count($rs)>0) $psg_list = $rs;

                    foreach($psg_list as $psg_idx=>$psg_row){
                        $temp_result = Product_Stock_Engine::stock_good_add(
                            $db,
                            'pi_product',
                            $psg_row['id'],
                            $psg_row['warehouse_id'],
                            $product_batch_id,
                            -1 * Tools::_float($psg_row['qty']),
                            'Purchase Invoice: <a target="_blank" href="{base_url}/purchase_invoice/view/'.$purchase_invoice_id.'">'.$purchase_invoice_db['code'].'</a>'
                                .' '.SI::type_get('purchase_invoice_engine', 
                                $fpurchase_invoice['purchase_invoice_status'],'$status_list'
                            )['text'],
                            $moddate

                        );
                        if($temp_result['success']!== 1){
                            $success = $temp_result['success'];
                            $msg = array_merge($msg, $temp_result['msg']);
                        }
                    }

                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Inactive Product Batch Qty">
                if($success === 1){
                    $param = array(
                        'product_batch'=>array(
                            'product_batch_status'=>'inactive',
                            'modid'=>$modid,
                            'moddate'=>$moddate
                        ),
                    );
                    
                    $temp_result = Product_Batch_Engine::product_batch_inactive($db, $param,$product_batch_id);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Set Product Purchase Amount">
                if($success ===1){
                    $param =array(
                        'p_u'=>array(
                            'product_id'=>$row['product_id'],
                            'unit_id'=>$row['unit_id']
                        )
                    );
                    $temp_result = Product_Engine::purchase_amount_recalculate($db,$param,null);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>
                
                
                if($success !== 1) break;
            }
        }
        
        //<editor-fold defaultstate="collapsed" desc="Add Supplier Debit">
        if($success === 1){
            $param = array(
                'supplier'=>array(
                    'supplier_id'=>$purchase_invoice_db['supplier_id'],
                    'supplier_debit_amount'=>-1 * Tools::_float($purchase_invoice_db['grand_total_amount']),
                    'description'=>'Purchase Invoice: <a href="{base_url}/purchase_invoice/view/'.$purchase_invoice_id.'" target="_blank">'.$purchase_invoice_db['code'].'</a>'
                        .' '.SI::type_get('purchase_invoice_engine', $fpurchase_invoice['purchase_invoice_status'],'$status_list')['text']
                ),
                'supplier_amount_log'=>array(
                    'ref_type'=>'purchase_invoice',
                    'ref_id'=>$purchase_invoice_id
                ),
            );
            
            $temp_result = Supplier_Engine::supplier_debit_credit_amount_add($db, $param, 'debit');
            if($temp_result['success']!== 1){
                $success = $temp_result['success'];
                $msg = array_merge($msg, $temp_result['msg']);
            }
        }
        //</editor-fold>        
        
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public static function purchase_invoice_outstanding_grand_total_amount_add($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
        
        $fpurchase_invoice = $final_data['purchase_invoice'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_invoice_id = $id;

        $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($id);
        $purchase_invoice_db = $temp['purchase_invoice'];
        
        $result['trans_id'] = $id;
        
        $q = '
            update purchase_invoice 
            set outstanding_grand_total_amount = outstanding_grand_total_amount+'.$db->escape($fpurchase_invoice['outstanding_grand_total_amount']).'
                ,modid = '.$db->escape($modid).'
                ,moddate = '.$db->escape($moddate).'
            where id = '.$db->escape($purchase_invoice_id).'
        ';
        
        if(!$db->query($q)){
            $success = 0;
            $msg[] = $db->_error_message();
            $db->trans_rollback();
        }
                
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    public static function pi_product_outstanding_movement_qty_add($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
        
        $fpi_product = $final_data['pi_product'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_invoice_id = $id;
        
        $result['trans_id'] = $id;
        
        $q = '
            update pi_product 
            set outstanding_movement_qty = outstanding_movement_qty+'.$db->escape($fpi_product['outstanding_movement_qty']).'
            where id = '.$db->escape($purchase_invoice_id).'
        ';
        
        if(!$db->query($q)){
            $success = 0;
            $msg[] = $db->_error_message();
            $db->trans_rollback();
        }
                
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
}

?>
