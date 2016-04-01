<?php

class Company_Data_Support {

    public static function company_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from company
            where id = ' . $db->escape($id) . '
                and status>0
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $company = $rs[0];
            $result['company'] = $company;
                    
        }
        return $result;
        //</editor-fold>
    }

    public static function company_search($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $lookup_str = $param['lookup_str'];
        $q_pnt_status = isset($param['company_status'])?
            ' and company_status = '.$db->escape($param['company_status']):
            '';
        
        $q = '
            select *
            from company
            where status>0
                and company.name like '.$db->escape('%'.$lookup_str.'%').'
            
                '.$q_pnt_status.'
                
        ';
        $rs = $db->query_array($q,1000);
        
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }

}
?>