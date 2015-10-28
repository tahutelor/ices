<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Customer_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'customer/customer_engine');
        //</editor-fold>
    }

    public static function modal_customer_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Customer'), 'icon' => APP_ICON::customer()));
        $modal->width_set('95%');
        $modal->footer_attr_set(array('style'=>'display:none'));
        $components = self::customer_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function customer_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();

        $id_prefix = Customer_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::customer_components_render($app, $form, false);
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

    public static function customer_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Customer_Engine::path_get();
        $components = array();
        $db = new DB();

        $id_prefix = Customer_Engine::$prefix_id;

        $bool_arr = array(
            array('id'=>1,'text'=>SI::get_status_attr('TRUE')),
            array('id'=>0,'text'=>SI::get_status_attr('FALSE')),
        );
        
        $components['id'] = $form->input_add()->input_set('id', $id_prefix . '_id')
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

        $form->input_select_add()
                ->input_select_set('label', Lang::get('Customer Type'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_customer_type')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Name'))
                ->input_set('id', $id_prefix . '_name')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true);

        $form->datetimepicker_add()->datetimepicker_set('label', Lang::get('Birthdate'))
                ->datetimepicker_set('id', $id_prefix . '_birthdate')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
                ->datetimepicker_set('allow_empty',true)
        ;

        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_mail_address')
                ->main_div_set('class', 'form-group hide_all')
                ->label_set('value', '')
                ->table_input_set('columns', array(
                    'col_name' => 'mail_address'
                    , 'th' => array('val' => 'Mail Address', 'visible' => true)
                    , 'td' => array('val' => '', 'tag' => 'input', 'class' => 'form-control', 'visible' => true, 'attr' => array('type' => 'email')
                    )
                ))
        ;

        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_address')
                ->main_div_set('class', 'form-group hide_all')
                ->label_set('value', '')
                ->table_input_set('columns', array(
                    'col_name' => 'address'
                    , 'th' => array('val' => 'Address', 'visible' => true)
                    , 'td' => array('val' => '', 'tag' => 'textarea', '', 'attr' => array('rows' => 3, 'style' => 'width:100%'), 'class' => 'form-control', 'visible' => true
                    )
                ))
        ;

        $form->table_input_add()->table_input_set('id', $id_prefix . '_tbl_phone_number')
                ->main_div_set('class', 'form-group hide_all')
                ->label_set('value', '')
                ->table_input_set('columns', array(
                    'col_id_exists' => true
                    ,'col_name' => 'phone_number_type'
                    , 'th' => array('val' => 'Phone Number Type', 'visible' => true)
                    , 'td' => array('val' => '', 'tag' => 'input', 'attr' => array('original' => ''), 'class' => '', 'visible' => true
                    )
                ))
                ->table_input_set('columns', array(
                    'col_name' => 'phone_number'
                    , 'th' => array('val' => 'Phone Number', 'visible' => true)
                    , 'td' => array('val' => '', 'tag' => 'input', 'class' => 'form-control', 'visible' => true
                    )
                ))
        ;

        $form->input_select_add()
                ->input_select_set('label', Lang::get('Sales Invoice - Customer Default'))
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_si_customer_default')
                ->input_select_set('data_add', $bool_arr)
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('allow_empty',false)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_customer_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
        $form->input_add()->input_set('label', Lang::get('Customer Debit'))
                ->input_set('id', $id_prefix . '_customer_debit_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
        ;
        
        $form->input_add()->input_set('label', Lang::get('Customer Credit'))
                ->input_set('id', $id_prefix . '_customer_credit_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
        ;
        
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
            'bool_arr'=>$bool_arr
            
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_customer .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_customer';
        }


        

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'customer/customer_mail_address_js', $param, TRUE);
        $app->js_set($js);

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'customer/customer_address_js', $param, TRUE);
        $app->js_set($js);

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'customer/customer_phone_number_js', $param, TRUE);
        $app->js_set($js);

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'customer/customer_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function customer_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'customer',
            'module_engine' => 'customer_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }

}

?>