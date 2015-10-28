<?php

class Purchase_Receipt_Data_Support {

    public static function purchase_receipt_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select pr.*
                ,st.code store_code
                ,st.name store_name
            from purchase_receipt pr
            inner join store st on pr.store_id = st.id
            where pr.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $purchase_receipt = $rs[0];
            $result['purchase_receipt'] = $purchase_receipt;
        }
        return $result;
        //</editor-fold>
    }

    public static function input_select_reference_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        $param = array(
            'lookup_data'=>$lookup_data,
            'q_condition'=>'and pi.outstanding_grand_total_amount > 0 and pi.purchase_invoice_status = "invoiced"'
        );
        $temp = Purchase_Invoice_Data_Support::input_select_purchase_invoice_search($param);
        $result = $temp;
        return $result;
        //</editor-fold>
    }
    
    public static function reference_dependency_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $ref_type = $param['ref_type'];
        $ref_id = $param['ref_id'];
        $reference_detail = array();
        $outstanding_grand_total_amount = Tools::_float('0');
        
        switch($ref_type){
            case 'purchase_invoice':
                SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
                $t_purchase_invoice = Purchase_Invoice_Data_Support::purchase_invoice_get($ref_id);
                if(count($t_purchase_invoice)>0){
                    $purchase_invoice = $t_purchase_invoice['purchase_invoice'];
                    $reference_detail = Purchase_Invoice_Data_Support::input_select_purchase_invoice_detail_get(array('purchase_invoice_id'=>$ref_id));
                    $outstanding_grand_total_amount = Tools::_float($purchase_invoice['outstanding_grand_total_amount']);
                }
                break;
        }
        
        
        $result['outstanding_grand_total_amount'] = $outstanding_grand_total_amount;
        $result['reference_detail'] = $reference_detail;
        return $result;
        //</editor-fold>
    }
    
}

?>