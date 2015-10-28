<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_Stock_Data_Support {

    public static function product_stock_get($module, $id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select ps.*
            
            from product_'.$module.' ps            
            where ps.status> 0
                and ps.id = ' . $db->escape($id) . '
                
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $result['product_'.$module] = $rs[0];
        }
        return $result;
        //</editor-fold>
    }
    
    public static function product_stock_mass_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $product_batch = $param['product_batch'];
        $warehouse = $param['warehouse'];
        $module = $param['module'];
        
        $db = new DB();
        $tbl_name = 'product_'.$module;
        
        $q_product_batch_id = '-1';
        foreach($product_batch as $idx=>$row){
            $q_product_batch_id.= ','.$db->escape($row);
        }
        
        $q_warehouse_id = '-1';
        foreach($warehouse as $idx=>$row){
            $q_warehouse_id.= ','.$db->escape($row);
        }
        
        $q = '
            select distinct pb.id product_batch_id
                ,w.id warehouse_id
                ,ps.qty qty
            from product_batch pb
            cross join warehouse w
            left outer join product_'.$module.' ps
                on ps.product_batch_id = pb.id
                and ps.warehouse_id = w.id
            where pb.id in ('.$q_product_batch_id.')
                and pb.status > 0
                and w.status > 0
                and ps.status > 0
                and w.id in ('.$q_warehouse_id.')
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0) $result = $rs;
        
        return $result;
        //</editor-fold>
    }
    
}

?>