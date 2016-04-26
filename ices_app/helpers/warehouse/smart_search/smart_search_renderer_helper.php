<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$my_param = array(
    'file_path' => APPPATH . 'helpers/ices/smart_search_renderer.php',
    'src_class' => 'Smart_Search_Renderer',
    'src_extends_class' => '',
    'dst_class' => 'Smart_Search_Renderer_Parent',
    'dst_extends_class' => '',
);
$my_content = my_load_and_rename_class($my_param);

class Smart_Search_Renderer extends Smart_Search_Renderer_Parent{

}
    
?>