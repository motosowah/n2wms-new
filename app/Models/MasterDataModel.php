<?php

// app/Models/ProductModel.php
namespace App\Models;

use CodeIgniter\Model;

class MasterDataModel extends Model
{
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }


    // get item name from item code //
    public function get_item_descr($item_code){
        return $this->db->table('cmitm001t')
                 ->getWhere(['item_code' => $item_code])
                 ->getRowArray();

        /*
        $sql = "SELECT item_desc FROM cmitm001t WHERE item_code = ?";
                        
        return $this->db->query($sql, [$item_code])->getResultArray();
        */
    }


    // search item //
    public function search_item($plant, $item_desc){

        $sql = "SELECT * FROM cmitm001t WHERE item_code ILIKE ? OR item_desc ILIKE ? LIMIT 15";
                        
        return $this->db->query($sql, ["%$item_desc%", "%$item_desc%"])->getResultArray();
    }


    // check pallet code //
    public function check_pallet($pallet){
        $sql = "SELECT COUNT(*) AS valid FROM cmitm002t WHERE pallet_code = ?";
        $query = $this->db->query($sql, [$pallet]);
        // log_message('debug', $this->db->getLastQuery());
        return $query->getResultArray()[0];
    }


    // check rack code //
    public function check_rack($rack){
        $sql = "SELECT COUNT(*) AS valid FROM cmitm003t WHERE str_location = ?";
        $query = $this->db->query($sql, [$rack]);
        // log_message('debug', $this->db->getLastQuery());
        return $query->getResultArray()[0];
    }

    // search pallet code //
    public function search_pallet($plant, $pallet){

        $sql = "SELECT * FROM _pallet_master_view WHERE plant = ? AND pallet_code ILIKE ? ORDER BY pallet_code ASC LIMIT 10";
                        
        // return $this->db->query($sql, [$plant, "%$pallet%"])->getResultArray();
        $query = $this->db->query($sql, [$plant, "%$pallet%"]);
        log_message('debug', $this->db->getLastQuery());
        return $query->getResultArray();
    }


    // search rack code //
    public function search_rack($plant, $rack){

        $sql = "SELECT * FROM _rack_master_view WHERE rack_code ILIKE ? ORDER BY rack_code ASC LIMIT 10";
                        
        $query = $this->db->query($sql, ["%$rack%"]);
        log_message('debug', $this->db->getLastQuery());
        return $query->getResultArray();
    }
    

    // search stock detail //
    public function stock_detail_model($plant, $item_code, $item_desc, $lot_no, $rack, $pallet)
    {

        $sql = "SELECT * FROM dsinv002t_detail WHERE whs_code = ? AND item_desc ILIKE ?";
                        
        return $this->db->query($sql, [$plant, $item_desc])->getResultArray();
    }
}

