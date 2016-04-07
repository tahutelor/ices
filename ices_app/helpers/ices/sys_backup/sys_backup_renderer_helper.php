<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sys_Backup_Renderer {

    public static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'sys_backup/sys_backup_engine');
        //</editor-fold>
    }
    
    public static function modal_sys_backup_render($app,$modal){
        $modal->header_set(array('title'=>'System Backup','icon'=>App_Icon::sys_backup()));
        $components = self::sys_backup_components_render($app, $modal,true);
    }

    public static function sys_backup_render($app,$form,$data,$path,$method){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_ENgine::$app['app_base_dir'].'sys_backup/sys_backup_engine');
        $path = Sys_Backup_Engine::path_get();
        $id = $data['id'];
        $components = self::sys_backup_components_render($app, $form,false);
        $back_href = $path->index;

        $js = '
            <script>
                $("#sys_backup_method").val("'.$method.'");
                $("#sys_backup_id").val("'.$id.'");
            </script>
        ';             
        $app->js_set($js);

        $js = '                
                sys_backup_init();
                sys_backup_bind_event();
                sys_backup_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function sys_backup_components_render($app,$form,$is_modal){
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'sys_backup/sys_backup_engine');
        $path = Sys_Backup_Engine::path_get();            
        $components = array();
        $db = new DB();

        $id_prefix = 'sys_backup';

        $components['id'] = $form->input_add()->input_set('id',$id_prefix.'_id')
                ->input_set('hide',true)
                ->input_set('value','')
                ;

        $reference_detail = array(

        );

        $form->input_add()->input_set('id',$id_prefix.'_method')
                ->input_set('hide',true)
                ->input_set('value','')
                ;            
        $db = new DB();
        
        $module_list =array();
        
        $raw_module_list = SI::type_list_get('Sys_Backup_Engine','$module_list');
        foreach($raw_module_list as $idx=>$row){
            $t_module = array(
                'id'=>$row['val'],
                'text'=>$row['label'],
                'method'=>$row['method'],
                
            );
            $module_list[] = $t_module;
        }
        
        $form->input_select_add()
            ->input_select_set('label',Lang::get('Module'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id',$id_prefix.'_module')
            ->input_select_set('data_add',$module_list)
            ->input_select_set('value',array())
            ->input_select_set('ajax_url','')
            ->input_select_set('disable_all',true)
             ->input_select_set('hide_all',true)
            ->input_select_set('allow_empty',false)

        ;

        $app_list = array();
        $t_app_list = SI::type_list_get('ICES_Engine','$app_list');
        foreach($t_app_list as $idx=>$row){
            $app_list[] = array('id'=>$row['val'],'text'=>$row['dev_text']);
        }
        
        $form->input_select_add()
            ->input_select_set('label',Lang::get('APP Name'))
            ->input_select_set('icon',App_Icon::info())
            ->input_select_set('min_length','0')
            ->input_select_set('id',$id_prefix.'_app_name')
            ->input_select_set('data_add',$app_list)
            ->input_select_set('value',array())
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->input_select_set('allow_empty',false)
        ;
        
        
        $form->hr_add()->hr_set('class','');

        $form->button_add()->button_set('value','Submit')
            ->button_set('id',$id_prefix.'_submit')
            ->button_set('icon',App_Icon::detail_btn_save())
        ;
        
        $param = array(
            'ajax_url' => $path->index . 'ajax_search/',
            'index_url' => $path->index,
            'detail_tab' => '#'.$id_prefix,
            'view_url' => $path->index . 'view/',
            'window_scroll' => 'body',
            'data_support_url' => $path->index . 'data_support/',
            'common_ajax_listener' => ICES_Engine::$app['app_base_url'] . 'common_ajax_listener/',
            'component_prefix_id' => $id_prefix,
            
        );
        


        if($is_modal){
            $param['detail_tab'] = '#modal_'.$id_prefix.' .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_'.$id_prefix;
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'].'sys_backup/'.$id_prefix.'_basic_function_js',$param,TRUE);
        $app->js_set($js);
        
        $app->add_library(array('type'=>'js','val'=>'filedownload/jquery.filedownload.js'));
        
        return $components;
        //</editor-fold>

    }

}
    
?>