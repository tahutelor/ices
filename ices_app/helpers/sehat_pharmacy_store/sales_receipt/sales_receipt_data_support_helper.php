<?php

class Sales_Receipt_Data_Support {

    public static function sales_receipt_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select pr.*
                ,st.code store_code
                ,st.name store_name
            from sales_receipt pr
            inner join store st on pr.store_id = st.id
            where pr.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $sales_receipt = $rs[0];
            $result['sales_receipt'] = $sales_receipt;
        }
        return $result;
        //</editor-fold>
    }

    public static function input_select_reference_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
        $param = array(
            'lookup_data'=>$lookup_data,
            'q_condition'=>'and si.outstanding_grand_total_amount > 0 and si.sales_invoice_status = "invoiced"'
        );
        $temp = Sales_Invoice_Data_Support::input_select_sales_invoice_search($param);
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
            case 'sales_invoice':
                SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_data_support'));
                $t_sales_invoice = Sales_Invoice_Data_Support::sales_invoice_get($ref_id);
                if(count($t_sales_invoice)>0){
                    $sales_invoice = $t_sales_invoice['sales_invoice'];
                    $reference_detail = Sales_Invoice_Data_Support::input_select_sales_invoice_detail_get(array('sales_invoice_id'=>$ref_id));
                    $outstanding_grand_total_amount = Tools::_float($sales_invoice['outstanding_grand_total_amount']);
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