<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/dashboard/dashboard_engine_helper.php',
    'src_class' => 'Dashboard_Engine',
    'dst_class' => 'Dashboard_Engine_Parent',
);
my_load_and_rename_class($my_param);

class Dashboard_Engine extends Dashboard_Engine_Parent {
}

?>
