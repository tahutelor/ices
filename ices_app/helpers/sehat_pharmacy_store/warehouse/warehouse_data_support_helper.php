<?php
class Warehouse_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'warehouse/warehouse_engine');
        //</editor-fold>
    }
    
    public static function warehouse_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from warehouse
            where id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $warehouse = $rs[0];
            $w_address = array();
            $w_phone_number = array();

            $q = '
                select sa.*
                from w_address sa
                where sa.warehouse_id = ' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $w_address = $rs;

            $q = 'select spn.*
                    ,pnt.name phone_number_type_name
                    ,pnt.code phone_number_type_code
                from w_phone_number spn 
                inner join warehouse s on spn.warehouse_id = s.id 
                inner join phone_number_type pnt on pnt.id = spn.phone_number_type_id 
                where spn.warehouse_id=' . $db->escape($id) . '
                    and s.status > 0';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $w_phone_number = $rs;


            $result['warehouse'] = $warehouse;
            $result['w_address'] = $w_address;
            $result['w_phone_number'] = $w_phone_number;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function phone_number_type_get() {
        //<editor-fold defaultstate="collapsed">
        $result = array();
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'phone_number_type/phone_number_type_engine');
        $path = Phone_Number_Type_Engine::path_get();

        get_instance()->load->helper($path->phone_number_type_data_support);
        $t_phone_number_type = Phone_Number_Type_Data_Support::phone_number_type_list_get();
        $result = $t_phone_number_type;
        return $result;
        //</editor-fold>
    }
    
    public static function warehouse_list_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_warehouse_status = isset($param['warehouse_status'])?
            ' and s.warehouse_status = '.$db->escape($param['warehouse_status']):'';
        $q = '
            select s.id
            from warehouse s
            where s.status>0
            '.$q_warehouse_status.'
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            foreach($rs as $idx=>$row){
                $result[] = self::warehouse_get($row['id']);
            }
        }
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_warehouse_list_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $t_warehouse_list = Warehouse_Data_Support::warehouse_list_get(array('warehouse_status'=>'active'));
        foreach($t_warehouse_list as $idx=>$row){
            $t_warehouse = $row['warehouse'];
            $t_warehouse['text']=Tools::html_tag('strong',$row['warehouse']['code'])
                    .' '.$row['warehouse']['name']
            ;
            $result[] = $t_warehouse;
        }

        if(count($t_warehouse_list)>0){
            $result[0]['default'] = true;
        }
        return $result;
        //</editor-fold>
    }
    
    public static function warehouse_validate($warehouse_id){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $t_warehouse = self::warehouse_get($warehouse_id);
        if(!count($t_warehouse)>0){
            $success = 0;
            $msg[] = Lang::get('Warehouse')
                .' '.Lang::get('invalid');
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
}
?>
