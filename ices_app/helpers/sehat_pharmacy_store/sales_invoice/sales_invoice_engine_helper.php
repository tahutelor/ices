<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_Invoice_Engine {

    public static $prefix_id = 'sales_invoice';
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
                , 'method' => 'sales_invoice_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Sales Invoice'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'invoiced'
                , 'text' => 'INVOICED'
                , 'method' => 'sales_invoice_invoiced'
                , 'next_allowed_status' => array('X')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Sales Invoice'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'X'
                , 'text' => 'CANCELED'
                , 'method' => 'sales_invoice_canceled'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Cancel')
                        , array('val' => Lang::get(array('Sales Invoice'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'sales_invoice/'
            , 'sales_invoice_engine' => ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_engine'
            , 'sales_invoice_data_support' => ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_data_support'
            , 'sales_invoice_renderer' => ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_renderer'
            , 'sales_invoice_print' => ICES_Engine::$app['app_base_dir'] . 'sales_invoice/sales_invoice_print'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'sales_invoice/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'sales_invoice/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        get_instance()->load->helper($path->sales_invoice_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $sales_invoice = isset($data['sales_invoice']) ? Tools::_arr($data['sales_invoice']) : array();
        $si_product = isset($data['si_product']) ? Tools::_arr($data['si_product']) : array();
        $sales_invoice_id = $sales_invoice['id'];
        $temp = Sales_Invoice_Data_Support::sales_invoice_get($sales_invoice_id);
        $sales_invoice_db = isset($temp['sales_invoice'])?$temp['sales_invoice']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
                SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_data_support'));
                SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
                SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_data_support'));
                
                if (!(isset($sales_invoice['store_id']) 
                        && isset($sales_invoice['customer_id']) 
                        && isset($sales_invoice['sales_invoice_status'])
                        && isset($sales_invoice['total_discount_amount'])
                        && isset($sales_invoice['notes'])
                        && isset($data['si_product'])
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Sales Invoice')
                            . ' ' . Lang::get('or',true, false). ' ' . Lang::get('Sales Invoice Product')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_id = Tools::empty_to_null(Tools::_str($sales_invoice['store_id']));
                    $customer_id = Tools::empty_to_null(Tools::_str($sales_invoice['customer_id']));
                    $total_discount_amount = Tools::_float($sales_invoice['total_discount_amount']);

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($store_id) 
                        || is_null($customer_id)
                        || (!count($si_product)>0)
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Store')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Customer')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Sales Invoice').' '.Lang::get('Customer')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Sales Invoice Product')
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
                    
                    $temp_result = Customer_Data_Support::customer_validate($customer_id);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    $total_amount = Tools::_float('0');                    
                    //<editor-fold defaultstate="collapsed" desc="Product Validation">
                    $local_success = 1;
                    $local_msg = array();
                    $temp_result = Product_Data_Support::product_list_validate($si_product);
                    if($temp_result['success']!== 1){
                        $local_success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    $product_list = Product_Data_Support::product_list_get($si_product,
                        array(
                            'q_condition'=>' and p.product_status = "active" and pb.expired_date>now() '
                        )
                    );
                    
                    if($local_success === 1){
                        foreach($si_product as $idx=>$row){
                            $product_type = Tools::_str($row['product_type']);
                            $product_id = Tools::_str($row['product_id']);
                            $unit_id = Tools::_str($row['unit_id']);
                            $unit_id_sales = Tools::_str(isset($row['unit_id_sales'])?$row['unit_id_sales']:'');
                            $qty = Tools::_float(isset($row['qty'])?$row['qty']:'');
                            $stock_qty = Tools::_float('0');
                            $amount = Tools::_float('0');
                            
                            $product_exists = false;
                            
                            foreach($product_list as $idx2=>$row2){
                                if($product_type ===$row2['product_type']
                                    && $row2['product_id'] === $product_id
                                    && $row2['unit_id'] === $unit_id
                                    && $row2['unit_id_sales'] === $unit_id_sales
                                ){
                                    $amount = $row2['sales_amount'];
                                    $stock_qty = $row2['qty'];
                                    $product_exists = true;
                                }
                            }
                            
                            $total_amount+=Tools::_float($amount) * Tools::_float($qty);
                            
                            if($product_exists){
                                if(Tools::_float($qty)> Tools::_float($stock_qty)){
                                    $local_success = 0;
                                    $msg[] = Lang::get('Qty')
                                        .' '.Lang::get('invalid',true,false);
                                }

                                if(Tools::_float($amount)<=Tools::_float('0')){
                                    $local_success = 0;
                                    $msg[] = Lang::get('Amount')
                                        .' '.Tools::thousand_separator('0')
                                    ;
                                }
                            }
                            
                            
                            
                            foreach($si_product as $idx2=>$row2){
                                if($product_id === $row2['product_id']
                                    && $unit_id === $row2['unit_id']
                                    && $idx !== $idx2
                                ){
                                    $local_success = 0;
                                    $msg[] = Lang::get('Product')
                                        .' '.Lang::get('duplicate',true,false);
                                    break;
                                }
                            }
                            
                            if(!$product_exists){
                                $msg[] = Lang::get('Product')
                                    .' '.Lang::get('invalid',true,false)
                                ;
                            }
                            
                            
                            if($local_success!== 1) break;
                        }
                    }
                    
                    if($local_success !== 1) $success = $local_success;
                    //</editor-fold>

                    $total_discount_amount= Tools::_float($sales_invoice['total_discount_amount']);
                    if(Tools::_float($total_discount_amount)> Tools::_float($total_amount)){
                        $success = 0;
                        $msg[] = Lang::get('Total Disc. Amount')
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
                            'module' => 'sales_invoice',
                            'module_name' => Lang::get('Sales Invoice'),
                            'module_engine' => 'sales_invoice_engine',
                        ), 
                        $sales_invoice
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
                if (!count($sales_invoice_db) > 0) {
                    $success = 0;
                    $msg[] = 'Invalid Sales Invoice';
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_cancel(
                                    array(
                                'module' => 'sales_invoice',
                                'module_name' => Lang::get('Sales Invoice'),
                                'module_engine' => 'sales_invoice_engine',
                                    ), $sales_invoice
                    );
                    $success = $temp_result['success'];
                    $msg = array_merge($msg,$temp_result['msg']);
                }
                
                if($success !== 1) break;
                //</editor-fold>
                
                if(Tools::_float($sales_invoice_db['outstanding_grand_total_amount'])
                    !== Tools::_float($sales_invoice_db['grand_total_amount']) ){
                    $success = 0;
                    $msg[] = Lang::get('Sales Receipt')
                        .' '.Lang::get('exists',true,false)
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
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
        $db = new DB();
        $result = array();

        $sales_invoice_data = isset($data['sales_invoice']) ? $data['sales_invoice'] : array();
        $si_product_data = isset($data['si_product']) ? $data['si_product'] : array();

        $temp_sales_invoice = Sales_Invoice_Data_Support::sales_invoice_get($sales_invoice_data['id']);
        $sales_invoice_db = isset($temp_sales_invoice['sales_invoice'])?$temp_sales_invoice['sales_invoice']:array();
        
        $sales_invoice_id = $sales_invoice_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                $sales_invoice = array(
                    'sales_invoice_type'=>'back_office',
                    'store_id' => Tools::_str($sales_invoice_data['store_id']),
                    'customer_id' => Tools::_str($sales_invoice_data['customer_id']),
                    'sales_invoice_date'=>Tools::_date(),
                    'notes' => Tools::empty_to_null(Tools::_str($sales_invoice_data['notes'])),
                    'sales_invoice_status'=>'invoiced',
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $total_amount = Tools::_float('0');
                $total_discount_amount = Tools::_float(Tools::_str($sales_invoice_data['total_discount_amount']));
                $grand_total_amount = Tools::_float('0');
                $si_product = array();
                
                $product_list = Product_Data_Support::product_list_get($si_product_data,
                    array(
                        'q_condition'=>' and p.product_status = "active" and pb.expired_date>now() '
                    )
                );
                
                foreach($si_product_data as $idx=>$row){
                    $product = array();
                    $amount = '0';
                    foreach($product_list as $idx2=>$row2){
                        if($row['product_type'] === $row2['product_type']
                            && $row['product_id'] === $row2['product_id']
                            && $row['unit_id'] === $row2['unit_id']
                            && $row['unit_id_sales'] === $row2['unit_id_sales']
                        ){
                            $product = $row2;
                        }
                    }
                    $amount = $product['sales_amount'];
                    
                    $subtotal_amount = Tools::_float($row['qty']) * Tools::_float($amount);
                    $total_amount+= $subtotal_amount;
                    $si_product[] = array(
                        'product_type'=>Tools::_str($row['product_type']),
                        'product_id'=>Tools::_str($row['product_id']),
                        'unit_id'=>Tools::_str($row['unit_id']),
                        'qty'=>Tools::_str($row['qty']),
                        'outstanding_movement_qty'=>'0',
                        'amount'=>$amount,
                        'subtotal_amount'=>$subtotal_amount,
                        'unit_id_sales'=>$row['unit_id_sales'],
                        'constant_sales'=>$product['constant_sales']
                    );
                }
                
                $grand_total_amount = $total_amount - $total_discount_amount;
                $sales_invoice['total_amount'] = $total_amount;
                $sales_invoice['total_discount_amount'] = $total_discount_amount;
                $sales_invoice['grand_total_amount'] = $grand_total_amount;
                $sales_invoice['outstanding_grand_total_amount'] = $grand_total_amount;
                
                $result['sales_invoice'] = $sales_invoice;
                $result['si_product'] = $si_product;
                
                //</editor-fold>
                break;
            case self::$prefix_method . '_invoiced':
                //<editor-fold defaultstate="collapsed">
                $sales_invoice = array(
                    'notes' => Tools::empty_to_null(Tools::_str($sales_invoice_data['notes'])),
                    'sales_invoice_status'=>'invoiced',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['sales_invoice'] = $sales_invoice;
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                $sales_invoice = array(
                    'sales_invoice_status'=>'X',
                    'cancellation_reason'=>Tools::_str($sales_invoice_data['cancellation_reason']),
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $result['sales_invoice'] = $sales_invoice;
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public function sales_invoice_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        get_instance()->load->helper($path->sales_invoice_data_support);
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_engine'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsales_invoice = $final_data['sales_invoice'];
        $fsi_product = $final_data['si_product'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        

        $fsales_invoice['code'] = SI::code_counter_store_get($db, 
            $fsales_invoice['store_id'],
            'sales_invoice'
        );
        
        if (!$db->insert('sales_invoice', $fsales_invoice)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            $sales_invoice_id = $db->last_insert_id();
            $result['trans_id'] = $sales_invoice_id;
            if(is_null($sales_invoice_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
            
        }

        if ($success === 1) {
            $temp_result = SI::status_log_add($db, 'sales_invoice', $sales_invoice_id, $fsales_invoice['sales_invoice_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        //<editor-fold defaultstate="collapsed" desc="Insert Sales Invoice Product">
        if($success === 1){
            foreach($fsi_product as $idx=>$row){
                $row['sales_invoice_id'] = $sales_invoice_id;
                $si_product_id = '';
                
                if (!$db->insert('si_product', $row)) {
                    $msg[] = $db->_error_message();
                    $db->trans_rollback();
                    $success = 0;
                }
                
                //<editor-fold defaultstate="collapsed" desc="Retreive Sales Invoice Product">
                if($success === 1){
                    $si_product_id = $db->last_insert_id();
                    if(is_null($si_product_id)){
                        $success = 0;
                        $msg[] = 'Retrieve'
                            .' '.Lang::get('Sales Invoice Product')
                            .' '.Lang::get('failed',true,false)
                        ;
                        $db->trans_rollback();
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
                }
                //</editor-fold>

                //<editor-fold defaultstate="collapsed" desc="Add Product Stock & SI Product Mov Qty">
                if($success === 1){     
                    $outstanding_qty = Tools::_float($row['qty']);

                    foreach($warehouse_list as $w_idx=>$w_row){
                        $warehouse_id = $w_row['warehouse']['id'];
                        
                        $q = '
                            select distinct psg.*
                            from product_stock_good psg
                                inner join product_batch pb on psg.product_batch_id = pb.id
                            where psg.status > 0
                                and pb.status > 0
                                and pb.product_batch_status = "active"
                                and psg.warehouse_id = '.$db->escape($warehouse_id).'
                                and pb.product_id = '.$db->escape($row['product_id']).'
                                and pb.unit_id = '.$db->escape($row['unit_id']).'
                                and psg.qty > 0
                                and pb.expired_date > now()
                            order by pb.expired_date asc                                
                        ';
                        
                        $rs_psg = $db->query_array($q);
                        foreach($rs_psg as $psg_idx=> $psg_row){
                            $product_batch_id = $psg_row['product_batch_id'];
                            $si_product_mov_qty_id = '';
                            $qty_used = Tools::_float($outstanding_qty) > Tools::_float($psg_row['qty'])?
                                Tools::_float($psg_row['qty']):
                                Tools::_float($outstanding_qty)
                            ;
                            $outstanding_qty -= Tools::_float($qty_used);
                            $product_stock_good_qty_log_id = '';
                            
                            if($success === 1){                                
                                
                                $param_si_product_mov_qty = array(
                                   'si_product_id'=>$si_product_id,
                                   'ref_type'=>'product_stock_good',
                                   'ref_id'=>$psg_row['id'],
                                    'qty'=>$qty_used,                                   
                                );
                                
                                if (!$db->insert('si_product_mov_qty', $param_si_product_mov_qty)) {
                                    $msg[] = $db->_error_message();
                                    $db->trans_rollback();
                                    $success = 0;
                                }
                                else{
                                    $si_product_mov_qty_id = $db->last_insert_id();
                                }
                                
                            }
                            
                            if($success === 1){
                                $temp_result = Product_Stock_Engine::stock_good_add(
                                    $db,
                                    'si_product_mov_qty',
                                    $si_product_mov_qty_id,
                                    $warehouse_id,
                                    $product_batch_id,
                                    -1 * $qty_used,
                                    'Sales Invoice: <a target="_blank" href="{base_url}/sales_invoice/view/'.$sales_invoice_id.'">'.$fsales_invoice['code'].'</a>'
                                        .' '.SI::type_get('sales_invoice_engine', 
                                        $fsales_invoice['sales_invoice_status'],'$status_list'
                                    )['text'],
                                    $moddate

                                );
                            }
                            if($temp_result['success']!== 1){
                                $success = $temp_result['success'];
                                $msg = array_merge($msg, $temp_result['msg']);
                            }                            
                            
                            if(Tools::_float($outstanding_qty ) <= Tools::_float(0)) break;
                            if($success !== 1) break;
                        }
                        if(Tools::_float($outstanding_qty ) <= Tools::_float(0)) break;
                        if($success !== 1) break;
                    }
                    
                }
                //</editor-fold>
                
                if($success !== 1) break;
                
            }
        }
        //</editor-fold>
        
        //<editor-fold defaultstate="collapsed" desc="Add Customer Credit">
        if($success === 1){
            $param = array(
                'customer'=>array(
                    'customer_id'=>$fsales_invoice['customer_id'],
                    'customer_credit_amount'=>$fsales_invoice['grand_total_amount'],
                    'description'=>'Sales Invoice: <a href="{base_url}/sales_invoice/view/'.$sales_invoice_id.'" target="_blank">'.$fsales_invoice['code'].'</a>'
                        .' '.SI::type_get('sales_invoice_engine', $fsales_invoice['sales_invoice_status'],'$status_list')['text']
                ),
                'customer_amount_log'=>array(
                    'ref_type'=>'sales_invoice',
                    'ref_id'=>$sales_invoice_id
                ),
            );
            
            $temp_result = Customer_Engine::customer_debit_credit_amount_add($db, $param, 'credit');
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

    public function sales_invoice_invoiced($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        get_instance()->load->helper($path->sales_invoice_data_support);
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsales_invoice = $final_data['sales_invoice'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_invoice_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('sales_invoice', $fsales_invoice, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'sales_invoice', $sales_invoice_id, $fsales_invoice['sales_invoice_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function sales_invoice_canceled($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Invoice_Engine::path_get();
        get_instance()->load->helper($path->sales_invoice_data_support);
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_engine'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_data_support'));
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_engine'));
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_engine'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsales_invoice = $final_data['sales_invoice'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_invoice_id = $id;

        $result['trans_id'] = $id;
        
        $temp = Sales_Invoice_Data_Support::sales_invoice_get($id);
        $sales_invoice_db = $temp['sales_invoice'];
        $si_product_db = $temp['si_product'];
        $si_product_mov_qty = $temp['si_product_mov_qty'];
        
        $temp_result = self::sales_invoice_invoiced($db, $final_data, $id);
        if($temp_result['success'] !== 1){
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        if($success === 1){
            foreach($si_product_db as $idx=>$row){
                
                //<editor-fold defaultstate="collapsed" desc="Product Movement Outstanding">
                $t_param = array(
                    'si_product'=>array(
                        'outstanding_movement_qty'=>$row['qty']
                    )
                );
                $temp_result = self::si_product_outstanding_movement_qty_add($db, $t_param, $row['id']);
                if($temp_result['success']!== 1){
                    $success = 0;
                    $msg = array_merge($msg, $temp_result['msg']);
                }
                //</editor-fold>
                
                if($success !== 1) break;
            }
        }
        
        //<editor-fold defaultstate="SI Product Mov Qty">
        if($success === 1){            
            foreach($si_product_mov_qty as $idx=>$row){
                if($row['ref_type'] === 'product_stock_good'){
                    $psg = Product_Stock_Data_Support::product_stock_get('stock_good',$row['ref_id'])['product_stock_good'];
                    
                    if($success === 1){
                        $temp_result = Product_Stock_Engine::stock_good_add(
                            $db,
                            'si_product_mov_qty',
                            $row['id'],
                            $psg['warehouse_id'],
                            $psg['product_batch_id'],
                            $row['qty'],
                            'Sales Invoice: <a target="_blank" href="{base_url}/sales_invoice/view/'.$sales_invoice_id.'">'.$sales_invoice_db['code'].'</a>'
                                .' '.SI::type_get('sales_invoice_engine', 
                                $fsales_invoice['sales_invoice_status'],'$status_list'
                            )['text'],
                            $moddate

                        );
                        
                        if($temp_result['success']!== 1){
                            $success = $temp_result['success'];
                            $msg = array_merge($msg, $temp_result['msg']);
                        }
                        
                    }
                    
                    if($success !== 1) break;
                }
            }
            
        }
        //</editor-fold>
        
        //<editor-fold defaultstate="collapsed" desc="Add Customer Credit">
        if($success === 1){
            $param = array(
                'customer'=>array(
                    'customer_id'=>$sales_invoice_db['customer_id'],
                    'customer_credit_amount'=> -1 * Tools::_float($sales_invoice_db['grand_total_amount']),
                    'description'=>'Sales Invoice: <a href="{base_url}/sales_invoice/view/'.$sales_invoice_id.'" target="_blank">'.$sales_invoice_db['code'].'</a>'
                        .' '.SI::type_get('sales_invoice_engine', $fsales_invoice['sales_invoice_status'],'$status_list')['text']
                ),
                'customer_amount_log'=>array(
                    'ref_type'=>'sales_invoice',
                    'ref_id'=>$sales_invoice_id
                ),
            );
            
            $temp_result = Customer_Engine::customer_debit_credit_amount_add($db, $param, 'credit');
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

    public static function sales_invoice_outstanding_grand_total_amount_add($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
        
        $fsales_invoice = $final_data['sales_invoice'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_invoice_id = $id;

        $temp = Sales_Invoice_Data_Support::sales_invoice_get($id);
        $sales_invoice_db = $temp['sales_invoice'];
        
        $result['trans_id'] = $id;
        
        $q = '
            update sales_invoice 
            set outstanding_grand_total_amount = outstanding_grand_total_amount+'.$db->escape($fsales_invoice['outstanding_grand_total_amount']).'
                ,modid = '.$db->escape($modid).'
                ,moddate = '.$db->escape($moddate).'
            where id = '.$db->escape($sales_invoice_id).'
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
    
    public static function si_product_outstanding_movement_qty_add($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
        
        $fsi_product = $final_data['si_product'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_invoice_id = $id;
        
        $result['trans_id'] = $id;
        
        $q = '
            update si_product 
            set outstanding_movement_qty = outstanding_movement_qty+'.$db->escape($fsi_product['outstanding_movement_qty']).'
            where id = '.$db->escape($sales_invoice_id).'
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
