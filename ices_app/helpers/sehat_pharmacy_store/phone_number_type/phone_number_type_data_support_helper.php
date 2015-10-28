<?php

class Phone_Number_Type_Data_Support {

    public static function phone_number_type_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from phone_number_type
            where id = ' . $db->escape($id) . '
                and status>0
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $phone_number_type = $rs[0];
            $result['phone_number_type'] = $phone_number_type;
                    
        }
        return $result;
        //</editor-fold>
    }

    public static function phone_number_type_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q_pnt_status = isset($param['phone_number_type_status'])?
            ' and phone_number_type_status = '.$db->escape($param['phone_number_type_status']):
            '';
        
        $q = '
            select *
            from phone_number_type
            where status>0'
                .$q_pnt_status
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