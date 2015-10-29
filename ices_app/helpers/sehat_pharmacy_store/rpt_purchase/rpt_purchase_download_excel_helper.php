<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Rpt_Purchase_Download_Excel {

    public static function purchase_invoice($param = array()) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module' => 'rpt_purchase', 'class_name' => 'rpt_purchase_data_support'));
        SI::module()->load_class(array('module' => 'purchase_invoice', 'class_name' => 'purchase_invoice_engine'));

        $start_date = Tools::_str((isset($param['start_date']) ? Tools::_str($param['start_date']) : ''));
        $end_date = Tools::_str((isset($param['end_date']) ? Tools::_str($param['end_date']) : ''));
        $purchase_invoice_status = Tools::_str(isset($param['purchase_invoice_status']) ? Tools::_str($param['purchase_invoice_status']) : '');
        $supplier_id = Tools::_str(isset($param['supplier_id']) ? Tools::_str($param['supplier_id']) : '');

        $excel = new Excel();
        $param = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'purchase_invoice_status' => $purchase_invoice_status,
            'supplier_id' => $supplier_id,
        );
        $purchase_invoice = Rpt_Purchase_Data_Support::purchase_invoice_get($param);

        $excel::$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'size' => 9,
                        'name' => 'Calibri',
                        'color' => array('rgb' => '002060'),
                    )
                )
        );

        $excel::$objPHPExcel->getDefaultStyle()->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $title = Lang::get('Purchase Invoice') . ' ' . Tools::_date($start_date, 'F d, Y') . ' s/d ' . Tools::_date($end_date, 'F d, Y');
        $excel::file_info_set('title', $title);
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

        $excel::$objPHPExcel->getActiveSheet()->getStyle('A' . $active_row . ':' . 'J' . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel::array_to_text(array('No', Lang::get('Code'), Lang::get(array('Purchase Invoice', 'Date')), Lang::get('Supplier'), Lang::get('Grand Total Amount'), Lang::get('Outstanding Grand Total Amount'), Lang::get('Status')), 'A' . $active_row, 0);
        $active_row+=1;

        $pi_start_row = $active_row;
        $pi_end_row = $pi_start_row;
        foreach ($purchase_invoice as $pi_idx => $pi_row) {
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($pi_idx + 1), 'A' . $active_row, 0);

            $code = $pi_row['code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("B" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($code), 'B' . $active_row, 0);

            $curr_date = Tools::_date($pi_row['purchase_invoice_date'], 'F d, Y H:i:s');
            $excel::$objPHPExcel->getActiveSheet()->getStyle("C" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($curr_date), 'C' . $active_row, 0);

            $supplier_name = $pi_row['supplier_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($supplier_name), 'D' . $active_row, 0);

            $grand_total_amount = $pi_row['grand_total_amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($grand_total_amount), 'E' . $active_row, 0);

            $outstanding_grand_total_amount = $pi_row['outstanding_grand_total_amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($outstanding_grand_total_amount), 'F' . $active_row, 0);

            $purchase_invocie_status = SI::type_get('purchase_invoice_engine', $pi_row['purchase_invoice_status'], '$status_list')['text'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($purchase_invocie_status), 'G' . $active_row, 0);

            $pi_end_row = $active_row;
            $active_row+=1;
        }

        if (count($purchase_invoice) > 0) {
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);

            $excel::array_to_text(array('=sum(E' . $pi_start_row . ':E' . $pi_end_row . ')'), 'E' . $active_row, 0);
            $excel::array_to_text(array('=sum(F' . $pi_start_row . ':F' . $pi_end_row . ')'), 'F' . $active_row, 0);
        }
        $excel::save(Lang::get('Purchase Invoice') . ' ' . Tools::_str(Tools::_date($start_date, 'Ymd')) . '-' . Tools::_str(Tools::_date($end_date, 'Ymd')) . ' ' . (string) Date('Ymd His'));
        //</editor-fold>
    }

    public static function purchase_receipt($param = array()) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module' => 'rpt_purchase', 'class_name' => 'rpt_purchase_data_support'));
        SI::module()->load_class(array('module' => 'purchase_receipt', 'class_name' => 'purchase_receipt_engine'));

        $start_date = Tools::_str((isset($param['start_date']) ? Tools::_str($param['start_date']) : ''));
        $end_date = Tools::_str((isset($param['end_date']) ? Tools::_str($param['end_date']) : ''));
        $purchase_receipt_status = Tools::_str(isset($param['purchase_receipt_status']) ? Tools::_str($param['purchase_receipt_status']) : '');

        $excel = new Excel();
        $param = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'purchase_receipt_status' => $purchase_receipt_status,
        );
        $purchase_receipt = Rpt_Purchase_Data_Support::purchase_receipt_get($param);

        $excel::$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'size' => 9,
                        'name' => 'Calibri',
                        'color' => array('rgb' => '002060'),
                    )
                )
        );

        $excel::$objPHPExcel->getDefaultStyle()->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $title = Lang::get('Purchase Invoice') . ' ' . Tools::_date($start_date, 'F d, Y') . ' s/d ' . Tools::_date($end_date, 'F d, Y');
        $excel::file_info_set('title', $title);
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

        $excel::$objPHPExcel->getActiveSheet()->getStyle('A' . $active_row . ':' . 'J' . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel::array_to_text(array('No', Lang::get('Code'), Lang::get(array('Purchase Receipt', 'Date')), Lang::get('Purchase Invoice Code'), Lang::get('Supplier'), Lang::get('Amount'), Lang::get('Status')), 'A' . $active_row, 0);
        $active_row+=1;

        $pi_start_row = $active_row;
        $pi_end_row = $pi_start_row;
        foreach ($purchase_receipt as $pi_idx => $pi_row) {
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($pi_idx + 1), 'A' . $active_row, 0);

            $code = $pi_row['code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("B" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($code), 'B' . $active_row, 0);

            $curr_date = Tools::_date($pi_row['purchase_receipt_date'], 'F d, Y H:i:s');
            $excel::$objPHPExcel->getActiveSheet()->getStyle("C" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($curr_date), 'C' . $active_row, 0);

            $purchase_invoice_code = $pi_row['purchase_invoice_code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($purchase_invoice_code), 'D' . $active_row, 0);

            $supplier_name = $pi_row['supplier_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($supplier_name), 'E' . $active_row, 0);

            $amount = $pi_row['amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($amount), 'F' . $active_row, 0);

            $purchase_invocie_status = SI::type_get('purchase_receipt_engine', $pi_row['purchase_receipt_status'], '$status_list')['text'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($purchase_invocie_status), 'G' . $active_row, 0);

            $pi_end_row = $active_row;
            $active_row+=1;
        }

        if (count($purchase_receipt) > 0) {
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);


            $excel::array_to_text(array('=sum(F' . $pi_start_row . ':F' . $pi_end_row . ')'), 'F' . $active_row, 0);
        }
        $excel::save(Lang::get('Purchase Receipt') . ' ' . Tools::_str(Tools::_date($start_date, 'Ymd')) . '-' . Tools::_str(Tools::_date($end_date, 'Ymd')) . ' ' . (string) Date('Ymd His'));
        //</editor-fold>
    }

    public static function purchase_return($param = array()) {
        //<editor-fold defaultstate="collapsed">
        SI::module()->load_class(array('module' => 'rpt_purchase', 'class_name' => 'rpt_purchase_data_support'));
        SI::module()->load_class(array('module' => 'purchase_return', 'class_name' => 'purchase_return_engine'));

        $start_date = Tools::_str((isset($param['start_date']) ? Tools::_str($param['start_date']) : ''));
        $end_date = Tools::_str((isset($param['end_date']) ? Tools::_str($param['end_date']) : ''));
        $purchase_return_status = Tools::_str(isset($param['purchase_return_status']) ? Tools::_str($param['purchase_return_status']) : '');

        $excel = new Excel();
        $param = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'purchase_return_status' => $purchase_return_status,
        );
        $purchase_return = Rpt_Purchase_Data_Support::purchase_return_get($param);

        $excel::$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray(
                array(
                    'font' => array(
                        'bold' => true,
                        'size' => 9,
                        'name' => 'Calibri',
                        'color' => array('rgb' => '002060'),
                    )
                )
        );

        $excel::$objPHPExcel->getDefaultStyle()->getAlignment()
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $title = Lang::get('Purchase Return') . ' ' . Tools::_date($start_date, 'F d, Y') . ' s/d ' . Tools::_date($end_date, 'F d, Y');
        $excel::file_info_set('title', $title);
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

        $excel::$objPHPExcel->getActiveSheet()->getStyle('A' . $active_row . ':' . 'J' . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $excel::array_to_text(array('No', Lang::get('Code'), Lang::get(array('Purchase Return', 'Date')), Lang::get('Purchase Invoice Code'), Lang::get('Supplier'), Lang::get('Grand Total Amount'), Lang::get('Status')), 'A' . $active_row, 0);
        $active_row+=1;

        $pi_start_row = $active_row;
        $pi_end_row = $pi_start_row;
        foreach ($purchase_return as $pi_idx => $pi_row) {
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($pi_idx + 1), 'A' . $active_row, 0);

            $code = $pi_row['code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("B" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($code), 'B' . $active_row, 0);

            $curr_date = Tools::_date($pi_row['purchase_return_date'], 'F d, Y H:i:s');
            $excel::$objPHPExcel->getActiveSheet()->getStyle("C" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($curr_date), 'C' . $active_row, 0);

            $purchase_invoice_code = $pi_row['purchase_invoice_code'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("D" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($purchase_invoice_code), 'D' . $active_row, 0);

            $supplier_name = $pi_row['supplier_name'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("E" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($supplier_name), 'E' . $active_row, 0);

            $grand_total_amount = $pi_row['grand_total_amount'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("F" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::array_to_text(array($grand_total_amount), 'F' . $active_row, 0);

            $purchase_invocie_status = SI::type_get('purchase_return_engine', $pi_row['purchase_return_status'], '$status_list')['text'];
            $excel::$objPHPExcel->getActiveSheet()->getStyle("G" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $excel::array_to_text(array($purchase_invocie_status), 'G' . $active_row, 0);

            $pi_end_row = $active_row;
            $active_row+=1;
        }

        if (count($purchase_return) > 0) {
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
            $excel::$objPHPExcel->getActiveSheet()->getStyle("A" . $active_row . ':' . "G" . $active_row)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);


            $excel::array_to_text(array('=sum(F' . $pi_start_row . ':F' . $pi_end_row . ')'), 'F' . $active_row, 0);
        }
        $excel::save(Lang::get('Purchase Return') . ' ' . Tools::_str(Tools::_date($start_date, 'Ymd')) . '-' . Tools::_str(Tools::_date($end_date, 'Ymd')) . ' ' . (string) Date('Ymd His'));
        //</editor-fold>
    }

}

?>