<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'controllers/ices/smart_search.php',
    'src_class' => 'Smart_Search',
    'src_extends_class' => '',
    'dst_class' => 'Smart_Search_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Smart_Search extends Smart_Search_Parent {
    
    function __construct(){
        parent::__construct();
    }
    
    public function ajax_search($method=''){
        //<editor-fold defaultstate="collapsed">
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data'])?Tools::_str($data['data']):'';
        $result =array();
        switch($method){
            case 'smart_search':
                $db = new DB();
                $lookup_str = $db->escape('%'.$lookup_data.'%'); 
                //<editor-fold defaultstate="collapsed" desc="Query Contact">
                $q_contact = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'contact', 'index')?
                    ' union all
                        select distinct 
                        id
                        ,"Contact"
                        ,concat(c.code)
                        ,concat(c.code," ",c.name)
                        , "contact"

                    from contact c
                    where c.status>0
                    and (
                        c.code like '.$lookup_str.'
                        or c.name like '.$lookup_str.'
                    )
                    ':
                    '';
                //</editor-fold>
                
                $config = array(
                    'additional_filter'=>array(
                        
                    ),
                    'query'=>array(
                        'basic'=>'
                            select * from (
                                select 
                                    null id
                                    ,null module_text
                                    ,null data
                                    ,null description
                                    ,null module
                                limit 0,0'
                                .$q_contact,
                        'where'=>'
                            
                        ',
                        'group'=>'
                            )tfinal
                        ',
                        'order'=>'order by module_text, data asc'
                    ),
                );                
                $temp_result = SI::form_data()->ajax_table_search($config, $data,array('output_type'=>'object'));
                $t_data = $temp_result->data;
                foreach($t_data as $i=>$row){
                    $row->data = '<a target="_blank" href="'.ICES_Engine::$app['app_base_url'].$row->module.'/view/'.$row->id.'">'.$row->data.'</a>';
                }
                $temp_result = json_decode(json_encode($temp_result),true);
                $result = $temp_result;

                break;

        }
        
        echo json_encode($result);
        //</editor-fold>
    }

    
}