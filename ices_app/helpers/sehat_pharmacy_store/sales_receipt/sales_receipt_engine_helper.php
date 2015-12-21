<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Sales_Receipt_Engine {

    public static $prefix_id = 'sales_receipt';
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
                , 'method' => 'sales_receipt_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Sales Receipt'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'invoiced'
                , 'text' => 'INVOICED'
                , 'method' => 'sales_receipt_invoiced'
                , 'next_allowed_status' => array('X')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Sales Receipt'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'X'
                , 'text' => 'CANCELED'
                , 'method' => 'sales_receipt_canceled'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Cancel')
                        , array('val' => Lang::get(array('Sales Receipt'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'sales_receipt/'
            , 'sales_receipt_engine' => ICES_Engine::$app['app_base_dir'] . 'sales_receipt/sales_receipt_engine'
            , 'sales_receipt_data_support' => ICES_Engine::$app['app_base_dir'] . 'sales_receipt/sales_receipt_data_support'
            , 'sales_receipt_renderer' => ICES_Engine::$app['app_base_dir'] . 'sales_receipt/sales_receipt_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'sales_receipt/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'sales_receipt/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Sales_Receipt_Engine::path_get();
        get_instance()->load->helper($path->sales_receipt_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $sales_receipt = isset($data['sales_receipt']) ? Tools::_arr($data['sales_receipt']) : array();
        $sales_receipt_id = $sales_receipt['id'];
        $temp = Sales_Receipt_Data_Support::sales_receipt_get($sales_receipt_id);
        $sales_receipt_db = isset($temp['sales_receipt'])?$temp['sales_receipt']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
                SI::module()->load_class(array('module'=>'payment_type','class_name'=>'payment_type_data_support'));
                SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
                SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
                
                if (!(isset($sales_receipt['store_id']) 
                        && isset($sales_receipt['ref_type']) 
                        && isset($sales_receipt['ref_id']) 
                        && isset($sales_receipt['payment_type_id'])
                        && isset($sales_receipt['bos_bank_account_id'])
                        && isset($sales_receipt['customer_bank_account'])
                        && isset($sales_receipt['amount'])
                        && isset($sales_receipt['change_amount'])
                        && isset($sales_receipt['sales_receipt_status'])
                        && isset($sales_receipt['notes'])
                        
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Sales Receipt')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_id = Tools::empty_to_null(Tools::_str($sales_receipt['store_id']));
                    $ref_type = Tools::empty_to_null(Tools::_str($sales_receipt['ref_type']));
                    $ref_id = Tools::empty_to_null(Tools::_str($sales_receipt['ref_id']));
                    $payment_type_id = Tools::empty_to_null(Tools::_str($sales_receipt['payment_type_id']));
                    $bos_bank_account_id = Tools::empty_to_null(Tools::_str($sales_receipt['bos_bank_account_id']));
                    $customer_bank_account = Tools::empty_to_null(Tools::_str($sales_receipt['customer_bank_account']));
                    $amount = Tools::empty_to_null(Tools::_str($sales_receipt['amount']));
                    $change_amount = Tools::empty_to_null(Tools::_str($sales_receipt['change_amount']));

                    //<editor-fold defaultstate="collapsed" desc="Major Validation">
                    if (is_null($store_id) 
                        || is_null($ref_type)
                        || is_null($ref_id)
                        || is_null($payment_type_id)
                        || Tools::_float($amount) <= Tools::_float('0')
                        
                    ) {
                        $success = 0;
                        $msg[] = Lang::get('Store')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Reference')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Payment Type')
                                . ' ' . Lang::get('or', true, false) . ' ' . Lang::get('Amount')
                                . ' ' . Lang::get('empty', true, false);
                    }
                    if ($success !== 1)
                        break;

                    //</editor-fold>
                    
                    switch($ref_type){
                        case 'sales_invoice':
                            //<editor-fold defaultstate="collapsed">
                            $temp = Sales_Invoice_Data_Support::sales_invoice_get($ref_id);
                            $local_success = 1;
                            if(!count($temp)>0){
                                $local_success = 0;
                                
                            }
                            else{
                                $sales_invoice = $temp['sales_invoice'];
                                if($sales_invoice['sales_invoice_status']!=='invoiced'
                                ){                                    
                                    $local_success = 0;
                                }
                            }
                            
                            if($local_success !== 1){
                                $success = 0;
                                $msg[] = Lang::get('Sales Invoice')
                                    .' '.Lang::get('invalid',true,false);
                            }
                            
                            if(Tools::_float($sales_invoice['outstanding_grand_total_amount']) 
                                        <= Tools::_float('0')
                                    || (Tools::_float($amount) - Tools::_float($change_amount))
                                        > Tools::_float($sales_invoice['outstanding_grand_total_amount'])
                            ){
                                $success = 0;
                                $msg[] = Lang::get('Amount')
                                    .' '.Lang::get('invalid',true,false)
                                ;
                            }
                            //</editor-fold>
                            break;
                        default:
                            $success = 0;
                            $msg[] = Lang::get('Reference')
                                .' '.Lang::get('invalid',true,false);
                            break;
                    }
                    
                    $temp_result = Store_Data_Support::store_validate($store_id);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    $temp_result = Payment_Type_Data_Support::payment_type_validate($sales_receipt);
                    if($temp_result['success']!== 1){
                        $success = $temp_result['success'];
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                    if(!is_null($bos_bank_account_id)){
                        $temp_result = BOS_Bank_Account_Data_Support::bos_bank_account_validate($bos_bank_account_id);
                        if($temp_result['success']!== 1){
                            $success = 0;
                            $msg = array_merge($msg, $temp_result['msg']);
                        }                        
                    }
                                        
                    if(!((Tools::_float($amount) - Tools::_float($change_amount))>=Tools::_float('0'))){
                        $success = 0;
                        $msg[] = Lang::get('Change Amount')
                            .' '.Lang::get('invalid',true,false)
                        ;
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
                            'module' => 'sales_receipt',
                            'module_name' => Lang::get('Sales Receipt'),
                            'module_engine' => 'sales_receipt_engine',
                        ), 
                        $sales_receipt
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
                if (!count($sales_receipt_db) > 0) {
                    $success = 0;
                    $msg[] = 'Invalid Sales Receipt';
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_cancel(
                                    array(
                                'module' => 'sales_receipt',
                                'module_name' => Lang::get('Sales Receipt'),
                                'module_engine' => 'sales_receipt_engine',
                                    ), $sales_receipt
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
        $db = new DB();
        $result = array();

        $sales_receipt_data = isset($data['sales_receipt']) ? $data['sales_receipt'] : array();

        $temp_sales_receipt = Sales_Receipt_Data_Support::sales_receipt_get($sales_receipt_data['id']);
        $sales_receipt_db = isset($temp_sales_receipt['sales_receipt'])?$temp_sales_receipt['sales_receipt']:array();
        
        $sales_receipt_id = $sales_receipt_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                $sales_receipt = array(
                    'store_id' => Tools::_str($sales_receipt_data['store_id']),
                    'ref_type' => Tools::_str($sales_receipt_data['ref_type']),
                    'ref_id' => Tools::_str($sales_receipt_data['ref_id']),
                    'payment_type_id' => Tools::_str($sales_receipt_data['payment_type_id']),
                    'sales_receipt_date'=>Tools::_date(),
                    'bos_bank_account_id'=>Tools::empty_to_null($sales_receipt_data['bos_bank_account_id']),
                    'customer_bank_account'=>Tools::empty_to_null($sales_receipt_data['customer_bank_account']),
                    'amount'=>Tools::_float($sales_receipt_data['amount']),
                    'change_amount'=>Tools::_float($sales_receipt_data['change_amount']),
                    'sales_receipt_status'=>'invoiced',
                    'notes' => Tools::empty_to_null(Tools::_str($sales_receipt_data['notes'])),                    
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                
                $result['sales_receipt'] = $sales_receipt;
                //</editor-fold>
                break;
            case self::$prefix_method . '_invoiced':
                //<editor-fold defaultstate="collapsed">
                $sales_receipt = array(
                    'notes' => Tools::empty_to_null(Tools::_str($sales_receipt_data['notes'])),
                    'sales_receipt_status'=>'invoiced',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['sales_receipt'] = $sales_receipt;
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                $sales_receipt = array(
                    'sales_receipt_status'=>'X',
                    'cancellation_reason'=>Tools::_str($sales_receipt_data['cancellation_reason']),
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $result['sales_receipt'] = $sales_receipt;
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public function sales_receipt_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_engine'));
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsales_receipt = $final_data['sales_receipt'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        
        $sales_receipt_id = '';
        $ref_type = $fsales_receipt['ref_type'];
        $ref_id = $fsales_receipt['ref_id'];

        $fsales_receipt['code'] = SI::code_counter_store_get($db, 
            $fsales_receipt['store_id'],
            'sales_receipt'
        );
        
        if (!$db->insert('sales_receipt', $fsales_receipt)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            $sales_receipt_id = $db->last_insert_id();
            $result['trans_id'] = $sales_receipt_id;
            if(is_null($sales_receipt_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
            
        }

        if ($success === 1) {
            $temp_result = SI::status_log_add($db, 'sales_receipt', $sales_receipt_id, $fsales_receipt['sales_receipt_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        switch($ref_type){
            case 'sales_invoice':
                //<editor-fold defaultstate="collapsed">
                $temp = Sales_Invoice_Data_Support::sales_invoice_get($ref_id);
                $sales_invoice = $temp['sales_invoice'];
                $pure_amount = Tools::_float($fsales_receipt['amount']) - Tools::_float($fsales_receipt['change_amount']);
                //<editor-fold defaultstate="collapsed" desc="Sales Invoice Outstanding Amount Add">
                if($success === 1){
                    $param = array(
                        'sales_invoice'=>array(
                            'outstanding_grand_total_amount'=> -1*Tools::_float($pure_amount),
                        )
                    );
                    $temp_result = Sales_Invoice_Engine::sales_invoice_outstanding_grand_total_amount_add($db,$param,$sales_invoice['id']);
                    if($temp_result['success']!== 1){
                        $success = 0;
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>

                //<editor-fold defaultstate="collapsed" desc="Add Customer Debit">
                if($success === 1){
                    
                    $param = array(
                        'customer'=>array(
                            'customer_id'=>$sales_invoice['customer_id'],
                            'customer_credit_amount'=>-1 * Tools::_float($pure_amount),
                            'description'=>'Sales Receipt: <a href="{base_url}/sales_receipt/view/'.$sales_receipt_id.'" target="_blank">'.$fsales_receipt['code'].'</a>'
                                .' '.SI::type_get('sales_receipt_engine', $fsales_receipt['sales_receipt_status'],'$status_list')['text']
                        ),
                        'customer_amount_log'=>array(
                            'ref_type'=>'sales_receipt',
                            'ref_id'=>$sales_receipt_id
                        ),
                    );

                    $temp_result = Customer_Engine::customer_debit_credit_amount_add($db, $param, 'credit');
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

    public function sales_receipt_invoiced($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsales_receipt = $final_data['sales_receipt'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_receipt_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('sales_receipt', $fsales_receipt, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'sales_receipt', $sales_receipt_id, $fsales_receipt['sales_receipt_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function sales_receipt_canceled($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_engine'));
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        SI::module()->load_class(array('module'=>'customer','class_name'=>'customer_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fsales_receipt = $final_data['sales_receipt'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_receipt_id = $id;

        $result['trans_id'] = $id;
        
        $temp = Sales_Receipt_Data_Support::sales_receipt_get($id);
        $sales_receipt_db = $temp['sales_receipt'];
        
        $ref_type = $sales_receipt_db['ref_type'];
        $ref_id = $sales_receipt_db['ref_id'];
        
        $temp_result = self::sales_receipt_invoiced($db, $final_data, $id);
        if($temp_result['success'] !== 1){
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        switch($ref_type){
            case 'sales_invoice':
                //<editor-fold defaultstate="collapsed">
                $temp = Sales_Invoice_Data_Support::sales_invoice_get($ref_id);
                $sales_invoice = $temp['sales_invoice'];
                $pure_amount = Tools::_float($sales_receipt_db['amount']) - Tools::_float($sales_receipt_db['change_amount']);
                //<editor-fold defaultstate="collapsed" desc="Sales Invoice Outstanding Amount Add">
                if($success === 1){
                    $param = array(
                        'sales_invoice'=>array(
                            'outstanding_grand_total_amount'=> Tools::_float($pure_amount),
                        )
                    );
                    $temp_result = Sales_Invoice_Engine::sales_invoice_outstanding_grand_total_amount_add($db,$param,$sales_invoice['id']);
                    if($temp_result['success']!== 1){
                        $success = 0;
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                }
                //</editor-fold>

                //<editor-fold defaultstate="collapsed" desc="Add Customer Debit">
                if($success === 1){
                    
                    $param = array(
                        'customer'=>array(
                            'customer_id'=>$sales_invoice['customer_id'],
                            'customer_credit_amount'=>Tools::_float($pure_amount),
                            'description'=>'Sales Receipt: <a href="{base_url}/sales_receipt/view/'.$sales_receipt_id.'" target="_blank">'.$sales_receipt_db['code'].'</a>'
                                .' '.SI::type_get('sales_receipt_engine', $fsales_receipt['sales_receipt_status'],'$status_list')['text']
                        ),
                        'customer_amount_log'=>array(
                            'ref_type'=>'sales_receipt',
                            'ref_id'=>$sales_receipt_id
                        ),
                    );

                    $temp_result = Customer_Engine::customer_debit_credit_amount_add($db, $param, 'credit');
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

    public function sales_receipt_outstanding_amount_add($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_receipt','class_name'=>'sales_receipt_data_support'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
        
        $fsales_receipt = $final_data['sales_receipt'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $sales_receipt_id = $id;

        $result['trans_id'] = $id;
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}

?>
