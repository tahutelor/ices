<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Product_Category extends MY_ICES_Controller {
        
    private $title='';
    private $title_icon = '';
    private $path = array();
    
    function __construct(){
        parent::__construct();
        $this->title = Lang::get(array('Product Category'),true,true,false,false,true);
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'product_category/product_category_engine');
        $this->path = Product_Category_Engine::path_get();
        $this->title_icon = APP_ICON::product_category();
    }
    
    public function index()
    {           
        //<editor-fold defaultstate="collapsed">
        $action = "";

        $app = new App();            
        $db = $this->db;

        $app->set_title($this->title);
        $app->set_breadcrumb($this->title,strtolower('product_category'));
        $app->set_content_header($this->title,$this->title_icon,$action);

        $row = $app->engine->div_add()->div_set('class','row');            
        $form = $row->form_add()->form_set('title',Lang::get('Product Category','List'))->form_set('span','12');
        $form->form_group_add()->button_add()->button_set('class','primary')->button_set('value',Lang::get(array('New','Product Category')))
                ->button_set('icon','fa fa-plus')->button_set('href',ICES_Engine::$app['app_base_url'].'product_category/add');
        
        
         
        $cols = array(
            array("name"=>"prnt_product_category_text","label"=>"Parent","data_type"=>"text"),
            array("name"=>"code","label"=>"Code","data_type"=>"text",'is_key'=>true),
            array("name"=>"name","label"=>"Name","data_type"=>"text"),
            array("name"=>"product_category_status","label"=>"Status","data_type"=>"text")
        );
        
        $tbl = $form->table_ajax_add();
        $tbl->table_ajax_set('id','ajax_table')
            ->table_ajax_set('base_href',$this->path->index.'view')
            ->table_ajax_set('lookup_url',$this->path->index.'ajax_search/product_category')
            ->table_ajax_set('columns',$cols)
        ;
        $app->render();
        //</editor-fold>
    }
    
    public function add(){
        //<editor-fold defaultstate="collapsed">
        $post = $this->input->post();        
        $this->view('','add');        
        //</editor-fold>
    }
    
    public function view($id = "",$method="view"){
        //<editor-fold defaultstate="collapsed">
        $this->load->helper($this->path->product_category_data_support);
        $this->load->helper($this->path->product_category_renderer);
        
        $action = $method;
        $cont = true;
        
        if(!in_array($method,array('add','view'))){
            Message::set('error',array("Method error"));
            $cont = false;
        }
        
        if($cont){
            if(in_array($method,array('view'))){
                if(!count(Product_Category_Data_Support::product_category_get($id))>0){
                    Message::set('error',array("Data doesn't exist"));
                    $cont = false;
                }
            }
        }
        
        if($cont){        
            if($method=='add') $id = '';
            $data = array(
                'id'=>$id
            );
            
            $app = new App();            
            $app->set_title($this->title);
            $app->set_menu('collapsed',true);
            $app->set_breadcrumb($this->title,strtolower('product_category'));
            $app->set_content_header($this->title,$this->title_icon,$action);
            $row = $app->engine->div_add()->div_set('class','row');            

            $nav_tab = $row->div_add()->div_set("span","12")->nav_tab_add();

            $detail_tab = $nav_tab->nav_tab_set('items_add'
                    ,array("id"=>'#detail_tab',"value"=>"Detail",'class'=>'active'));
            $detail_pane = $detail_tab->div_add()->div_set('id','detail_tab')->div_set('class','tab-pane active');
            Product_Category_Renderer::product_category_render($app,$detail_pane,array("id"=>$id),$this->path,$method);
            if($method === 'view'){
                $history_tab = $nav_tab->nav_tab_set('items_add'
                    ,array("id"=>'#status_log_tab',"value"=>"Status Log"));
                $history_pane = $history_tab->div_add()->div_set('id','status_log_tab')->div_set('class','tab-pane');
                Product_Category_Renderer::product_category_status_log_render($app,$history_pane,array("id"=>$id),$this->path);
                
            }
            
            
            $app->render();
        }
        else{
            redirect($this->path->index);
        }
        //</editor-fold>
    }

    public function product_category_add(){
        $post = $this->input->post();
        if($post!= null){
            $param = array('id'=>'','method'=>'product_category_add','primary_data_key'=>'product_category','data_post'=>$post);
            SI::data_submit()->submit('product_category_engine',$param);
            
        }        
    }
    
    public function product_category_active($id = ''){
        $post = $this->input->post();
        if($post!= null){
            $param = array('id'=>$id,'method'=>'product_category_active','primary_data_key'=>'product_category','data_post'=>$post);
            SI::data_submit()->submit('product_category_engine',$param);
            
        }
    }
    
    public function product_category_inactive($id = ''){
        $post = $this->input->post();
        if($post!= null){
            $param = array('id'=>$id,'method'=>'product_category_inactive','primary_data_key'=>'product_category','data_post'=>$post);
            $result = SI::data_submit()->submit('product_category_engine',$param);
            
        }
    }

    public function ajax_search($method=''){
        //<editor-fold defaultstate="collapsed">
        $path = Product_Category_Engine::path_get();
        get_instance()->load->helper($path->product_category_data_support);
        $data = json_decode($this->input->post(), true);
        $lookup_data = isset($data['data'])?Tools::_str($data['data']):'';
        $result =array();
        $response = array();
        switch($method){
            case 'product_category':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $lookup_str = $db->escape('%'.$lookup_data.'%');                
                $config = array(
                    'additional_filter'=>array(
                        
                    ),
                    'query'=>array(
                        'basic'=>'
                            select * from (
                                select distinct pc.*
                                    ,pc2.code prnt_product_category_code
                                    ,pc2.name prnt_product_category_name
                                from product_category pc
                                left outer join product_category pc2 
                                    on pc.prnt_product_category_id = pc2.id
                                where pc.status>0
                                
                        ',
                        'where'=>'
                            and (
                                pc.code like '.$lookup_str.' 
                                or pc.name like '.$lookup_str.' 
                                or pc.product_category_status like '.$lookup_str.'
                                or pc2.code like '.$lookup_str.'
                                or pc2.name like '.$lookup_str.'
                            )
                        ',
                        'group'=>'
                            )tfinal
                        ',
                        'order'=>'order by id desc'
                    ),
                );                
                $temp_result = SI::form_data()->ajax_table_search($config, $data,array('output_type'=>'object'));
                $t_data = $temp_result->data;
                foreach($t_data as $i=>$row){
                    $row->product_category_status = SI::get_status_attr(
                        SI::type_get('Product_Category_Engine',$row->product_category_status,'$status_list')['text']
                    );
                    $row->prnt_product_category_text = Tools::html_tag('strong',$row->prnt_product_category_code)
                        .' '.$row->prnt_product_category_name
                    ;
                }
                $temp_result = json_decode(json_encode($temp_result),true);
                $result = $temp_result;
                //</editor-fold>
                break;
            
            case 'prnt_product_category_search':
                //<editor-fold defaultstate="collapsed">
                $product_category_id = Tools::_str(isset($data['extra_param']['product_category_id'])?$data['extra_param']['product_category_id']:'');
                $response = Product_Category_Data_Support::input_select_prnt_product_category_search(
                    array('lookup_data'=>$lookup_data,
                        'product_category_id'=>$product_category_id
                    )
                );
                
                
                //</editor-fold>
                break;
        }
        $result['response'] = $response;
        echo json_encode($result);
        //</editor-fold>
    }

    public function data_support($method=''){
        //<editor-fold defaultstate="collapsed">
        $path = Product_Category_Engine::path_get();
        get_instance()->load->helper($path->product_category_data_support);
        $data = json_decode($this->input->post(), true);
        $result = SI::result_format_get();
        $msg=[];
        $success = 1;
        $response = array();
        switch($method){
             case 'product_category_get':
                //<editor-fold defaultstate="collapsed">
                $db = new DB();
                $response = array();
                $product_category_id = Tools::_str(isset($data['data']) ? $data['data'] : '');
                $temp = Product_Category_Data_Support::product_category_get($product_category_id);
                if (count($temp) > 0) {
                    $product_category = $temp['product_category'];
                    $product_category['product_category_status'] = array(
                        'id' => $product_category['product_category_status'],
                        'text' => SI::get_status_attr(
                                SI::type_get('product_category_engine', $product_category['product_category_status'], '$status_list'
                                )['text']
                        )
                    );
                    
                    $product_category['prnt_product_category'] = null;
                    if(!is_null($product_category['prnt_product_category_id'])){
                        $product_category['prnt_product_category'] = array(
                            'id' => $product_category['prnt_product_category_id'],
                            'text' => Tools::html_tag('strong',$product_category['prnt_product_category_code'])
                                .' '.$product_category['prnt_product_category_name']
                        );
                    }
                    
                    $next_allowed_status_list = SI::form_data()
                            ->status_next_allowed_status_list_get('product_category_engine', $product_category['product_category_status']['id']
                    );

                    $response['product_category'] = $product_category;
                    $response['product_category_status_list'] = $next_allowed_status_list;
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