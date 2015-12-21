<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Product_Download_Excel{
    
    public static function product_stock($param = array()){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_purchase','class_name'=>'rpt_purchase_data_support'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_engine'));
        SI::module()->load_class(array('module'=>'product','class_name'=>'product_data_support'));
        SI::module()->load_class(array('module'=>'warehouse','class_name'=>'warehouse_data_support'));
        
        $keyword = Tools::_str((isset($param['keyword'])?Tools::_str($param['keyword']):''));
        $warehouse_id = Tools::_str((isset($param['warehouse_id'])?Tools::_str($param['warehouse_id']):''));
        $product_status = Tools::_str(isset($param['product_status'])?Tools::_str($param['product_status']):'');
        $product_batch_expired = Tools::_str(isset($param['product_batch_expired'])?Tools::_str($param['product_batch_expired']):'');
        
        $excel = new Excel();
        $param = array(
            'keyword'=>$keyword,
            'warehouse_id'=>$warehouse_id,
            'product_status'=>$product_status,
            'product_batch_expired'=>$product_batch_expired,
        );
        $product_stock = Rpt_Product_Data_Support::product_stock_get($param);
        
        $excel::$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray(
            array(
                'font'=>array(
                    'bold'=>true,
                    'size'=>9,
                    'name'=>'Calibri',
                    'color' => array('rgb' => '002060'),
                )
            )
        );
        
        $excel::$objPHPExcel->getDefaultStyle()->getAlignment()
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        
        $warehouse = '';
        $temp = Warehouse_Data_Support::warehouse_get($warehouse_id);
        $warehouse = isset($temp['warehouse'])?$temp['warehouse']['code'].' '.$temp['warehouse']['name']:'';
        
        $title = Lang::get('Product Stock').' - '.$warehouse;
        $excel::file_info_set('title',$title);
        $excel::$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
        $excel::$objPHPExcel->getActiveSheet()->setCellValue('A1', $title);
        $excel::$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(14);
        $excel::$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
        //define column size
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $excel::$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        
        $active_row = 3;
        
        $excel::$objPHPExcel->getActiveSheet()->getStyle('A'.$active_row.':'.'G'.$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel::array_to_text(array('No',Lang::get('Category'),Lang::get(array('Product')),Lang::get('Qty'),Lang::get('Unit'),Lang::get('Purchase Amount'),Lang::get('Sales Amount'),Lang::get('Status')),'A'.$active_row,0);
        $active_row+=1;
        
        $pi_start_row = $active_row;
        $pi_end_row = $pi_start_row;
        foreach($product_stock as $ps_idx=>$ps_row){
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($ps_idx+1),'A'.$active_row,0);
            
            $category = $ps_row['product_category_code'].' '.$ps_row['product_category_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("B".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($category),'B'.$active_row,0);
            
            $product = $ps_row['product_code'].' '.$ps_row['product_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("C".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($product),'C'.$active_row,0);
            
            $qty = $ps_row['qty'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($qty),'D'.$active_row,0);
            
            $unit = $ps_row['unit_code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($unit),'E'.$active_row,0);
            
            
            $purchase_amount = $ps_row['purchase_amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($purchase_amount),'F'.$active_row,0);
            
            $sales_amount = Product_Data_support::sales_amount_get($ps_row['sales_amount']);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($sales_amount),'G'.$active_row,0);
            
            $product_status = SI::type_get('product_engine',$ps_row['product_status'],'$status_list')['text'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("H".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($product_status),'H'.$active_row,0);
            
            $pi_end_row = $active_row;
            $active_row+=1;
        }
        
        $excel::save(Lang::get('Product').' '.' '.(string)Date('Ymd His'));
        //</editor-fold>
    }
    
}

?>