use sehat_pharmacy_store_db;
-- truncate table phone_number_type;
-- truncate table phone_number_type_status_log;
-- truncate table store;
-- truncate table s_phone_number;
-- truncate table s_address;
-- truncate table store_status_log;
-- truncate table bos_bank_account;
-- truncate table bos_bank_account_status_log;
-- truncate table warehouse;
-- truncate table warehouse_status_log;


-- MASTER --
-- truncate table product_category;
-- truncate table product_category_status_log;
-- truncate table product;
-- truncate table product_status_log;
-- truncate table product_batch;
-- truncate table product_stock_good;
-- truncate table product_stock_good_qty_log;
-- truncate table product_stock_bad;
-- truncate table product_stock_bad_qty_log;

-- truncate table supplier;
-- truncate table supplier_status_log;
-- truncate table supplier_debit_amount_log
-- truncate table supplier_credit_amount_log

-- truncate table product_batch;
-- truncate table product_batch_qty_log;




-- TRANSACTIONAL --
-- truncate table purchase_invoice;
-- truncate  table pi_product;
-- truncate table purchase_invoice_status_log;

-- truncate table purchase_receipt;
-- truncate table purchase_receipt_status_log;

-- truncate table purchase_return;
-- truncate table pr_product;
-- truncate table purchase_return_status_log;

-- truncate table sys_code_counter_store_value;
-- update sys_code_counter set value = 1;