<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/dashboard/dashboard_renderer_helper.php',
    'src_class' => 'Dashboard_Renderer',
    'dst_class' => 'Dashboard_Renderer_Parent',
);
my_load_and_rename_class($my_param);

class Dashboard_Renderer extends Dashboard_Renderer_Parent {
    
    public static function dashboard_render($app, $pane){
        //<editor-fold defaultstate="collapsed">
                
        if (Security_Engine::get_controller_permission(ICES_Engine::$app['val'],
                User_Info::get()['user_id']
                , 'dashboard', 'weekly_sales_invoice')) {

            self::weekly_sales_invoice_render($app, $pane);
        }
            
        //</editor-fold>
    }
    
    public static function weekly_sales_invoice_render($app, $pane) {
        //<editor-fold defaultstate="collapsed">
        $app->add_library(array('type'=>'js','val'=>'raphael/2.1.0/raphael-min.js'));
        $app->add_library(array('type'=>'css','val'=>'morris/morris.css'));
        $app->add_library(array('type'=>'js','val'=>'morris/morris.js'));
        
        
        $weekly_sales_invoice = $pane->div_add()->div_set('class','col-md-6')
            ->dashboard_component_add();
        $weekly_sales_invoice->properties_set('id', 'weekly_sales_invoice')
                ->module_name_set('weekly_sales_invoice')
                ->header_set('icon', APP_Icon::sales_invoice())
                ->header_set('title', 'Weekly Sales Invoice')
                ->body_set('style','height:250px')
        ;
        
        $div_content = $weekly_sales_invoice->div_add()
            ->div_set('attrib',array('style'=>'height: 200px;'))
                ->div_set('id','weekly_sales_invoice_content')
                ->div_set('class','chart')
        ;
        
        
        
        //</editor-fold>
    }

}

?>