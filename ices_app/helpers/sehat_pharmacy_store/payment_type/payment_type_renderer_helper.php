<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Payment_Type_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'payment_type/payment_type_engine');
        //</editor-fold>
    }

    public static function modal_payment_type_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Payment Type'), 'icon' => 'fa fa-cogs'));
        $modal->width_set('95%');
        $components = self::payment_type_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function payment_type_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Payment_Type_Engine::path_get();

        $id_prefix = Payment_Type_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::payment_type_components_render($app, $form, false);
        $back_href = $path->index;

        $form->button_add()->button_set('value', 'BACK')
                ->button_set('icon', App_Icon::btn_back())
                ->button_set('href', $back_href)
                ->button_set('class', 'btn btn-default')
        ;

        $js = '
            <script>
                $("#' . $id_prefix . '_method").val("' . $method . '");
                $("#' . $id_prefix . '_id").val("' . $id . '");
            </script>
        ';
        $app->js_set($js);

        $js = '                
                ' . $id_prefix . '_init();
                ' . $id_prefix . '_bind_event();
                ' . $id_prefix . '_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function payment_type_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
        $path = Payment_Type_Engine::path_get();
        $components = array();
        $db = new DB();

        $bool_arr = array(
            array('id'=>1,'text'=>SI::get_status_attr('TRUE')),
            array('id'=>0,'text'=>SI::get_status_attr('FALSE')),
        );
        
        $id_prefix = Payment_Type_Engine::$prefix_id;

        $form->input_add()->input_set('id', $id_prefix . '_id')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('id', $id_prefix . '_method')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('id', $id_prefix . '_code')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $form->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('id', $id_prefix . '_name')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true);

        $form->input_select_add()
                ->input_select_set('label', 'Customer - Bank Account')
                ->input_select_set('icon', APP_ICON::bank_account())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_customer_bank_account')
                ->input_select_set('data_add', $bool_arr)
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Supplier - Bank Account')
                ->input_select_set('icon', APP_ICON::bank_account())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_supplier_bank_account')
                ->input_select_set('data_add', $bool_arr)
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'BOS Bank Account Default')
                ->input_select_set('icon', APP_ICON::bank_account())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_bos_bank_account_default')
                ->input_select_set('data_add', BOS_Bank_Account_Data_Support::input_select_bos_bank_account_list_get())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Change Amount')
                ->input_select_set('icon', APP_ICON::money())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_change_amount')
                ->input_select_set('data_add', $bool_arr)
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_payment_type_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
        $form->textarea_add()->textarea_set('label','Notes')
                ->textarea_set('id',$id_prefix.'_notes')
                ->textarea_set('value','')
                ->textarea_set('hide_all',true)
                ->textarea_set('disable_all',true)
            ;

        $form->hr_add()->hr_set('class', '');

        $form->button_add()->button_set('value', 'Submit')
                ->button_set('id', $id_prefix . '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;

        $param = array(
            'ajax_url' => $path->index . 'ajax_search/',
            'index_url' => $path->index,
            'detail_tab' => '#detail_tab',
            'view_url' => $path->index . 'view/',
            'window_scroll' => 'body',
            'data_support_url' => $path->index . 'data_support/',
            'common_ajax_listener' => ICES_Engine::$app['app_base_url'] . 'common_ajax_listener/',
            'component_prefix_id' => $id_prefix,
            'bool_arr'=>$bool_arr,
            
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_payment_type .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_payment_type';
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'payment_type/payment_type_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function payment_type_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'payment_type',
            'module_engine' => 'payment_type_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }

}

?>