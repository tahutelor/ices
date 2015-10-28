<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Purchase_Receipt_Engine {

    public static $prefix_id = 'purchase_receipt';
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
                , 'method' => 'purchase_receipt_add'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Add')
                        , array('val' => Lang::get(array('Purchase Receipt'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'invoiced'
                , 'text' => 'INVOICED'
                , 'method' => 'purchase_receipt_invoiced'
                , 'next_allowed_status' => array('X')
                , 'default' => true
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Update')
                        , array('val' => Lang::get(array('Purchase Receipt'), true, true, false, false, true))
                        , array('val' => 'success','lower_all'=>true)
                    )
                )
            ),
            array(
                'val' => 'X'
                , 'text' => 'CANCELED'
                , 'method' => 'purchase_receipt_canceled'
                , 'next_allowed_status' => array()
                , 'msg' => array(
                    'success' => array(
                        array('val' => 'Cancel')
                        , array('val' => Lang::get(array('Purchase Receipt'), true, true, false, false, true))
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
            'index' => ICES_Engine::$app['app_base_url'] . 'purchase_receipt/'
            , 'purchase_receipt_engine' => ICES_Engine::$app['app_base_dir'] . 'purchase_receipt/purchase_receipt_engine'
            , 'purchase_receipt_data_support' => ICES_Engine::$app['app_base_dir'] . 'purchase_receipt/purchase_receipt_data_support'
            , 'purchase_receipt_renderer' => ICES_Engine::$app['app_base_dir'] . 'purchase_receipt/purchase_receipt_renderer'
            , 'ajax_search' => ICES_Engine::$app['app_base_url'] . 'purchase_receipt/ajax_search/'
            , 'data_support' => ICES_Engine::$app['app_base_url'] . 'purchase_receipt/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function validate($method, $data = array()) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Receipt_Engine::path_get();
        get_instance()->load->helper($path->purchase_receipt_data_support);
                
        $result = SI::result_format_get();

        $success = 1;
        $msg = array();

        $purchase_receipt = isset($data['purchase_receipt']) ? Tools::_arr($data['purchase_receipt']) : array();
        $purchase_receipt_id = $purchase_receipt['id'];
        $temp = Purchase_Receipt_Data_Support::purchase_receipt_get($purchase_receipt_id);
        $purchase_receipt_db = isset($temp['purchase_receipt'])?$temp['purchase_receipt']:array();
        
        $db = new DB();
        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
                SI::module()->load_class(array('module'=>'payment_type','class_name'=>'payment_type_data_support'));
                SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
                SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
                
                if (!(isset($purchase_receipt['store_id']) 
                        && isset($purchase_receipt['ref_type']) 
                        && isset($purchase_receipt['ref_id']) 
                        && isset($purchase_receipt['payment_type_id'])
                        && isset($purchase_receipt['bos_bank_account_id'])
                        && isset($purchase_receipt['supplier_bank_account'])
                        && isset($purchase_receipt['amount'])
                        && isset($purchase_receipt['change_amount'])
                        && isset($purchase_receipt['purchase_receipt_status'])
                        && isset($purchase_receipt['notes'])
                        
                    )) {
                    $success = 0;
                    $msg[] = Lang::get('Purchase Receipt')
                            . ' ' . Lang::get('parameter', true, false)
                            . ' ' . Lang::get('invalid', true, false);
                }
                if ($success === 1) {

                    $store_id = Tools::empty_to_null(Tools::_str($purchase_receipt['store_id']));
                    $ref_type = Tools::empty_to_null(Tools::_str($purchase_receipt['ref_type']));
                    $ref_id = Tools::empty_to_null(Tools::_str($purchase_receipt['ref_id']));
                    $payment_type_id = Tools::empty_to_null(Tools::_str($purchase_receipt['payment_type_id']));
                    $bos_bank_account_id = Tools::empty_to_null(Tools::_str($purchase_receipt['bos_bank_account_id']));
                    $supplier_bank_account = Tools::empty_to_null(Tools::_str($purchase_receipt['supplier_bank_account']));
                    $amount = Tools::empty_to_null(Tools::_str($purchase_receipt['amount']));
                    $change_amount = Tools::empty_to_null(Tools::_str($purchase_receipt['change_amount']));

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
                        case 'purchase_invoice':
                            //<editor-fold defaultstate="collapsed">
                            $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($ref_id);
                            $local_success = 1;
                            if(!count($temp)>0){
                                $local_success = 0;
                                
                            }
                            else{
                                $purchase_invoice = $temp['purchase_invoice'];
                                if($purchase_invoice['purchase_invoice_status']!=='invoiced'
                                ){                                    
                                    $local_success = 0;
                                }
                            }
                            
                            if($local_success !== 1){
                                $success = 0;
                                $msg[] = Lang::get('Purchase Invoice')
                                    .' '.Lang::get('invalid',true,false);
                            }
                            
                            if(Tools::_float($purchase_invoice['outstanding_grand_total_amount']) 
                                        <= Tools::_float('0')
                                    || (Tools::_float($amount) - Tools::_float($change_amount))
                                        > Tools::_float($purchase_invoice['outstanding_grand_total_amount'])
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
                    
                    $temp_result = Payment_Type_Data_Support::payment_type_validate($purchase_receipt);
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
                if (!count($purchase_receipt_db) > 0) {
                    $success = 0;
                    $msg[] = 'Purchase Receipt'
                        .' '.Lang::get('invalid',true,false);
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_update(
                        array(
                            'module' => 'purchase_receipt',
                            'module_name' => Lang::get('Purchase Receipt'),
                            'module_engine' => 'purchase_receipt_engine',
                        ), 
                        $purchase_receipt
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
                if (!count($purchase_receipt_db) > 0) {
                    $success = 0;
                    $msg[] = 'Invalid Purchase Receipt';
                }

                if ($success === 1) {
                    $temp_result = SI::data_validator()->validate_on_cancel(
                                    array(
                                'module' => 'purchase_receipt',
                                'module_name' => Lang::get('Purchase Receipt'),
                                'module_engine' => 'purchase_receipt_engine',
                                    ), $purchase_receipt
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

        $purchase_receipt_data = isset($data['purchase_receipt']) ? $data['purchase_receipt'] : array();

        $temp_purchase_receipt = Purchase_Receipt_Data_Support::purchase_receipt_get($purchase_receipt_data['id']);
        $purchase_receipt_db = isset($temp_purchase_receipt['purchase_receipt'])?$temp_purchase_receipt['purchase_receipt']:array();
        
        $purchase_receipt_id = $purchase_receipt_data['id'];
        $modid = User_Info::get()['user_id'];
        $datetime_curr = Date('Y-m-d H:i:s');

        switch ($method) {
            case self::$prefix_method . '_add':
                //<editor-fold defaultstate="collapsed">
                $purchase_receipt = array(
                    'store_id' => Tools::_str($purchase_receipt_data['store_id']),
                    'ref_type' => Tools::_str($purchase_receipt_data['ref_type']),
                    'ref_id' => Tools::_str($purchase_receipt_data['ref_id']),
                    'payment_type_id' => Tools::_str($purchase_receipt_data['payment_type_id']),
                    'purchase_receipt_date'=>Tools::_date(),
                    'bos_bank_account_id'=>Tools::empty_to_null($purchase_receipt_data['bos_bank_account_id']),
                    'supplier_bank_account'=>Tools::empty_to_null($purchase_receipt_data['supplier_bank_account']),
                    'amount'=>Tools::_float($purchase_receipt_data['amount']),
                    'change_amount'=>Tools::_float($purchase_receipt_data['change_amount']),
                    'purchase_receipt_status'=>'invoiced',
                    'notes' => Tools::empty_to_null(Tools::_str($purchase_receipt_data['notes'])),                    
                    'status' => 1,
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                
                $result['purchase_receipt'] = $purchase_receipt;
                //</editor-fold>
                break;
            case self::$prefix_method . '_invoiced':
                //<editor-fold defaultstate="collapsed">
                $purchase_receipt = array(
                    'notes' => Tools::empty_to_null(Tools::_str($purchase_receipt_data['notes'])),
                    'purchase_receipt_status'=>'invoiced',
                    'modid' => $modid,                    
                    'moddate' => $datetime_curr,
                );
                $result['purchase_receipt'] = $purchase_receipt;
                //</editor-fold>
                break;
            case self::$prefix_method . '_canceled':
                //<editor-fold defaultstate="collapsed">
                $purchase_receipt = array(
                    'purchase_receipt_status'=>'X',
                    'cancellation_reason'=>Tools::_str($purchase_receipt_data['cancellation_reason']),
                    'modid' => $modid,
                    'moddate' => $datetime_curr,
                );
                $result['purchase_receipt'] = $purchase_receipt;
                //</editor-fold>
                break;
        }

        return $result;
        //</editor-fold>
    }
    
    public function purchase_receipt_add($db, $final_data, $id = '') {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_receipt = $final_data['purchase_receipt'];

        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        
        $purchase_receipt_id = '';
        $ref_type = $fpurchase_receipt['ref_type'];
        $ref_id = $fpurchase_receipt['ref_id'];

        $fpurchase_receipt['code'] = SI::code_counter_store_get($db, 
            $fpurchase_receipt['store_id'],
            'purchase_receipt'
        );
        
        if (!$db->insert('purchase_receipt', $fpurchase_receipt)) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success === 1) {
            $purchase_receipt_id = $db->last_insert_id();
            $result['trans_id'] = $purchase_receipt_id;
            if(is_null($purchase_receipt_id)){
                $msg[] = $db->_error_message();
                $db->trans_rollback();
                $success = 0;
            }
            
        }

        if ($success === 1) {
            $temp_result = SI::status_log_add($db, 'purchase_receipt', $purchase_receipt_id, $fpurchase_receipt['purchase_receipt_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }

        switch($ref_type){
            case 'purchase_invoice':
                //<editor-fold defaultstate="collapsed">
                $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($ref_id);
                $purchase_invoice = $temp['purchase_invoice'];
                $pure_amount = Tools::_float($fpurchase_receipt['amount']) - Tools::_float($fpurchase_receipt['change_amount']);
                //<editor-fold defaultstate="collapsed" desc="Purchase Invoice Outstanding Amount Add">
                if($success === 1){
                    $param = array(
                        'purchase_invoice'=>array(
                            'outstanding_grand_total_amount'=> -1*Tools::_float($pure_amount),
                        )
                    );
                    $temp_result = Purchase_Invoice_Engine::purchase_invoice_outstanding_grand_total_amount_add($db,$param,$purchase_invoice['id']);
                    if($temp_result['success']!== 1){
                        $success = 0;
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                }
                //</editor-fold>

                //<editor-fold defaultstate="collapsed" desc="Add Supplier Debit">
                if($success === 1){
                    
                    $param = array(
                        'supplier'=>array(
                            'supplier_id'=>$purchase_invoice['supplier_id'],
                            'supplier_debit_amount'=>-1 * Tools::_float($pure_amount),
                            'description'=>'Purchase Receipt: <a href="{base_url}/purchase_receipt/view/'.$purchase_receipt_id.'" target="_blank">'.$fpurchase_receipt['code'].'</a>'
                                .' '.SI::type_get('purchase_receipt_engine', $fpurchase_receipt['purchase_receipt_status'],'$status_list')['text']
                        ),
                        'supplier_amount_log'=>array(
                            'ref_type'=>'purchase_receipt',
                            'ref_id'=>$purchase_receipt_id
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

    public function purchase_receipt_invoiced($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_receipt = $final_data['purchase_receipt'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_receipt_id = $id;

        $result['trans_id'] = $id;

        if (!$db->update('purchase_receipt', $fpurchase_receipt, array('id' => $id))) {
            $msg[] = $db->_error_message();
            $db->trans_rollback();
            $success = 0;
        }

        if ($success == 1) {
            $temp_result = SI::status_log_add($db, 'purchase_receipt', $purchase_receipt_id, $fpurchase_receipt['purchase_receipt_status']);
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }

    public function purchase_receipt_canceled($db, $final_data, $id) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();

        $fpurchase_receipt = $final_data['purchase_receipt'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_receipt_id = $id;

        $result['trans_id'] = $id;
        
        $temp = Purchase_Receipt_Data_Support::purchase_receipt_get($id);
        $purchase_receipt_db = $temp['purchase_receipt'];
        
        $ref_type = $purchase_receipt_db['ref_type'];
        $ref_id = $purchase_receipt_db['ref_id'];
        
        $temp_result = self::purchase_receipt_invoiced($db, $final_data, $id);
        if($temp_result['success'] !== 1){
            $success = $temp_result['success'];
            $msg = array_merge($msg, $temp_result['msg']);
        }
        
        switch($ref_type){
            case 'purchase_invoice':
                //<editor-fold defaultstate="collapsed">
                $temp = Purchase_Invoice_Data_Support::purchase_invoice_get($ref_id);
                $purchase_invoice = $temp['purchase_invoice'];
                $pure_amount = Tools::_float($purchase_receipt_db['amount']) - Tools::_float($purchase_receipt_db['change_amount']);
                //<editor-fold defaultstate="collapsed" desc="Purchase Invoice Outstanding Amount Add">
                if($success === 1){
                    $param = array(
                        'purchase_invoice'=>array(
                            'outstanding_grand_total_amount'=> Tools::_float($pure_amount),
                        )
                    );
                    $temp_result = Purchase_Invoice_Engine::purchase_invoice_outstanding_grand_total_amount_add($db,$param,$purchase_invoice['id']);
                    if($temp_result['success']!== 1){
                        $success = 0;
                        $msg = array_merge($msg, $temp_result['msg']);
                    }
                    
                }
                //</editor-fold>

                //<editor-fold defaultstate="collapsed" desc="Add Supplier Debit">
                if($success === 1){
                    
                    $param = array(
                        'supplier'=>array(
                            'supplier_id'=>$purchase_invoice['supplier_id'],
                            'supplier_debit_amount'=>Tools::_float($pure_amount),
                            'description'=>'Purchase Receipt: <a href="{base_url}/purchase_receipt/view/'.$purchase_receipt_id.'" target="_blank">'.$purchase_receipt_db['code'].'</a>'
                                .' '.SI::type_get('purchase_receipt_engine', $fpurchase_receipt['purchase_receipt_status'],'$status_list')['text']
                        ),
                        'supplier_amount_log'=>array(
                            'ref_type'=>'purchase_receipt',
                            'ref_id'=>$purchase_receipt_id
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

    public function purchase_receipt_outstanding_amount_add($db, $final_data, $id){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_receipt','class_name'=>'purchase_receipt_data_support'));
        $result = DB::result_format_get();
        $success = 1;
        $msg = array();
        
        $fpurchase_receipt = $final_data['purchase_receipt'];
        
        $modid = User_Info::get()['user_id'];
        $moddate = Date('Y-m-d H:i:s');
        $purchase_receipt_id = $id;

        $result['trans_id'] = $id;
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}

?>
