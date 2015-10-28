<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'controllers/ices/app_notification.php',
    'src_class' => 'App_Notification',
    'src_extends_class' => '',
    'dst_class' => 'App_Notification_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class App_Notification extends App_Notification_Parent {
    function __construct() {
        parent::__construct();

    }
}