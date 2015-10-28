<?php

class Payment_Type_Data_Support {

    public static function payment_type_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select distinct s.*
                ,bba.code bos_bank_account_code
                ,bba.bank_account bos_bank_account
            from payment_type s   
                left outer join bos_bank_account bba on s.bos_bank_account_id_default = bba.id
            where s.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $payment_type = $rs[0];
            

            $result['payment_type'] = $payment_type;
        }
        return $result;
        //</editor-fold>
    }

    public static function payment_type_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_payment_type_status = isset($param['payment_type_status'])?
            ' and u.payment_type_status = '.$db->escape($param['payment_type_status']):'';
        $q = '
            select u.*
            from payment_type u
            where u.status>0
            
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            foreach($rs as $idx=>$row){
            
                $result[] = self::payment_type_get($row['id']);
            
            }
        }
        return $result;
        //</editor-fold>
    }
    
    public static function bos_bank_account_list_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
        $t_bos_bank_account_list = BOS_Bank_ACcount_Data_Support::bos_bank_account_list_get(array('bos_bank_account_status'=>'active'));
        $result = $t_bos_bank_account_list;
        return $result;
        //</editor-fold>
    }

    public static function input_select_payment_type_list_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $t_payment_type_list = Payment_Type_Data_Support::payment_type_list_get(array('payment_type_status'=>'active'));
        foreach($t_payment_type_list as $idx=>$row){
            $t_payment_type = $row['payment_type'];
            $t_payment_type['text']=Tools::html_tag('strong',$row['payment_type']['code'])
                    .' '.$row['payment_type']['name']
            ;
            $result[] = $t_payment_type;
        }

        if(count($t_payment_type_list)>0){
            $result[0]['default'] = true;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function payment_type_validate($receipt_data = array()){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        $payment_type_id = Tools::_str(isset($receipt_data['payment_type_id'])?
            $receipt_data['payment_type_id']:''
        );
        
        $temp = self::payment_type_get($payment_type_id);
        if(!count($temp)>0){
            $success = 0;
            
        }
        else{
            $payment_type = $temp['payment_type'];
            $bos_bank_account_id = Tools::empty_to_null(isset($receipt_data['bos_bank_account_id'])?
                $receipt_data['bos_bank_account_id']:''
            );            
            $change_amount = Tools::_float(isset($receipt_data['change_amount'])?
                $receipt_data['change_amount']:''
            );            
            $supplier_bank_account = Tools::empty_to_null(isset($receipt_data['supplier_bank_account'])?
                $receipt_data['supplier_bank_account']:''
            );            
            $customer_bank_account = Tools::empty_to_null(isset($receipt_data['customer_bank_account'])?
                $receipt_data['customer_bank_account']:''
            );
            
            if($payment_type['payment_type_status']!=='active'){
                $success = 0;                
            }
            if(!Tools::_bool($payment_type['change_amount']) && Tools::_float($change_amount)>Tools::_float('0')){
                $success = 0;
            }
            
            if(is_null($customer_bank_account) 
                &&(
                    (Tools::_bool($payment_type['supplier_bank_account']) && is_null($supplier_bank_account))
                    || (!Tools::_bool($payment_type['supplier_bank_account']) && !is_null($supplier_bank_account))
                )
            ){
                $success = 0;
            }
            else if(is_null($supplier_bank_account) 
                &&(
                    (Tools::_bool($payment_type['customer_bank_account']) && is_null($customer_bank_account))
                    || (!Tools::_bool($payment_type['customer_bank_account']) && !is_null($customer_bank_account))
                )
            ){                
                $success = 0;
            }
            
            
            if(!is_null(Tools::empty_to_null($payment_type['bos_bank_account_id_default'])) && is_null($bos_bank_account_id)){
                $success = 0;
                
            }
            
        }
        
        if($success !== 1){
            $msg[] = Lang::get('Payment Type')
                .' '.Lang::get('invalid',true,false);
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}

?>