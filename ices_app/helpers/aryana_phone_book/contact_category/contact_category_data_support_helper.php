<?php

class Contact_Category_Data_Support {

    public static function contact_category_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from contact_category
            where id = ' . $db->escape($id) . '
                and status>0
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $contact_category = $rs[0];
            $result['contact_category'] = $contact_category;
                    
        }
        return $result;
        //</editor-fold>
    }

    public static function contact_category_list_get($param=array()) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q_cc_status = isset($param['contact_category_status'])?
            ' and contact_category_status = '.$db->escape($param['contact_category_status']):
            '';
        
        $q = '
            select *
            from contact_category
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