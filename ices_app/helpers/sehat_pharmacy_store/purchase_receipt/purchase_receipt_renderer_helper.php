<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Purchase_Receipt_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'purchase_receipt/purchase_receipt_engine');
        //</editor-fold>
    }

    public static function modal_purchase_receipt_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Purchase Receipt'), 'icon' => APP_ICON::purchase_receipt()));
        $modal->width_set('75%');
        $modal->footer_attr_set(array('style'=>'display:none'));
        $components = self::purchase_receipt_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function purchase_receipt_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Receipt_Engine::path_get();
        
        $id_prefix = Purchase_Receipt_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::purchase_receipt_components_render($app, $form, false);
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

    public static function purchase_receipt_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Purchase_Receipt_Engine::path_get();
        SI::module()->load_class(array('module'=>'store','class_name'=>'store_data_support'));
        SI::module()->load_class(array('module'=>'bos_bank_account','class_name'=>'bos_bank_account_data_support'));
        SI::module()->load_class(array('module'=>'payment_type','class_name'=>'payment_type_data_support'));
        $components = array();
        $db = new DB();

        $id_prefix = Purchase_Receipt_Engine::$prefix_id;

        $form->input_add()->input_set('id', $id_prefix . '_id')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('id', $id_prefix . '_method')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_select_add()
                ->input_select_set('label', 'Store')
                ->input_select_set('icon', APP_ICON::store())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_store')
                ->input_select_set('data_add', Store_Data_Support::input_select_store_list_get())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
                ->input_select_set('allow_empty', false)
        ;
        
        $form->input_add()->input_set('label', Lang::get('Code'))
                ->input_set('id', $id_prefix . '_code')
                ->input_set('icon', APP_ICON::info())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => 'font-weight:bold'))
        ;
        
        $form->input_select_detail_add()
            ->input_select_set('icon',App_Icon::reference())
            ->input_select_set('label',' Reference')
            ->input_select_set('id',$id_prefix.'_reference')
            ->input_select_set('min_length','0')
            ->input_select_set('data_add',array())
            ->input_select_set('value',array())
            ->input_select_set('ajax_url',$path->ajax_search.'/input_select_reference_search/')
            ->input_select_set('disable_all',true)
            ->input_select_set('hide_all',true)
            ->input_select_set('allow_empty', false)
            ->detail_set('rows',array())
            ->detail_set('id',$id_prefix.'_reference_detail')
            ->detail_set('ajax_url','')
        ;
        
        $form->input_select_add()
                ->input_select_set('label', Lang::get('Payment Type'))
                ->input_select_set('icon', APP_ICON::payment_type())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_payment_type')
                ->input_select_set('data_add', Payment_Type_Data_Support::input_select_payment_type_list_get())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
                ->input_select_set('allow_empty', false)
        ;
        
        $form->datetimepicker_add()->datetimepicker_set('label', Lang::get(array('Purchase Receipt','Date')))
                ->datetimepicker_set('id', $id_prefix . '_purchase_receipt_date')
                ->datetimepicker_set('value', Tools::_date('', 'F d, Y H:i'))
                ->datetimepicker_set('disable_all', true)
                ->datetimepicker_set('hide_all', true)
                ->datetimepicker_set('allow_empty',false)
        ;
        
        $form->input_select_add()
                ->input_select_set('label', 'BOS Bank Account')
                ->input_select_set('icon', APP_ICON::bank_account())
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_bos_bank_account')
                ->input_select_set('data_add', BOS_Bank_Account_Data_Support::input_select_bos_bank_account_list_get())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('disable_all', true)
                ->input_select_set('allow_empty', false)
                
        ;
        
        $form->input_add()->input_set('label', Lang::get('Supplier Bank Account'))
                ->input_set('id', $id_prefix . '_supplier_bank_account')
                ->input_set('icon', APP_ICON::bank_account())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
        ;        
        
        
        $form->input_select_add()
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_purchase_receipt_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
        $form->input_add()->input_set('label', Lang::get('Amount'))
                ->input_set('id', $id_prefix . '_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                ->input_set('is_numeric',true)
        ;
        
        
        $form->input_add()->input_set('label', Lang::get('Change Amount'))
                ->input_set('id', $id_prefix . '_change_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
                ->input_set('is_numeric',true)
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
            
            
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_purchase_receipt .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_purchase_receipt';
        }
        
        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'purchase_receipt/purchase_receipt_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function purchase_receipt_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'purchase_receipt',
            'module_engine' => 'purchase_receipt_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }

}

?>