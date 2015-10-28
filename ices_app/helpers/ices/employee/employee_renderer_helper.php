<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Employee_Renderer {

    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'employee/employee_engine');
        //</editor-fold>
    }
    
    public static function modal_employee_render($app,$modal){
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title'=>Lang::get('Employee'),'icon'=>'fa fa-cogs'));
        $modal->width_set('95%');
        self::employee_components_render($app, $modal,true);
        //</editor-fold>
    }

    public static function employee_render($app,$form,$data,$path,$method){
        //<editor-fold defaultstate="collapsed">
        $path = Employee_Engine::path_get();
        
        $id_prefix = Employee_Engine::$prefix_id;
        
        $id = $data['id'];
        self::employee_components_render($app, $form,false);
        $back_href = $path->index;

        $form->button_add()->button_set('value','BACK')
            ->button_set('icon',App_Icon::btn_back())
            ->button_set('href',$back_href)
            ->button_set('class','btn btn-default')
        ;

        $js = '
            <script>
                $("#'.$id_prefix.'_method").val("'.$method.'");
                $("#'.$id_prefix.'_id").val("'.$id.'");
            </script>
        ';             
        $app->js_set($js);

        $js = '                
                '.$id_prefix.'_init();
                '.$id_prefix.'_bind_event();
                '.$id_prefix.'_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function employee_components_render($app,$form,$is_modal){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'u_group/u_group_engine');
        $u_group_path = U_Group_Engine::path_get();
        get_instance()->load->helper($u_group_path->u_group_data_support);
        
        $path = Employee_Engine::path_get();            
        $components = array();
        $db = new DB();

        $id_prefix = Employee_Engine::$prefix_id;

        $form->input_add()->input_set('id',$id_prefix.'_id')
            ->input_set('hide',true)
            ->input_set('value','')
        ;

        $form->input_add()->input_set('id',$id_prefix.'_method')
            ->input_set('hide',true)
            ->input_set('value','')
        ;            
        
        $form->input_add()->input_set('label',Lang::get('First Name'))
            ->input_set('id',$id_prefix.'_firstname')
            ->input_set('icon',APP_ICON::info())
            ->input_set('hide_all',true)
            ->input_set('disable_all',true)
        ;
        
        $form->input_add()->input_set('label',Lang::get('Last Name'))
            ->input_set('id',$id_prefix.'_lastname')
            ->input_set('icon',APP_ICON::info())
            ->input_set('hide_all',true)
            ->input_set('disable_all',true)
        ;
        
        $form->input_add()->input_set('label',Lang::get('Username'))
            ->input_set('id',$id_prefix.'_username')
            ->input_set('icon',APP_ICON::user())
            ->input_set('hide_all',true)
            ->input_set('disable_all',true)
        ;
        
        $form->input_add()->input_set('label','Password')
            ->input_set('id',$id_prefix.'_password')
            ->input_set('icon',APP_ICON::password())
            ->input_set('hide_all',true)
            ->input_set('disable_all',true)
        ;
        
        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_u_group')
            ->main_div_set('class', 'form-group hide_all')
            ->label_set('value', '')
            ->table_input_set('columns', array(
                'col_name' => 'u_group'
                , 'col_id_exists' => true
                , 'th' => array('val' => 'User Group', 'visible' => true)
                , 'td' => array('val' => '', 'tag' => 'input', 'attr' => array('original' => ''), 'class' => '', 'visible' => true
                )
            ))
        ;
        
        $components[$id_prefix.'_status'] = $form->input_select_add()
            ->input_select_set('label','Status')
            ->input_select_set('icon','fa fa-info')
            ->input_select_set('min_length','0')
            ->input_select_set('id',$id_prefix.'_employee_status')
            ->input_select_set('data_add',array())
            ->input_select_set('value',array())
            ->input_select_set('hide_all',true)
            ->input_select_set('is_module_status',true)
            ;
        
        $form->hr_add()->hr_set('class','');

        $form->button_add()->button_set('value','Submit')
            ->button_set('id',$id_prefix.'_btn_submit')
            ->button_set('icon',App_Icon::detail_btn_save())
        ;
        
        $param = array(
            'ajax_url'=>$path->index.'ajax_search/'
            ,'index_url'=>$path->index
            ,'detail_tab'=>'#detail_tab'
            ,'view_url'=>$path->index.'view/'
            ,'window_scroll'=>'body'
            ,'data_support_url'=>$path->index.'data_support/'
            ,'common_ajax_listener'=>ICES_Engine::$app['app_base_url'].'common_ajax_listener/'
            ,'component_prefix_id'=>$id_prefix
        );

        if($is_modal){
            $param['detail_tab'] = '#modal_employee .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_employee';
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'employee/employee_u_group_js', $param, TRUE);
        $app->js_set($js);
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'employee/employee_basic_function_js',$param,TRUE);
        $app->js_set($js);
        //</editor-fold>
    }

    public static function employee_status_log_render($app,$form,$data,$path){
        $config=array(
            'module_name'=>'employee',
            'module_engine'=>'employee_engine',
            'id'=>$data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }
    
    public static function u_group_log_render($app,$pane, $data){
        //<editor-fold defaultstate="collapsed">
        $log_data = array();
        $db  = new DB();
        $q = '
            select distinct
                eugl.*,
                ug.name ug_name,
                e.username,
                ug.app_name
            from employee_u_group_log eugl
                inner join u_group ug on eugl.u_group_id = ug.id
                inner join employee e on eugl.modid = e.id 
            where eugl.employee_id = '.$db->escape($data['id']).'
            order by eugl.id desc
            limit 20
        ';
        $rs = $db->query_array($q);

        if(count($rs)>0){
            for($i = 0;$i<count($rs);$i++){
                $rs[$i]['row_num'] = $i+1;
                $rs[$i]['moddate'] = Tools::_date($rs[$i]['moddate'],'F d, Y H:i:s');
                $rs[$i]['app_name'] = SI::type_get('ICES_Engine',$rs[$i]['app_name'],'$app_list')['text'];
            }
            $log_data = $rs;
        }
        
        $table = $pane->form_group_add()->table_add();
        $table->table_set('id','employee_u_group_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Modified Date",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"app_name","label"=>"APP Name",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"ug_name","label"=>"User Group",'col_attrib'=>array()));
        $table->table_set('columns',array("name"=>"username","label"=>"User",'col_attrib'=>array()));
        $table->table_set('data',$log_data);
        
        //</editor-fold>
    }
    
}
    
?>