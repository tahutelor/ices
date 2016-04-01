<?php

class Unit_Data_Support {

    public static function unit_get($id) {
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select s.*
            from unit s   
            where s.id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $unit = $rs[0];
            

            $result['unit'] = $unit;
        }
        return $result;
        //</editor-fold>
    }

    public static function unit_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_unit_status = isset($param['unit_status'])?
            ' and u.unit_status = '.$db->escape($param['unit_status']):'';
        $q = '
            select u.*
            from unit u
            where u.status>0
            
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $result = $rs;
        }
        return $result;
        //</editor-fold>
    }

    public static function input_select_unit_list_get($param = array()) {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $t_unit_list  = self::unit_list_get();
        foreach($t_unit_list as $idx=>$row){
            $result[] = array(
                'id'=>$row['id'],
                'text'=>'<strong>'.$row['code'].'</strong>'.' '.$row['name'],
            );
        }
        return $result;
        //</editor-fold>
    }
    
}

?>