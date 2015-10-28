<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Supplier_Renderer {

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_engine');
        //</editor-fold>
    }

    public static function modal_supplier_render($app, $modal) {
        //<editor-fold defaultstate="collapsed">
        $modal->header_set(array('title' => Lang::get('Supplier'), 'icon' => APP_ICON::supplier()));
        $modal->width_set('95%');
        $modal->footer_attr_set(array('style'=>'display:none'));
        $components = self::supplier_components_render($app, $modal, true);
        //</editor-fold>
    }

    public static function supplier_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        $path = Supplier_Engine::path_get();

        $id_prefix = Supplier_Engine::$prefix_id;

        $id = $data['id'];
        $components = self::supplier_components_render($app, $form, false);
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

    public static function supplier_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        $path = Supplier_Engine::path_get();
        $components = array();
        $db = new DB();

        $id_prefix = Supplier_Engine::$prefix_id;

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
                ->input_select_set('label', 'Status')
                ->input_select_set('icon', 'fa fa-info')
                ->input_select_set('min_length', '0')
                ->input_select_set('id', $id_prefix . '_supplier_status')
                ->input_select_set('data_add', array())
                ->input_select_set('value', array())
                ->input_select_set('hide_all', true)
                ->input_select_set('is_module_status', true);
        
        $form->input_add()->input_set('label', Lang::get('Supplier Debit'))
                ->input_set('id', $id_prefix . '_supplier_debit_amount')
                ->input_set('icon', APP_ICON::dollar())
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
                ->input_set('attrib', array('style' => ''))
        ;
        
        $form->input_add()->input_set('label', Lang::get('Supplier Credit'))
                ->input_set('id', $id_prefix . '_supplier_credit_amount')
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
            
            
        );

        if ($is_modal) {
            $param['detail_tab'] = '#modal_supplier .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_supplier';
        }


        

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_mail_address_js', $param, TRUE);
        $app->js_set($js);

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_address_js', $param, TRUE);
        $app->js_set($js);

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_phone_number_js', $param, TRUE);
        $app->js_set($js);

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'supplier/supplier_basic_function_js', $param, TRUE);
        $app->js_set($js);
        
        return $components;
        //</editor-fold>
    }

    public static function supplier_status_log_render($app, $form, $data, $path) {
        $config = array(
            'module_name' => 'supplier',
            'module_engine' => 'supplier_engine',
            'id' => $data['id']
        );
        SI::form_renderer()->status_log_tab_render($form, $config);
    }
    
    public static function supplier_debit_credit_amount_log_render($app, $form, $data, $path,$module_type_val){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'supplier','class_name'=>'supplier_engine'));
        SI::module()->load_class(array('module'=>'purchase_invoice','class_name'=>'purchase_invoice_engine'));
        
        $module_type_text = $module_type_val === 'debit'?'Debit':'Credit';
        $id = $data['id'];
        $db = new DB();
        $q = '
            select null row_num
                ,sdal.moddate
                ,sdal.old_amount
                ,sdal.amount
                ,sdal.new_amount
                ,sdal.description

            from supplier_'.$module_type_val.'_amount_log sdal
            where sdal.supplier_id = '.$id.'
                order by sdal.moddate desc
            limit 100
        ';
        $rs = $db->query_array($q);
        for($i = 0;$i<count($rs);$i++){
            $rs[$i]['row_num'] = $i+1;
            $rs[$i]['old_amount'] = Tools::thousand_separator($rs[$i]['old_amount']);
            $rs[$i]['amount'] = Tools::thousand_separator($rs[$i]['amount']);
            $rs[$i]['new_amount'] = Tools::thousand_separator($rs[$i]['new_amount']);
            $rs[$i]['description'] = SI::form_data()->log_description_translate($rs[$i]['description']);

        }
        $customer_status_log = $rs;

        $table = $form->form_group_add()->table_add();
        $table->table_set('id','supplier_debit_amount_log_table');
        $table->table_set('class','table fixed-table');
        $table->table_set('columns',array("name"=>"row_num","label"=>"#",'col_attrib'=>array('style'=>'width:30px')));
        $table->table_set('columns',array("name"=>"moddate","label"=>"Modified Date",'col_attrib'=>array('style'=>'')));
        $table->table_set('columns',array("name"=>"old_amount","label"=>"Old ".$module_type_text." Amount",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"amount","label"=>"&Delta; Amount",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"new_amount","label"=>"New ".$module_type_text." Amount",'attribute'=>'style="text-align:right"','col_attrib'=>array('style'=>'text-align:right')));
        $table->table_set('columns',array("name"=>"description","label"=>"Description",'col_attrib'=>array('style'=>'')));
        
        $table->table_set('data',$customer_status_log);
        //</editor-fold>
    }
    
    

}

?>