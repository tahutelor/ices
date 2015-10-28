<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Render extends MY_Extended_Controller {
    
    private $title = '';
    private $title_icon = '';
    private $path = array();

    function __construct() {
        parent::__construct();
        $this->title = Lang::get(array('Product'), true, true, false, false, true);
        $this->title_icon = '';
    }

    
    function index(){
        get_instance()->load->helper(ICES_Engine::$app['app_base_dir'].'test/Test_Renderer');
        $app = new APP();
        
        
        $app->set_menu('collapsed', false);
        
        
        $row = $app->engine->div_add()->div_set('class', 'row');

        $nav_tab = $row->div_add()->div_set("span", "12")->nav_tab_add();

        //Test_Renderer::bos_bank_account_render($app,$nav_tab,'BOS Bank Account');
        //Test_Renderer::payment_type_render($app,$nav_tab,'Payment Type');
        
        //Test_Renderer::supplier_render($app,$nav_tab,'Supplier');
        //Test_Renderer::customeddddr_type_render($app,$nav_tab,'Customer Type');
        //Test_Renderer::customer_render($app,$nav_tab,'Customer');
        
        //Test_Renderer::product_render($app,$nav_tab,'Product');
        //Test_Renderer::product_batch_render($app,$nav_tab,'Product Batch');
        //Test_Renderer::product_stock_opname_render($app,$nav_tab,'Product Stock Opname');

        Test_Renderer::purchase_invoice_render($app,$nav_tab,'Purchase Invoice');
        //Test_Renderer::purchase_receipt_render($app,$nav_tab,'Purchase Receipt');
        
        //Test_Renderer::rma_render_add($app,$nav_tab,'Return Merchandise Authorization');
        //Test_Renderer::rma_render_view($app,$nav_tab,'Return Merchandise Authorization');
        
        //Test_Renderer::sales_invoice_render($app,$nav_tab,'Sales Invoice');
        //Test_Renderer::sales_receipt_render($app,$nav_tab,'Sales Receipt');
        
        $app->render();
    }
}