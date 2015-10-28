<?php
class Employee_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        $ices = SI::type_get('ICES_Engine','ices','$app_list');
        get_instance()->load->helper($ices['app_base_dir'].'employee/employee_engine');
        //</editor-fold>
    }
    
    public static function employee_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from employee
            where id = '.$db->escape($id).'
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            $employee = $rs[0];
            $u_group = array();
            $employee_id = $id;
            
            $q = '
                select distinct ug.*
                from u_group ug
                inner join employee_u_group eug on ug.id = eug.u_group_id
                where eug.employee_id = '.$db->escape($id).'
                    and ug.status>0
                order by ug.app_name, ug.name
            ';
            $rs = $db->query_array($q);
            if(count($rs)>0){
                $u_group = $rs;
            }
            $result['employee'] = $employee;
            $result['u_group'] = $u_group;
        }
        return $result;
        //</editor-fold>
    }
    
    
}
?>
