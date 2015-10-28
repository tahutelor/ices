<?php
class Smart_Search_Data_Support {
    static function helper_init(){
        //<editor-fold defaultstate="collapsed">
        $ices = SI::type_get('ICES_Engine','ices','$app_list');
        get_instance()->load->helper($ices['app_base_dir'].'smart_search/smart_search_engine');
        //</editor-fold>
    }
    

}
?>
