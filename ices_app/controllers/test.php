<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Test extends MY_Extended_Controller {

    public function backup() {
        $this->load->library('zip');
        $date = date("dmy H:i");
        $path = '/htdocs\ices\/';
        $this->zip->read_dir($path);
        $this->zip->download($date . ' - mybackup.zip');
    }

//    public function backupdb() {
//        $this->load->library('mysqldump');
//        $this->mysqldump->set_connection('aryana_phone_book_db');
//        $this->mysqldump->do_dump(true);
//        $this->mysqldump->set_connection('default');
//        $this->mysqldump->do_dump(true);
//    }

    public function backupdb() {
        $db = new DB();
        $this->load->dbutil();

        //jgn export db ini
        $except_db = array("information_schema", "phpmyadmin", "performance_schema", "mysql");
        //export hanya db ini
        $only_db = array('ices_db', 'aryana_phone_book_db');
        $list_all_db = $this->dbutil->list_databases();

        if (!empty($list_all_db)) {
            $this->load->library('mysqldump');
            if (!empty($except_db)) {
                $list_all_db = array_diff($list_all_db, $except_db);
            }

            if (!empty($only_db)) {
//                $list_all_db = array_intersect($list_all_db, $only_db);
            }

            foreach ($list_all_db as $key => $value) {
                $conn = array(
                    'dsn' => '',
                    'hostname' => 'localhost',
                    'username' => 'ices',
                    'password' => 'Ices123',
                    'database' => $value,
                    'dbdriver' => 'mysqli',
                    'dbprefix' => '',
                    'pconnect' => FALSE,
                    'db_debug' => FALSE,
                    'cache_on' => FALSE,
                    'cachedir' => '',
                    'char_set' => 'utf8',
                    'dbcollat' => 'utf8_general_ci',
                    'swap_pre' => '',
                    'encrypt' => FALSE,
                    'compress' => FALSE,
                    'stricton' => FALSE,
                    'failover' => array(),
                    'save_queries' => TRUE
                );

                $this->mysqldump->set_connection($conn);

                $this->mysqldump->do_dump(true);
            }
        }
    }

    public function export() {
        $this->load->library('excel');

        $sql = $this->db->get('aryana_phone_book_db');

        $this->excel->filename = 'phonebook';
        $this->excel->make_from_db($sql);
    }

}
