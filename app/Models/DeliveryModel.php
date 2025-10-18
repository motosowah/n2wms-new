<?php

// app/Models/ProductModel.php
namespace App\Models;

use CodeIgniter\Model;

class DeliveryModel extends Model
{
    // Not tied to a single table for complex queries
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();

    }
    

    // GET DELIVERY SAP DATA //
    public function get_sap_data($date_from, $date_to, $plant, $do_no)
    {
        $sql = "SELECT id_t, delivery, deliv_date_from_to, name_of_the_ship_to_party, material, description, batch, delivery_quantity,sales_unit,total_weight,weight_unit,shipping_point_receiving_pt, created_by_sys, 
        TO_CHAR(created_date, 'YYYY-MM-DD HH24:MI:SS') AS f_created_date
        FROM dssls100t 
        WHERE DATE(deliv_date_from_to) BETWEEN '$date_from' AND '$date_to' 
        AND shipping_point_receiving_pt = '$plant'
        AND delivery LIKE '%$do_no%'
        ORDER BY id_t DESC";

        $query = $this->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        return $query->getResultArray();
        
    }


    // GET PLANT FROM DO NUMBER //
    public function get_plant($do_no){
        $sql = "SELECT shipping_point_receiving_pt
        FROM dssls100t 
        WHERE delivery = '$do_no'";

        $query = $this->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        $result = $query->getRowArray();
        return $result['shipping_point_receiving_pt'];
    }


    // GET DO DATE FROM DO NUMBER //
    public function get_do_date($do_no){
        $sql = "SELECT deliv_date_from_to
        FROM dssls100t 
        WHERE delivery = '$do_no'";

        $query = $this->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        $result = $query->getRowArray();
        return $result['deliv_date_from_to'];
    }


    // GET CUSTOMER NAME FROM DO NUMBER //
    public function get_customer($do_no){
        $sql = "SELECT name_of_the_ship_to_party
        FROM dssls100t 
        WHERE delivery = '$do_no'";

        $query = $this->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        $result = $query->getRowArray();
        return $result['name_of_the_ship_to_party'];
    }



    // get latest sap stock data //
    public function get_sap_data_latest()
    {
        $sql = "SELECT id_t, delivery, deliv_date_from_to, name_of_the_ship_to_party, material, description, batch, delivery_quantity,sales_unit,total_weight,weight_unit,shipping_point_receiving_pt, created_by_sys, 
        TO_CHAR(created_date, 'YYYY-MM-DD HH24:MI:SS') AS f_created_date
        FROM dssls100t 
        ORDER BY id_t DESC LIMIT 1000";
                        
        return $this->db->query($sql)->getResultArray();
    }


    // get generate DO list //
    public function get_generate()
    {
        // $sql = "SELECT plant, delivery, deliv_date_from_to, name_of_the_ship_to_party, schdelivery_quantity, total_pick, balance_pick, del_status FROM public.dssls100t_balance_pick ORDER BY deliv_date_from_to, delivery DESC";

        $sql = "SELECT * FROM _delivery_generate_balance_view WHERE NOT outstanding_stock = 0 ORDER BY do_date DESC LIMIT 100";
                        
        return $this->db->query($sql)->getResultArray();
    }


    // get on process picking //
    public function get_on_process()
    {
        // $sql = "SELECT id_t, trans_type, doc_number, pallet_no, storage_location, item_code, item_desc, lot_no, quantity, created_by, 
        // TO_CHAR(created_date, 'YYYY-MM-DD HH24:MI:SS') AS f_created_date 
        // FROM mobt400t_temp_cmitm001t WHERE TRUE ORDER BY doc_number DESC";

        $sql = "SELECT id_t, plant, doc_number, do_date, customer, item_code, item_desc, lot_no, storage_location, pallet_no, quantity, created_by, TO_CHAR(created_date, 'YYYY-MM-DD HH24:MI:SS') AS f_created_date FROM _delivery_on_process_view;";
                        
        return $this->db->query($sql)->getResultArray();
    }


    // GET FINISHED PICKING DATA //
    public function get_finished($date_from, $date_to, $plant)
    {
        $sql = "SELECT id_t, plant, doc_number, do_date, customer, item_code, item_desc, 
        lot_no, rack, pallet, qty, created_by, 
        TO_CHAR(created_date, 'YYYY-MM-DD HH24:MI:SS') AS f_created_date 
        FROM _delivery_finished_view 
        WHERE DATE(do_date) BETWEEN '$date_from' AND '$date_to' 
        AND plant = '$plant'
        ORDER BY id_t DESC";

        $query = $this->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        return $query->getResultArray();

    }


    // GET LATEST FINISHED PICKING DATA //
    public function get_finished_latest()
    {
        $sql = "SELECT id_t, trans_type, doc_number, pallet_no, storage_location, item_code, item_desc, 
        lot_no,  inv_qty as quantity, created_by, 
        TO_CHAR(created_date, 'YYYY-MM-DD HH24:MI:SS') AS f_created_date 
        , hid
        FROM mobt400t ORDER BY id_t DESC LIMIT 1000";
                        
        return $this->db->query($sql)->getResultArray();
    }


    // GET BALANCE DO DATA //
    public function get_balance($date_from, $date_to, $plant, $do_no)
    {
        // $timer = \Config\Services::timer();
        // $timer->start('queryTimer');   // start timer

        $sql = "SELECT * FROM _delivery_balance_view3 
        WHERE DATE(do_date) BETWEEN '$date_from' AND '$date_to' 
        AND plant = '$plant' AND do_no LIKE '%$do_no%'
        ORDER BY do_date DESC";

        // log_message('info', $sql);
        // $timer->stop('queryTimer');    // stop timer
        // log_message('info', $timer->getElapsedTime('queryTimer')." seconds");

        $return = $this->db->query($sql)->getResultArray();
        // $performance = $this->db->getPerformanceData();
        // log_message('info', print_r($performance, TRUE));
           
        return $return;
    }


    // GET LATEST BALANCE DO DATA //
    public function get_balance_latest()
    {
        $sql = "SELECT id_t, deliv_date_from_to, delivery, item, name_of_the_ship_to_party, material, material_description, 
        batch, delivery_quantity, sales_unit, total_weight, weight_unit, actual_delivery_qty, base_unit_of_measure, net_weight, volume, volume_unit, inv_qty, balance, images, plant
        --FROM public.dssls100t_mobt400t_union ORDER BY id_t DESC 
        FROM delivery_balance ORDER BY id_t DESC 
        LIMIT 1000";
                        
        return $this->db->query($sql)->getResultArray();
    }


    // generate DO //
    public function generate()
    {
        $sql = "SELECT * FROM delivery_allocate";
                        
        return $this->db->query($sql)->getResultArray();
    }


    // CREATE NEW DO DELIVERY //
    public function create_delivery($data){
        $builder = $this->db->table('dssls100t');
        return $builder->insert($data);
        /*
        $sql = "SELECT deliv_date_from_to
        FROM dssls100t 
        WHERE delivery = '$do_no'";

        $query = $this->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        $result = $query->getRowArray();
        return $result['deliv_date_from_to'];
        */
    }


}

