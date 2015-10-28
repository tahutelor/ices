<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpt_Sales_Download_Excel{
    
    public static function sales_invoice($param = array()){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        SI::module()->load_class(array('module'=>'sales_invoice','class_name'=>'sales_invoice_engine'));
        
        $start_date = Tools::_str((isset($param['start_date'])?Tools::_str($param['start_date']):''));
        $end_date = Tools::_str((isset($param['end_date'])?Tools::_str($param['end_date']):''));
        $sales_invoice_status = Tools::_str(isset($param['sales_invoice_status'])?Tools::_str($param['sales_invoice_status']):'');
        
        $excel = new Excel();
        $param = array(
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'sales_invoice_status'=>$sales_invoice_status,
        );
        $sales_invoice = Rpt_Sales_Data_Support::sales_invoice_get($param);
        
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
        
        $title = Lang::get('Sales Invoice').' '.Tools::_date($start_date,'F d, Y').' s/d '.Tools::_date($end_date,'F d, Y');
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
        
        $active_row = 3;
        
        $excel::$objPHPExcel->getActiveSheet()->getStyle('A'.$active_row.':'.'J'.$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel::array_to_text(array('No',Lang::get('Code'),Lang::get(array('Sales Invoice','Date')),Lang::get('Customer'),Lang::get('Grand Total Amount'),Lang::get('Outstanding Grand Total Amount'),Lang::get('Status')),'A'.$active_row,0);
        $active_row+=1;
        
        $pi_start_row = $active_row;
        $pi_end_row = $pi_start_row;
        foreach($sales_invoice as $pi_idx=>$pi_row){
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($pi_idx+1),'A'.$active_row,0);
            
            $code = $pi_row['code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("B".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($code),'B'.$active_row,0);
            
            $curr_date = Tools::_date($pi_row['sales_invoice_date'],'F d, Y H:i:s');
            $excel::$objPHPExcel->getActiveSheet()->getStyle("C".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($curr_date),'C'.$active_row,0);
            
            $customer_name = $pi_row['customer_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($customer_name),'D'.$active_row,0);
            
            $grand_total_amount = $pi_row['grand_total_amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($grand_total_amount),'E'.$active_row,0);
            
            $outstanding_grand_total_amount = $pi_row['outstanding_grand_total_amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($outstanding_grand_total_amount),'F'.$active_row,0);
            
            $sales_invocie_status = SI::type_get('sales_invoice_engine',$pi_row['sales_invoice_status'],'$status_list')['text'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($sales_invocie_status),'G'.$active_row,0);
            
            $pi_end_row = $active_row;
            $active_row+=1;
        }
        
        if(count($sales_invoice)>0){
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

            $excel::array_to_text(array('=sum(E'.$pi_start_row.':E'.$pi_end_row.')'),'E'.$active_row,0);
            $excel::array_to_text(array('=sum(F'.$pi_start_row.':F'.$pi_end_row.')'),'F'.$active_row,0);
            
        }
        $excel::save(Lang::get('Sales Invoice').' '.Tools::_str(Tools::_date($start_date,'Ymd')).'-'.Tools::_str(Tools::_date($end_date,'Ymd')).' '.(string)Date('Ymd His'));
        //</editor-fold>
    }
    
    public static function sales_receipt($param = array()){
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module'=>'rpt_sales','class_name'=>'rpt_sales_data_support'));
        SI::module()->load_class(array('module'=>'sales_receipt','class_name'=>'sales_receipt_engine'));
        
        $start_date = Tools::_str((isset($param['start_date'])?Tools::_str($param['start_date']):''));
        $end_date = Tools::_str((isset($param['end_date'])?Tools::_str($param['end_date']):''));
        $sales_receipt_status = Tools::_str(isset($param['sales_receipt_status'])?Tools::_str($param['sales_receipt_status']):'');
        
        $excel = new Excel();
        $param = array(
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'sales_receipt_status'=>$sales_receipt_status,
        );
        $sales_receipt = Rpt_Sales_Data_Support::sales_receipt_get($param);
        
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
        
        $title = Lang::get('Sales Invoice').' '.Tools::_date($start_date,'F d, Y').' s/d '.Tools::_date($end_date,'F d, Y');
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
        
        $active_row = 3;
        
        $excel::$objPHPExcel->getActiveSheet()->getStyle('A'.$active_row.':'.'J'.$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel::array_to_text(array('No',Lang::get('Code'),Lang::get(array('Sales Receipt','Date')),Lang::get('Sales Invoice Code'),Lang::get('Customer'),Lang::get('Amount'),Lang::get('Status')),'A'.$active_row,0);
        $active_row+=1;
        
        $pi_start_row = $active_row;
        $pi_end_row = $pi_start_row;
        foreach($sales_receipt as $pi_idx=>$pi_row){
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($pi_idx+1),'A'.$active_row,0);
            
            $code = $pi_row['code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("B".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($code),'B'.$active_row,0);
            
            $curr_date = Tools::_date($pi_row['sales_receipt_date'],'F d, Y H:i:s');
            $excel::$objPHPExcel->getActiveSheet()->getStyle("C".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($curr_date),'C'.$active_row,0);
            
            $sales_invoice_code = $pi_row['sales_invoice_code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($sales_invoice_code),'D'.$active_row,0);
            
            $customer_name = $pi_row['customer_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($customer_name),'E'.$active_row,0);
            
            $amount = $pi_row['amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($amount),'F'.$active_row,0);
            
            $sales_invocie_status = SI::type_get('sales_receipt_engine',$pi_row['sales_receipt_status'],'$status_list')['text'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($sales_invocie_status),'G'.$active_row,0);
            
            $pi_end_row = $active_row;
            $active_row+=1;
        }
        
        if(count($sales_receipt)>0){
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A".$active_row.':'."G".$active_row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

            
            $excel::array_to_text(array('=sum(F'.$pi_start_row.':F'.$pi_end_row.')'),'F'.$active_row,0);
            
        }
        $excel::save(Lang::get('Sales Receipt').' '.Tools::_str(Tools::_date($start_date,'Ymd')).'-'.Tools::_str(Tools::_date($end_date,'Ymd')).' '.(string)Date('Ymd His'));
        //</editor-fold>
    }
    
}

?>