<?php

class Product_Stock_Opname_Data_Support {

    public static function product_stock_opname_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select pso.*
                ,st.code store_code
                ,st.name store_name
            from product_stock_opname pso
            inner join store st on pso.store_id = st.id
            where pso.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $product_stock_opname = $rs[0];
            $pso_product = array();
            $q = '
                select distinct psop.*
                    ,p.code product_code
                    ,p.name product_name
                    ,u.code unit_code
                    ,u.name unit_name
                    ,pb.id product_batch_id
                    ,pb.batch_number batch_number
                    ,pb.expired_date expired_date
                from pso_product psop
                inner join product_batch pb
                    on psop.ref_type="product_batch" and psop.ref_id = pb.id
                inner join product p on pb.product_id = p.id and pb.product_type = "registered_product"
                inner join unit u on pb.unit_id = u.id
                where psop.product_stock_opname_id = '.$db->escape($product_stock_opname['id']).'
            ';
            
            $rs = $db->query_array($q);
            if(count($rs)>0){
                $pso_product = $rs;
            }
            $result['pso_product'] = $pso_product;
            $result['product_stock_opname'] = $product_stock_opname;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function product_stock_opname_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_product_stock_opname_status = isset($param['product_stock_opname_status'])?
            ' and u.product_stock_opname_status = '.$db->escape($param['product_stock_opname_status']):'';
        $q = '
            select u.*
            from product_stock_opname u
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
    
    public static function input_select_product_batch_search($param=array()){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'product_batch','class_name'=>'product_batch_data_support'));
        SI::module()->load_class(array('module'=>'product_stock','class_name'=>'product_stock_data_support'));
        $result = array();
        
        $temp = Product_Batch_Data_Support::input_select_product_batch_search($param);
        $t_product_batch_list = array();
        $product_batch = array();
        foreach($temp as $idx=>$row){
            $t_product_batch_list[] = array(
                'id'=>$row['id'],
                'text'=>$row['text'],
                'qty'=>'0',
            );
            $product_batch[] = $row['id'];
        }
        
        $t_param = array('module'=>'stock_good','warehouse'=>array($param['warehouse_id']),'product_batch'=>$product_batch);
        $temp_product_stock = Product_Stock_Data_Support::product_stock_mass_get($t_param);
        
        foreach($t_product_batch_list as $idx=>$row){
            foreach($temp_product_stock as $idx2=>$row2){
                if($row['id'] === $row2['product_batch_id']){
                    $t_product_batch_list[$idx]['qty'] = $row2['qty'];
                }
            }
        }
        $result =  $t_product_batch_list;
        
        return $result;
        //</editor-fold>
    }
    
}

?>