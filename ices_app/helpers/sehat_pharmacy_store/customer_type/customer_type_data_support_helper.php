<?php
class Customer_Type_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'customer_type/customer_type_engine');
        //</editor-fold>
    }
    
    public static function customer_type_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select distinct ct.*
            from customer_type ct
            where ct.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $customer_type = $rs[0];
            $result['customer_type'] = $customer_type;
            
        }
        return $result;
        //</editor-fold>
    }
    
    public static function customer_type_default_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q = '
            select ct.id
            from customer_type ct
            where ct.status > 0
                and ct.customer_type_status = "active"
                and ct.customer_type_default = 1
                
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $customer_type_id = $rs[0]['id'];
            $result = self::customer_type_get($customer_type_id);
        }
        
        return $result;
        //</editor-fold>
    }
    
    public static function customer_type_list_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_ct_status = isset($param['customer_type_status'])?
            ' and ct.customer_type_status = '.$db->escape($param['customer_type_status']):
            '';
        $q = '
            select ct.*
            from customer_type ct
            where ct.status > 0'
                .$q_ct_status
            
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
