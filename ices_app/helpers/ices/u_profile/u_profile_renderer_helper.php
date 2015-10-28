<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class U_Profile_Renderer {

    public static function modal_u_profile_render($app, $modal) {
        $modal->header_set(array('title' => 'User Profile', 'icon' => App_Icon::u_profile()));
        $components = self::u_profile_components_render($app, $modal, true);
    }

    public static function u_profile_render($app, $form, $data, $path, $method) {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'u_profile/u_profile_engine');
        $path = U_Profile_Engine::path_get();
        $id = $data['id'];
        $components = self::u_profile_components_render($app, $form, false);
        $back_href = $path->index;

        $js = '
            <script>
                $("#u_profile_method").val("' . $method . '");
                $("#u_profile_id").val("' . $id . '");
            </script>
        ';
        $app->js_set($js);

        $js = '                
                u_profile_init();
                u_profile_bind_event();
                u_profile_components_prepare(); 
        ';
        $app->js_set($js);
        //</editor-fold>
    }

    public static function u_profile_components_render($app, $form, $is_modal) {
        //<editor-fold defaultstate="collapsed">
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'u_profile/u_profile_engine');
        $path = U_Profile_Engine::path_get();
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'] . 'employee/employee_engine');
        $path_employee = Employee_Engine::path_get();
        get_instance()->load->helper($path_employee->employee_data_support);

        $components = array();
        $db = new DB();

        $id_prefix = 'u_profile';

        $components['id'] = $form->input_add()->input_set('id', $id_prefix . '_id')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;

        $form->input_add()->input_set('id', $id_prefix . '_method')
                ->input_set('hide', true)
                ->input_set('value', '')
        ;
        $db = new DB();

        $temp = Employee_Data_Support::employee_get(User_Info::get()['user_id']);
        $user_login = isset($temp['employee']) ? $temp['employee'] : array();

        $form->input_add()->input_set('label', Lang::get('User Name'))
                ->input_set('id', $id_prefix . '_username')
                ->input_set('icon', 'fa fa-info')
                ->input_set('attrib', array('disabled' => '', 'style' => 'font-weight:bold'))
                ->input_set('value', $user_login['username'])
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;

        $form->input_add()->input_set('label', Lang::get('First Name'))
                ->input_set('id', $id_prefix . '_firstname')
                ->input_set('icon', 'fa fa-info')
                ->input_set('attrib', array('disabled' => '', 'style' => ''))
                ->input_set('value', $user_login['firstname'])
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;

        $form->input_add()->input_set('label', Lang::get('Last Name'))
                ->input_set('id', $id_prefix . '_lastname')
                ->input_set('icon', 'fa fa-info')
                ->input_set('attrib', array('disabled' => '', 'style' => ''))
                ->input_set('value', $user_login['lastname'])
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;

        $form->input_add()->input_set('label', Lang::get('Password'))
                ->input_set('id', $id_prefix . '_password')
                ->input_set('icon', 'fa fa-info')
                ->input_set('attrib', array('disabled' => '', 'style' => ''))
                ->input_set('value', $user_login['password'])
                ->input_set('hide_all', true)
                ->input_set('disable_all', true)
        ;

        $form->hr_add()->hr_set('class', '');

        $form->button_add()->button_set('value', 'Submit')
                ->button_set('id', $id_prefix . '_btn_submit')
                ->button_set('icon', App_Icon::detail_btn_save())
        ;

        $param = array(
            'ajax_url' => $path->index . 'ajax_search/'
            , 'index_url' => $path->index
            , 'detail_tab' => '#' . $id_prefix
            , 'view_url' => $path->index . ''
            , 'window_scroll' => 'body'
            , 'data_support_url' => $path->index . 'data_support/'
            , 'common_ajax_listener' => ICES_Engine::$app['app_base_url'] . 'common_ajax_listener/'
            , 'component_prefix_id' => $id_prefix
        );



        if ($is_modal) {
            $param['detail_tab'] = '#modal_' . $id_prefix . ' .modal-body';
            $param['view_url'] = '';
            $param['window_scroll'] = '#modal_' . $id_prefix;
        }

        $js = get_instance()->load->view(ICES_Engine::$app['app_base_dir'] . 'u_profile/' . $id_prefix . '_basic_function_js', $param, TRUE);
        $app->js_set($js);
        return $components;
        //</editor-fold>
    }

}

?>