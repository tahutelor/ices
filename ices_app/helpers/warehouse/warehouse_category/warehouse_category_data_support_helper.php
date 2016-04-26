<?php

class Warehouse_Category_Data_Support {

    public static function warehouse_category_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from warehouse_category
            where id = ' . $db->escape($id) . '
                and status>0
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $warehouse_category = $rs[0];
            $result['warehouse_category'] = $warehouse_category;
                    
        }
        return $result;
        //</editor-fold>
    }

    public static function warehouse_category_list_get($param=array()) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q_cc_status = isset($param['warehouse_category_status'])?
            ' and warehouse_category_status = '.$db->escape($param['warehouse_category_status']):
            '';
        
        $q = '
            select *
            from warehouse_category
            where status>0'
            .$q_cc_status
            
        .'';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }

}
?>