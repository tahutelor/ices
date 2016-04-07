<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Download_Excel{
    function __construct(){
        
    }
    
    public function download($param = array()){
        //<editor-fold defaultstate="collapsed">
        
        $module = $param['module'];
        $class_engine_name = $param['class_engine_name'];
        $class_data_support_name = $param['class_data_support_name'];
        $class_download_name = $param['class_download_name'];
        $t_param = $param['param'];
        
        switch($module['type']){
            case 'free_excel':                
                if(method_exists($class_download_name, $module['val'])){
                    $download_param = $t_param;
                    $class_download_name::$module['val']($download_param);
                }
                break;
            case 'simple_table':
                //<editor-fold defaultstate="collapsed">                
                $ajax_tbl_data = Tools::_arr(isset($t_param['ajax_tbl_data'])?$t_param['ajax_tbl_data']:array());
                $search_param = array(
                    'data'=>Tools::_str(isset($ajax_tbl_data['data'])?$ajax_tbl_data['data']:''),
                    'additional_filter'=>Tools::_arr(isset($ajax_tbl_data['additional_filter'])?$ajax_tbl_data['additional_filter']:''),
                    'records_page'=>Tools::_str(isset($ajax_tbl_data['records_page'])?$ajax_tbl_data['records_page']:'99999'),
                    'page'=>Tools::_str(isset($ajax_tbl_data['page'])?$ajax_tbl_data['page']:'1'),
                );
                
                $excel = new Excel();
                $title = SI::type_get($class_engine_name,$module['val'])['label'];
                $excel::file_info_set('title',$title);
                $excel::array_to_text(array($title),'A1',0);
                
                $col_header = array();
                foreach($module['tbl_col'] as $i => $col){
                    $col_header[] = array(
                        'val'=>SI::html_untag($col['label']),
                        'style'=>array(
                            'font'=>array(),
                            'alignment'=>array('horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT)
                        ),
                    );

                }
                
                $ajax_tbl_result = eval('return '.$class_data_support_name.'::'.$module['val'].'_search($search_param);');

                $data_arr = array();
                $t_data = $ajax_tbl_result['data'];
                unset($ajax_tbl_result);

                if(count($t_data)>0){
                    $f_data = array();
                    for($i = 0;$i<count($t_data);$i++){
                        $t_row = array();
                        foreach($module['tbl_col'] as $col_i=>$col){
                            $t_row[] = isset($t_data[$i][$col['name']])?SI::html_untag($t_data[$i][$col['name']]):'';
                        }
                        $f_data[] = $t_row;
                    }
                    $excel::array_to_text_smart(array('column_header'=>$col_header,'data'=>$f_data),'A4',0);
                }
                
                
                $excel::save($title.' '.(string)Date('Ymd His'));
                //</editor-fold>
                break;
        }
        
        //</editor-fold>
    }
    
}

?>