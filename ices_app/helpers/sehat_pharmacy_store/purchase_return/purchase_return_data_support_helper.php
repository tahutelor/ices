<?php

class Purchase_Return_Data_Support {

    public static function purchase_return_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select pr.*
                ,st.id store_id
                ,st.code store_code
                ,st.name store_name
            from purchase_return pr
            inner join store st on pr.store_id = st.id
            where pr.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $purchase_return = $rs[0];
            $pr_product = array();
            $q = '
                select distinct prp.*
                    ,p.code product_code
                    ,p.name product_name
                    ,u.code unit_code
                    ,u.name unit_name
                    ,pb.id product_batch_id
                    ,pip.expired_date
                    ,pip.product_type
                    ,pip.product_id
                    ,pip.unit_id
                from pr_product prp
                inner join pi_product pip on prp.ref_type="pi_product" and prp.ref_id = pip.id
                inner join product p on pip.product_id = p.id
                inner join unit u on pip.unit_id = u.id
                inner join product_batch pb
                    on pb.ref_type = "pi_product" and pb.ref_id = pip.id
                where prp.purchase_return_id = '.$db->escape($purchase_return['id']).'
            ';
            
            $rs = $db->query_array($q);
            if(count($rs)>0){
                $pr_product = $rs;
            }
            $result['pr_product'] = $pr_product;
            $result['purchase_return'] = $purchase_return;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_reference_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
        $param = array(
            'lookup_data'=>$lookup_data,
            'q_condition'=>'and pi.outstanding_grand_total_amount > 0'
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
        $ref_outstanding_amount = Tools::_float('0');
        $ref_product = array();
        
        switch($ref_type){
            case 'purchase_invoice':
                SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_data_support'));
                $t_purchase_invoice = Purchase_Invoice_Data_Support::purchase_invoice_get($ref_id);
                if(count($t_purchase_invoice)>0){
                    $purchase_invoice = $t_purchase_invoice['purchase_invoice'];
                    $ref_product = $t_purchase_invoice['pi_product'];
                    $reference_detail = Purchase_Invoice_Data_Support::input_select_purchase_invoice_detail_get(array('purchase_invoice_id'=>$ref_id));
                    $ref_outstanding_amount = Tools::_float($purchase_invoice['outstanding_grand_total_amount']);
                    
                    $ref_product = json_decode(json_encode($ref_product));
                    foreach($ref_product as $idx=>$row){
                        $row->ref_type='pi_product';
                        $row->ref_id=$row->id;
                        $row->available_qty = Tools::_float($row->product_batch_qty);
                        $row->product_text = Tools::html_tag('strong',$row->product_code)
                            .' '.$row->product_name;
                        $row->unit_text = Tools::html_tag('strong',$row->unit_code)
                            .' '.$row->unit_name;
                    }
                    $ref_product = json_decode(json_encode($ref_product),true);
                    
                }
                break;
        }
        
        
        $result['ref_outstanding_amount'] = $ref_outstanding_amount;
        $result['reference_detail'] = $reference_detail;
        $result['ref_product'] = $ref_product;
        return $result;
        //</editor-fold>
    }

}

?>