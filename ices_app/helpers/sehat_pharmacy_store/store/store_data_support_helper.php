<?php
class Store_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'store/store_engine');
        //</editor-fold>
    }
    
    public static function store_get($id){
        //<editor-fold defaultstate="collapsed">
        $db = new DB();
        $result = array();
        $q = '
            select *
            from store
            where id = ' . $db->escape($id) . '
        ';
        $rs = $db->query_array($q);
        if (count($rs) > 0) {
            $store = $rs[0];
            $s_address = array();
            $s_phone_number = array();

            $q = '
                select sa.*
                from s_address sa
                where sa.store_id = ' . $db->escape($id) . '
            ';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $s_address = $rs;

            $q = 'select spn.*
                    ,pnt.name phone_number_type_name
                    ,pnt.code phone_number_type_code
                from s_phone_number spn 
                inner join store s on spn.store_id = s.id 
                inner join phone_number_type pnt on pnt.id = spn.phone_number_type_id 
                where spn.store_id=' . $db->escape($id) . '
                    and s.status > 0';
            $rs = $db->query_array($q);
            if (count($rs) > 0)
                $s_phone_number = $rs;


            $result['store'] = $store;
            $result['s_address'] = $s_address;
            $result['s_phone_number'] = $s_phone_number;
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
        $t_phone_number_type = Phone_Number_Type_Data_Support::phone_number_type_list_get(array('phone_number_type_status'=>'active'));
        $result = $t_phone_number_type;
        return $result;
        //</editor-fold>
    }
    
    public static function store_list_get($param = array()){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $db = new DB();
        $q_store_status = isset($param['store_status'])?
            ' and s.store_status = '.$db->escape($param['store_status']):'';
        $q = '
            select s.id
            from store s
            where s.status>0
            '.$q_store_status.'
        ';
        $rs = $db->query_array($q);
        if(count($rs)>0){
            foreach($rs as $idx=>$row){
                $result[] = self::store_get($row['id']);
            }
        }
        return $result;
        //</editor-fold>
    }
        
    public static function store_validate($store_id){
        //<editor-fold defaultstate="collapsed">
        $result = SI::result_format_get();
        $success = 1;
        $msg = array();
        $db = new DB();
        
        $t_store = self::store_get($store_id);
        if(!count($t_store)>0){
            $success = 0;
            
        }
        else if ($t_store['store']['store_status'] !== 'active'){
            $success = 0;
            
        }
        
        if($success !== 1){
            $msg[] = Lang::get('Store')
                .' '.Lang::get('invalid');
        }
        
        $result['success'] = $success;
        $result['msg'] = $msg;
        return $result;
        //</editor-fold>
    }
    
    public static function input_select_store_list_get(){
        //<editor-fold defaultstate="collapsed">
        $result = array();
        $t_store_list = Store_Data_Support::store_list_get(array('store_status'=>'active'));
        foreach($t_store_list as $idx=>$row){
            $address = '';
            if(isset($row['s_address'])){
                if(count($row['s_address'])>0){
                    $address = ' - '.$row['s_address'][0]['address'];
                }
            }

            $result[] = array(
                'id'=>$row['store']['id'],
                'text'=>Tools::html_tag('strong',$row['store']['code'])
                    .' '.$row['store']['name'].' '.$address
            );
        }

        if(count($t_store_list)>0){
            $result[0]['default'] = true;
        }
        return $result;
        //</editor-fold>
    }
    
}
?>
