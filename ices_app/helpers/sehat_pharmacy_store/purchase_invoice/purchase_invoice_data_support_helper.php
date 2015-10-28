<?php

class Purchase_Invoice_Data_Support {

    public static function purchase_invoice_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select pi.*
                ,s.id supplier_id
                ,s.code supplier_code
                ,s.name supplier_name
                ,st.id store_id
                ,st.code store_code
                ,st.name store_name
            from purchase_invoice pi
            inner join supplier s on pi.supplier_id = s.id
            inner join store st on pi.store_id = st.id
            where pi.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $purchase_invoice = $rs[0];
            $pi_product = array();
            $q = '
                select pip.*
                    ,p.code product_code
                    ,p.name product_name
                    ,u.code unit_code
                    ,u.name unit_name
                    ,pb.id product_batch_id
                    ,pb.qty product_batch_qty
                from pi_product pip
                inner join product p on pip.product_id = p.id
                inner join unit u on pip.unit_id = u.id
                inner join product_batch pb 
                    on pb.ref_type = "pi_product" and pb.ref_id = pip.id
                where pip.purchase_invoice_id = '.$db->escape($purchase_invoice['id']).'
            ';
            
            $rs = $db->query_array($q);
            if(count($rs)>0){
                $pi_product = $rs;
            }
            $result['pi_product'] = $pi_product;
            $result['purchase_invoice'] = $purchase_invoice;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function purchase_invoice_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_purchase_invoice_status = isset($param['purchase_invoice_status'])?
            ' and u.purchase_invoice_status = '.$db->escape($param['purchase_invoice_status']):'';
        $q = '
            select u.*
            from purchase_invoice u
            where u.status>0
            
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_supplier_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_data_support'));
        $result = array();
        $t_supplier_list = Supplier_Data_Support::input_select_supplier_search(array('lookup_data'=>$lookup_data,'supplier_status'=>'active'));
        return $result = $t_supplier_list;
        //</editor-fold>
    }
    
    public static function supplier_dependency_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $supplier_id = $param['supplier_id'];
        $supplier_detail = array();
        
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_data_support'));
        $supplier_detail = Supplier_Data_Support::input_select_supplier_detail_get(array('supplier_id'=>$supplier_id));
        
        $result['supplier_detail'] = $supplier_detail;
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_product_search($lookup_data){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
        $result = array();
        $t_product_list = Product_Data_Support::input_select_product_search(array('lookup_data'=>$lookup_data,'product_status'=>'active'));
        $result =  $t_product_list;
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_purchase_invoice_search($param=array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $lookup_data = $param['lookup_data'];
        $q_condition = $param['q_condition'];
        
        $db = new DB();
        
            
        $q = '
            select distinct pi.*
            from purchase_invoice pi
            where pi.status > 0
            and (
                pi.code like '.$db->escape('%'.$lookup_data.'%').'
                or pi.supplier_si_code like '.$db->escape('%'.$lookup_data.'%').'
                or pi.grand_total_amount like '.$db->escape('%'.$lookup_data.'%').'
            )
                '.$q_condition.'
            order by pi.id desc
            limit 100
        ';
        
        $rs = $db->query_array($q);
        if(count($rs)>0){
            foreach($rs as $idx=>$row){
                $result[] = array(
                    'id'=>$row['id'],
                    'text'=>Tools::html_tag('strong',$row['code'])
                        .' '.'Grand Total Amount: '.Tools::thousand_separator($row['grand_total_amount'])
                    ,
                    'ref_type'=>'purchase_invoice',
                    'ref_outstanding_amount'=>$row['outstanding_grand_total_amount'],
                );
            }
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_purchase_invoice_detail_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $purchase_invoice_id = $param['purchase_invoice_id'];        
        $t_purchase_invoice = self::purchase_invoice_get($purchase_invoice_id);
        if(count($t_purchase_invoice)>0){
            $purchase_invoice = $t_purchase_invoice['purchase_invoice'];
            $pi_view_path = ICES_Engine::$app['app_base_url'].'purchase_invoice/view/';
            $result = array(
                array('id'=>'code','label'=>Lang::get('Code').': ','val'=>'<a href="'.$pi_view_path.$purchase_invoice_id.'" target="_blank">'.$purchase_invoice['code'].'</a>'),
                array('id'=>'type','label'=>Lang::get('Type').': ','val'=>'Purchase Invoice'),
                array('id'=>'purchase_invoice_date','label'=>Lang::get('Purchase Invoice Date').': ','val'=>Tools::_date($purchase_invoice['purchase_invoice_date'],'F d, Y H:i')),
                array('id'=>'supplier_si_code','label'=>Lang::get('Sales Invoice').' '.Lang::get('Supplier').': ','val'=>$purchase_invoice['supplier_si_code']),
                array('id'=>'grand_total_amount','label'=>'Grand Total Amount: ','val'=>Tools::thousand_separator($purchase_invoice['grand_total_amount'])),
                array('id'=>'outstanding_grand_total_amount','label'=>'Outstanding Amount: ','val'=>Tools::thousand_separator($purchase_invoice['outstanding_grand_total_amount'])),
            );
        }
        
        return $result;
        //</editor-fold>
    }
    
    function notification_outstanding_grand_total_amount_get(){
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'rpt_simple/rpt_simple_data_support');
        $result = array('response'=>null);
        $response = null;        
        $temp_result = Rpt_Simple_Data_Support::report_table_purchase_invoice_outstanding_grand_total_amount();        
        if($temp_result['info']['data_count']>0){
            $response = array(
                'icon'=>App_Icon::html_get(APP_Icon::purchase_invoice())
                ,'href'=>ICES_Engine::$app['app_base_url'].'rpt_simple/index/purchase_invoice/outstanding_grand_total_amount'
                ,'msg'=>' '.($temp_result['info']['data_count']).' '.'purchase invoice'.' - '.Lang::get('outstanding amount',true,false,false,true)
            );
        }
        $result['response'] = $response;
        return  $result;
    }

}

?>