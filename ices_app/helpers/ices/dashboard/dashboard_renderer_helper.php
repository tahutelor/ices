<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dashboard_Renderer {

    public static function dashboard_render($app, $pane) {
        //<editor-fold defaultstate="collpased">
        $left_section = $pane->section_add()->section_set('class', 'col-lg-6 connectedSortable ui-sortable');
        $right_section = $pane->section_add()->section_set('class', 'col-lg-6 connectedSortable ui-sortable');
        
        //</editor-fold>
    }

}

?>