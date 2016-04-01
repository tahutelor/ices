<?php
class Product_Unit_Conversion_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'product_unit_conversion/product_Unit_conversion_engine');
        //</editor-fold>
    }
    
    public static function product_unit_conversion_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select distinct puc.*                
                ,u.code unit_code
                ,u.name unit_name
                ,u2.code unit_code2
                ,u2.name unit_name2
                
            from p_u_conversion puc
            inner join unit u on puc.unit_id = u.id
            inner join unit u2 on puc.unit_id2 = u2.id
            where puc.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $product_unit_conversion = $rs[0];
            $result['product_unit_conversion'] = $product_unit_conversion;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function product_unit_conversion_get_by_product_id($product_id){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q = '
            select distinct puc.*                
            from p_u_conversion puc
            where puc.product_id = ' . $db->escape($product_id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $product_unit_conversion = $rs;
            $result['product_unit_conversion'] = $product_unit_conversion;
        }
        return $result;
        //</editor-fold>
    }
        
}
?>
