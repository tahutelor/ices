<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Smart_Search extends MY_ICES_Controller {
        
    private $title='';
    private $title_icon = '';
    private $path = array();
    
    function __construct(){
        parent::__construct();
        $this->title = Lang::get(array('Smart Search'),true,true,false,false,true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'smart_search/smart_search_engine');
        $this->path = Smart_Search_Engine::path_get();
        $this->title_icon = APP_ICON::smart_search();
    }
    
    public function index($param = ''){           
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();            
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title,strtolower('smart_search'));
        $app->set_content_header($this->title,$this->title_icon,$action);

        $row = $app->engine->div_add()->div_set('class','row');
        $form = $row->form_add()->form_set('title',Lang::get('Smart Search','List'))->form_set('span','12');
         
        $cols = array(
            array("name"=>"module_text","label"=>"Module","data_type"=>"text"),
            array("name"=>"data","label"=>"Data","data_type"=>"text"),
            array("name"=>"description","label"=>"Description","data_type"=>"text")
        );
        
        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id','smart_search')
            ->table_ajax_set('base_href',$this->path->index.'view')
            ->table_ajax_set('lookup_url',$this->path->index.'ajax_search/smart_search')
            ->table_ajax_set('columns',$cols)
            ->table_ajax_set('key_exists',false)


        ;
        $js = '
            $("#smart_search_filter").val("'.Tools::_str($param).'");
            smart_search.filter = "'.Tools::_str($param).'"    
            smart_search.methods.data_show(1);
        ';
        $app->js_set($js);
        
        $app->render();
        //</editor-fold>
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
                //<editor-fold defaultstate="collapsed" desc="Query Security Controller">
                $q_security_controller = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'security_controller', 'index')?
                    ' union all                                
                    select distinct 
                        sc.app_name
                        ,id
                        ,"Security Controller" 
                        ,concat(sc.name," ",sc.method) 
                        ,concat("") 
                        , "security_controller" 

                    from security_controller sc
                    where sc.status>0
                    and (
                        replace(sc.app_name,"_"," ") like '.$lookup_str.'
                        or sc.app_name like '.$lookup_str.'
                        or sc.name like '.$lookup_str.'
                        or sc.method like '.$lookup_str.'                                    
                    )
                    ':
                    ''
                ;
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Query Employee">
                $q_employee = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'employee', 'index')?
                    ' union all                                
                    select distinct 
                        null app_name
                        ,id
                        ,"Employee" module_text
                        ,concat(e.firstname," ",e.lastname) 
                        ,concat(e.username) 
                        , "employee" module
                    from employee e
                    where e.status>0
                    and (
                        e.firstname like '.$lookup_str.'
                        or e.lastname like '.$lookup_str.'
                        or e.username like '.$lookup_str.'                                    
                    )
                    ':
                    ''
                ;
                //</editor-fold>
                
                //<editor-fold defaultstate="collapsed" desc="Query User Group">
                $q_u_group = Security_Engine::get_controller_permission(
                    ICES_Engine::$app['val'], User_Info::get()['user_id'], 'u_group', 'index')?
                    ' union all
                    select distinct 
                        ug.app_name
                        ,id
                        ,"User Group" 
                        ,concat(ug.name) 
                        ,concat("") 
                        , "u_group" 

                    from u_group ug
                    where ug.status>0
                    and (
                        replace(ug.app_name,"_"," ") like '.$lookup_str.'
                        or ug.app_name like '.$lookup_str.'
                        or ug.name like '.$lookup_str.'
                    )
                    ':
                    ''
                ;
                //</editor-fold>
                
                
                $config = array(
                    'additional_filter'=>array(
                        
                    ),
                    'query'=>array(
                        'basic'=>'
                            select * from (
                                select null app_name
                                    ,null id
                                    ,null module_text
                                    ,null data
                                    ,null description
                                    ,null module
                                limit 0,0
                                '
                                .$q_security_controller
                                .$q_employee
                                .$q_u_group,
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
                    if(!is_null($row->app_name)) $row->description = SI::type_get ('ICES_Engine', $row->app_name,'$app_list')['text'];
                    $row->data = '<a target="_blank" href="'.ICES_Engine::$app['app_base_url'].$row->module.'/view/'.$row->id.'">'.$row->data.'</a>';
                }
                $temp_result = json_decode(json_encode($temp_result),true);
                $result = $temp_result;

                break;

        }
        
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method=''){
        //<editor-fold defaultstate="collapsed">
        $path = Smart_Search_Engine::path_get();
        get_instance()->load->helper($path->smart_search_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg=[];
        $success = 1;
        $response = array();
        switch($method){
            case 'smart_search_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $q = '
                    select *
                    from smart_search t1
                    where t1.id = '.$db->escape($data['data']).'
                ';
                $rs = $db->query_array($q);
                if(count($rs)>0){
                    $smart_search = $rs[0];
                    $smart_search['app_name'] = array(
                        'id'=>$smart_search['app_name'],
                        'text'=>SI::type_get('ICES_Engine',
                            $smart_search['app_name'],'$app_list'
                        )['text'],
                    );
                    
                    $smart_search['smart_search_status'] = array(
                        'id'=>$smart_search['smart_search_status'],
                        'text'=>SI::get_status_attr(
                            SI::type_get('smart_search_engine',
                                $smart_search['smart_search_status'],
                                '$status_list'
                            )['text']
                        ),
                    );
                    
                            
                    $next_allowed_status_list = SI::form_data()
                    ->status_next_allowed_status_list_get('smart_search_engine',
                        $smart_search['smart_search_status']['id']
                    );        
                    
                    $response['smart_search'] = $smart_search;
                    $response['smart_search_status_list'] = $next_allowed_status_list;
                    
                }
                
                
                //</editor-fold>
                break;
            
        }
        $result['success'] = $success;
        $result['msg'] = $msg;
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }
    
}