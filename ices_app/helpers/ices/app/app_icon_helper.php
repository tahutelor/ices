<?php

class App_Icon {

    function __construct() {
        
    }

    public static function reference() {
        return 'fa fa-info';
    }
    
    public static function dollar() {
        return 'fa fa-dollar';
    }

    public static function unit() {
        return 'fa fa-adn';
    }

    public static function formula() {
        return 'fa fa-pencil-square-o';
    }

    public static function payment_type() {
        return 'fa fa-info';
    }

    public static function bank_account() {
        return 'fa fa-bank';
    }

    public static function warehouse() {
        return 'fa fa-cubes';
    }

    public static function notification() {
        return 'fa fa-warning';
    }

    public static function location() {
        return 'fa fa-location-arrow';
    }

    public static function phone() {
        return 'fa fa-phone';
    }

    public static function barcode() {
        return 'fa fa-barcode';
    }

    public static function password() {
        return 'fa fa-key';
    }

    public static function submit() {
        return 'fa fa-save';
    }

    public static function cancel() {
        return 'fa fa-times';
    }

    public static function dashboard() {
        return 'fa fa-dashboard';
    }

    public static function smart_search() {
        return array('fa fa-graduation-cap', 'fa fa-search');
    }

    public static function search() {
        return 'fa fa-search';
    }

    public static function store() {
        return 'fa fa-building';
    }

    public static function money() {
        return 'fa fa-money';
    }

    public static function mail() {
        return 'fa fa-envelope';
    }

    public static function bos_bank_account() {
        return 'fa fa-bank';
    }

    public static function customer() {
        return 'fa fa-user';
    }

    public static function supplier() {
        return 'fa fa-user';
    }

    public static function user() {
        return 'fa fa-user';
    }

    public static function u_group() {
        return 'fa fa-group';
    }

    public static function menu() {
        return 'fa fa-align-left';
    }

    public static function customer_type() {
        return 'fa fa-info';
    }

    public static function product() {
        return 'fa fa-cubes';
    }

    public static function product_category() {
        return 'fa fa-info';
    }

    public static function product_batch() {
        return 'fa fa-barcode';
    }

    public static function purchase_invoice() {
        return 'fa fa-list-alt';
    }

    public static function purchase_receipt() {
        return 'fa fa-money';
    }
    
    public static function purchase_return() {
        return array('fa fa-money','fa fa-cubes');
    }
    
    public static function sales_invoice() {
        return 'fa fa-list-alt';
    }
    
    public static function sales_receipt() {
        return 'fa fa-money';
    }
    
    public static function detail_btn_delete() {
        return 'fa fa-times';
    }

    public static function detail_btn_edit() {
        return 'fa fa-pencil-square-o';
    }

    public static function detail_btn_save() {
        return 'fa fa-save';
    }

    public static function detail_btn_download() {
        return 'fa fa-download';
    }

    public static function detail_btn_cancel() {
        return 'fa fa-times';
    }

    public static function detail_btn_back() {
        return 'fa fa-arrow-left';
    }

    public static function printer() {
        return 'fa fa-print';
    }

    public static function btn_add() {
        return 'fa fa-plus';
    }

    public static function btn_back() {
        return 'fa fa-arrow-left';
    }

    public static function btn_save() {
        return 'fa fa-save';
    }

    public static function btn_cancel() {
        return 'fa fa-times';
    }

    public static function btn_refresh() {
        return 'fa fa-refresh';
    }
    
    public static function btn_preview(){
        return 'fa fa-search';
    }
    
    public static function btn_print(){
        return 'fa fa-print';
    }

    public static function sir() {
        return 'fa fa-pencil-square-o';
    }

    public static function request_form() {
        return 'fa fa-calendar-o';
    }

    public static function report() {
        return 'fa fa-bar-chart-o';
    }

    public static function message() {
        return 'fa fa-envelope-o';
    }

    public static function info() {
        return 'fa fa-info';
    }

    public static function contact_category() {
        return 'fa fa-info';
    }

    public static function product_stock_opname() {
        return array('fa fa-pencil','fa fa-cubes');
    }
    
    public static function misc(){
        return 'fa fa-info';
    }
    
    public static function print_form(){
        return 'fa fa-printer';
    }
    
    public static function sys_backup(){
        return 'fa fa-cogs';
    }
    
    public static function html_get($app_icon) {
        $result = '';
        if (is_array($app_icon)) {
            $app_icon_arr = $app_icon;
            foreach ($app_icon_arr as $icon_idx => $icon) {
                $result.='<i class="' . $icon . '"></i> ';
            }
        } else {
            $result.='<i class="' . $app_icon . '"></i>';
        }
        return $result;
    }

}

?>
