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

    

}

?>