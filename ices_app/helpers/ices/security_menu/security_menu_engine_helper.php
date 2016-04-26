<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Security_Menu_Engine {

    static $menu_list;

    public static function helper_init() {
        //<editor-fold defaultstate="collapsed">

        self::$menu_list = array();
        $ices_app_base_url = SI::type_get('ICES_Engine', 'ices', '$app_list')['app_base_url'];
        self::$menu_list['ices'] = array(
            //<editor-fold defaultstate="collapsed" desc="ices menu">
            Lang::get("Dashboard") => array(
                'id' => 'd'
                , "properties" => array(
                    "class" => "fa fa-dashboard"
                )
                , "ref" => $ices_app_base_url . "dashboard"
            )
            , Lang::get("Security") => array(
                'id' => 's'
                , "properties" => array(
                    "class" => "fa fa-cog"
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("User Group") => array(
                        'id' => 's_ug'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $ices_app_base_url . "u_group"
                    )
                    , Lang::get("Employee") => array(
                        'id' => 's_ul',
                        "properties" => array("class" => "fa fa-th")
                        , "ref" => $ices_app_base_url . "employee"
                    )
                    , Lang::get("System") => array(
                        'id' => 's_s',
                        "properties" => array("class" => "fa fa-th")
                        , "ref" => "#"
                        , "child" => array(
                            Lang::get("Controller") => array(
                                'id' => 's_s_cnt',
                                "properties" => array("class" => "fa fa-th")
                                , "ref" => $ices_app_base_url . "security_controller"
                            ),
                            Lang::get("Component") => array(
                                'id' => 's_s_cmp',
                                "properties" => array("class" => "fa fa-th")
                                , "ref" => $ices_app_base_url . "security_component"
                            ),
                            Lang::get("Backup") => array(
                                'id' => 's_s_b',
                                "properties" => array("class" => "fa fa-th")
                                , "ref" => $ices_app_base_url . "sys_backup"
                            )
                        )
                    )
                )
            ),
            Lang::get("Report") => array(
                'id' => 'a'
                , "properties" => array(
                    "class" => APP_ICON::report()
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Simple") => array(
                        'id' => 'a_s'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $ices_app_base_url . 'rpt_simple'
                    ),
                )
            ),
            Lang::get("Fontawesome") => array(
                'id' => 'f'
                , "properties" => array(
                    "class" => "fa fa-info"
                )
                , "ref" => $ices_app_base_url . 'fontawesome'
            )
                //</editor-fold>
        );

        $phone_book_app_base_url = SI::type_get('ICES_Engine', 'aryana_phone_book', '$app_list')['app_base_url'];
        self::$menu_list['aryana_phone_book'] = array(
            //<editor-fold defaultstate="collapsed" desc="ices menu">
            Lang::get("Dashboard") => array(
                'id' => 'd'
                , "properties" => array(
                    "class" => "fa fa-dashboard"
                )
                , "ref" => $phone_book_app_base_url . "dashboard"
            ),
            Lang::get("Master") => array(
                'id' => 'm'
                , "properties" => array(
                    "class" => 'fa fa-th'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Contact Category") => array(
                        'id' => 'm_cc'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $phone_book_app_base_url . "contact_category"
                    ),
                    Lang::get("Phone Number Type") => array(
                        'id' => 'm_pnt'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $phone_book_app_base_url . "phone_number_type"
                    ),
                    Lang::get("Company") => array(
                        'id' => 'm_cpn'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $phone_book_app_base_url . "company"
                    ),
                )
            ),
            Lang::get("List") => array(
                'id' => 'l'
                , "properties" => array(
                    "class" => 'fa fa-list-alt'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Contact") => array(
                        'id' => 'l_c'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $phone_book_app_base_url . "contact"
                    ),
                )
            ),
            Lang::get("Activity") => array(
                'id' => 'a'
                , "properties" => array(
                    "class" => 'fa fa-child'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Contact") => array(
                        'id' => 'a_c'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $phone_book_app_base_url . "contact/add"
                    ),
                )
            ),
                //</editor-fold>
        );


        $mini_warehouse_app_base_url = SI::type_get('ICES_Engine', 'warehouse', '$app_list')['app_base_url'];
        self::$menu_list['warehouse'] = array(
            //  <editor-fold defaultstate="collapsed" desc="mini warehouse menu">
            Lang::get("Dashboard") => array(
                'id' => 'd'
                , "properties" => array(
                    "class" => "fa fa-dashboard"
                )
                , "ref" => $mini_warehouse_app_base_url . "dashboard"
            ),
            Lang::get("Master") => array(
                'id' => 'm'
                , "properties" => array(
                    "class" => 'fa fa-th'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Warehouse Category") => array(
                        'id' => 'w_cc'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $mini_warehouse_app_base_url . "warehouse_category"
                    ),
                    Lang::get("Unit") => array(
                        'id' => 'un_t'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $mini_warehouse_app_base_url . "unit"
                    ),
                    Lang::get("Warehouse Location") => array(
                        'id' => 'w_lc'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $mini_warehouse_app_base_url . "warehouse_location"
                    ),
                )
            ),
            Lang::get("Stock Card") => array(
                'id' => 's'
                , "properties" => array(
                    "class" => 'fa fa-list-alt'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Stock In") => array(
                        'id' => 'st_in'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $mini_warehouse_app_base_url . "inflow"
                    ),
                    Lang::get("Stock Out") => array(
                        'id' => 'st_out'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $mini_warehouse_app_base_url . "inflow"
                    ),
                )
            ),
            Lang::get("Report") => array(
                'id' => 'r'
                , "properties" => array(
                    "class" => 'fa fa-child'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Report Stock Card") => array(
                        'id' => 'r_sc'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $phone_book_app_base_url . "contact/add"
                    ),
                )
            ),
                //</editor-fold>
        );

        $sehat_pharmacy_store_app_base_url = SI::type_get('ICES_Engine', 'sehat_pharmacy_store', '$app_list')['app_base_url'];
        self::$menu_list['sehat_pharmacy_store'] = array(
            //<editor-fold defaultstate="collapsed" desc="ices menu">
            Lang::get("Dashboard") => array(
                'id' => 'd'
                , "properties" => array(
                    "class" => "fa fa-dashboard"
                )
                , "ref" => $sehat_pharmacy_store_app_base_url . "dashboard"
            ),
            Lang::get("Master") => array(
                'id' => 'm'
                , "properties" => array(
                    "class" => 'fa fa-th'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Store") => array(
                        'id' => 'm_s'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . "store"
                    ),
                    Lang::get("Warehouse") => array(
                        'id' => 'm_w'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . "warehouse"
                    ),
                    Lang::get("Customer") => array(
                        'id' => 'm_c'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . "customer"
                    ),
                    Lang::get("Supplier") => array(
                        'id' => 'm_s'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . "supplier"
                    ),
                    Lang::get("Product") => array(
                        'id' => 'm_p'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => '#'
                        , 'child' => array(
                            Lang::get("Product Category") => array(
                                'id' => 'm_pc'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . "product_category"
                            ),
                            Lang::get("Product") => array(
                                'id' => 'm_pp'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . "product"
                            ),
                            Lang::get("Product Batch") => array(
                                'id' => 'm_ppb'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . "product_batch"
                            ),
                        ),
                    ),
                    Lang::get("Unit") => array(
                        'id' => 'm_u'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . "unit"
                    ),
                    Lang::get("Misc") => array(
                        'id' => 'm_etc'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => '#'
                        , 'child' => array(
                            Lang::get("BOS Bank Account") => array(
                                'id' => 'm_bba'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . "bos_bank_account"
                            ),
                            Lang::get("Payment Type") => array(
                                'id' => 'm_pt'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . "payment_type"
                            ),
                            Lang::get("Phone Number Type") => array(
                                'id' => 'm_pnt'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . "phone_number_type"
                            ),
                        )
                    ),
                )
            ),
            Lang::get("List") => array(
                'id' => 'l'
                , "properties" => array(
                    "class" => 'fa fa-list-alt'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Purchase") => array(
                        'id' => 'l_s'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => ''
                        , "child" => array(
                            Lang::get("Invoice") => array(
                                'id' => 'l_p_i'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'purchase_invoice/'
                            ),
                            Lang::get("Receipt") => array(
                                'id' => 'l_p_rec'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'purchase_receipt/'
                            ),
                            Lang::get("Return") => array(
                                'id' => 'l_p_ret'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'purchase_return/'
                            ),
                        )
                    ),
                    Lang::get("Sales") => array(
                        'id' => 'l_s'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => ''
                        , "child" => array(
                            Lang::get("Invoice") => array(
                                'id' => 'l_s_i'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'sales_invoice/'
                            ),
                            Lang::get("Receipt") => array(
                                'id' => 'l_s_rec'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'sales_invoice/'
                            ),
                        )
                    ),
                    Lang::get("Product") => array(
                        'id' => 'l_p'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => ''
                        , "child" => array(
                            Lang::get("Stock Opname") => array(
                                'id' => 'l_p_so'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'product_stock_opname/'
                            ),
                        )
                    ),
                )
            ),
            Lang::get("Activity") => array(
                'id' => 'a'
                , "properties" => array(
                    "class" => 'fa fa-child'
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Purchase") => array(
                        'id' => 'a_p'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => ''
                        , "child" => array(
                            Lang::get("Invoice") => array(
                                'id' => 'a_p_i'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'purchase_invoice/add'
                            ),
                            Lang::get("Receipt") => array(
                                'id' => 'a_p_rec'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'purchase_receipt/add'
                            ),
                            Lang::get("Return") => array(
                                'id' => 'a_p_ret'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'purchase_return/add'
                            ),
                        )
                    ),
                    Lang::get("Sales") => array(
                        'id' => 'a_s'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => ''
                        , "child" => array(
                            Lang::get("Invoice") => array(
                                'id' => 'a_s_i'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'sales_invoice/add'
                            ),
                            Lang::get("Receipt") => array(
                                'id' => 'a_s_rec'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'sales_receipt/add'
                            ),
                        )
                    ),
                    Lang::get("Product") => array(
                        'id' => 'l_p'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => ''
                        , "child" => array(
                            Lang::get("Stock Opname") => array(
                                'id' => 'a_p_so'
                                , "properties" => array("class" => "fa fa-th")
                                , "ref" => $sehat_pharmacy_store_app_base_url . 'product_stock_opname/add'
                            ),
                        )
                    ),
                )
            ),
            Lang::get("Report") => array(
                'id' => 'rpt'
                , "properties" => array(
                    "class" => APP_ICON::report()
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Product") => array(
                        'id' => 'Product'
                        , "rpt_product" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . 'rpt_product'
                    ),
                    Lang::get("Sales") => array(
                        'id' => 'rpt_sales'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . 'rpt_sales'
                    ),
                    Lang::get("Purchase") => array(
                        'id' => 'rpt_purchase'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . 'rpt_purchase'
                    ),
                    Lang::get("Simple") => array(
                        'id' => 'rpt_simple'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . 'rpt_simple'
                    ),
                )
            ),
            Lang::get('Misc') => array(
                'id' => 'misc'
                , "properties" => array(
                    "class" => APP_ICON::misc()
                )
                , "ref" => "#"
                , "child" => array(
                    Lang::get("Print Form") => array(
                        'id' => 'misc_print_form'
                        , "properties" => array("class" => "fa fa-th")
                        , "ref" => $sehat_pharmacy_store_app_base_url . 'print_form'
                    ),
                )
            )
                //</editor-fold>
        );

        //</editor-fold>
    }

    public static function path_get() {
        $app = SI::type_get('ICES_Engine', 'ices', '$app_list');
        $path = array(
            'index' => $app['app_base_url'] . 'security_menu/'
            , 'security_menu_engine' => $app['app_base_dir'] . 'security_menu/security_menu_engine'
            , 'security_menu_data_support' => $app['app_base_dir'] . 'security_menu/security_menu_data_support'
            , 'security_menu_renderer' => $app['app_base_dir'] . 'security_menu/security_menu_renderer'
            , 'ajax_search' => $app['app_base_url'] . 'security_menu/ajax_search/'
            , 'data_support' => $app['app_base_url'] . 'security_menu/data_support/'
        );

        return json_decode(json_encode($path));
    }

    public static function current_user_menu_get() {
        $result = array();
        $path = self::path_get();
        get_instance()->load->helper($path->security_menu_data_support);
        $app_name = ICES_Engine::$app['val'];
        $ices = SI::type_get('ICES_Engine', 'ices', '$app_list');
        $db = new DB(array('db_name' => $ices['app_db_conn_name']));

        $result = self::$menu_list[$app_name];

        if (User_Info::get_active_role() != 'ROOT') {

            $u_group_security_menu = Security_Menu_Data_Support::
                    u_group_security_menu_by_employee_get(User_Info::get()['user_id'], $app_name);

            foreach ($result as $key1 => $lvl1) {
                if (isset($lvl1['child'])) {
                    foreach ($lvl1['child'] as $key2 => $lvl2) {
                        if (isset($lvl2['child'])) {
                            foreach ($lvl2['child'] as $key3 => $lvl3) {
                                $found = false;
                                for ($i = 0; $i < count($u_group_security_menu); $i++) {
                                    if ($u_group_security_menu[$i]->menu_id == $lvl3['id']) {
                                        $found = true;
                                    }
                                }
                                if (!$found) {
                                    unset($result[$key1]['child'][$key2]['child'][$key3]);
                                }
                            }
                        }
                        $found = false;
                        for ($i = 0; $i < count($u_group_security_menu); $i++) {
                            if ($u_group_security_menu[$i]->menu_id == $lvl2['id']) {
                                $found = true;
                            }
                        }
                        if (!$found) {
                            unset($result[$key1]['child'][$key2]);
                        }
                    }
                }
                $found = false;
                for ($i = 0; $i < count($u_group_security_menu); $i++) {
                    if ($u_group_security_menu[$i]->menu_id == $lvl1['id']) {
                        $found = true;
                    }
                }
                if (!$found) {
                    unset($result[$key1]);
                }
            }
        }


        return $result;
    }

}

?>
