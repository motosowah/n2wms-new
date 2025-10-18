<?php

// app/Models/ProductModel.php
namespace App\Models;

use CodeIgniter\Model;

class WmsModel extends Model
{
    // Not tied to a single table for complex queries
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();

    }
    

    // get stock detail //
    public function get_stock_detail($plant, $item_code, $item_desc, $lot_no, $rack, $pallet)
    {
        

        // $sql = "SELECT * FROM dsinv002t_detail WHERE whs_code = ? AND item_desc ILIKE ?";
        $sql = "SELECT * FROM stock_detail_view WHERE whs_code ILIKE ? AND item_code ILIKE ? AND item_desc ILIKE ? AND lot_no ILIKE ? AND str_location ILIKE ? AND pallet_code ILIKE ? ORDER BY item_desc";
                        
        $result = $this->db->query($sql, [$plant, $item_code, $item_desc, $lot_no, $rack, $pallet])->getResultArray();

        log_message('debug', $this->db->getLastQuery());
        return $result;
    }


    // get current stock qty //
    public function get_current_stock($plant, $item_code, $lot_no, $rack, $pallet)
    {

        // $sql = "SELECT * FROM dsinv002t_detail WHERE whs_code = ? AND item_desc ILIKE ?";
        $sql = "SELECT * FROM stock_detail_view WHERE whs_code ILIKE ? AND item_code ILIKE ? AND lot_no ILIKE ? AND str_location ILIKE ? AND pallet_code ILIKE ? ORDER BY item_desc";
                        
        $result = $this->db->query($sql, [$plant, $item_code, $lot_no, $rack, $pallet])->getResultArray();

        log_message('debug', $this->db->getLastQuery());

        
        // $current_stock = $result[0]['available_stock'];
        
        return $current_stock;
    }


    // -- INSERT NEW STOCK DATA -- //
    function insert_stock($trans_type, $doc_number, $item_code, $lot_no, $rack, $pallet, $inv_onhand, $inv_alloc, $username = "unknown"){
        // update stock table index //
        $sql = "INSERT INTO dsinv002t (item_code, lot_no, pallet_code, str_location, inv_on_hand, inv_allocated, created_by) VALUES ('$item_code', '$lot_no', '$pallet', '$rack', 0, 0, '$username') ON CONFLICT (item_code, lot_no, pallet_code, str_location) DO NOTHING";
        
        if($query = $this->db->query($sql)){
            log_message('info', " > stock index updated \n".__FILE__." [".__LINE__."]");
        }else{
            $error = $this->db->error();
        }

        // calculate stock from previous qty //
        $sql = "UPDATE dsinv002t 
            SET inv_on_hand = (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet' AND str_location = '$rack') + $inv_onhand, 
            inv_allocated = (SELECT inv_allocated FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet' AND str_location = '$rack') + $inv_alloc,
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet' AND str_location = '$rack'";
        
        if($query = $this->db->query($sql)){
            log_message('info', " > stock data updated");
        }else{
            $error = $this->db->error();
        }

        // insert palletizing data history //
        $sql = "INSERT INTO mobt100t(trans_type, doc_number, pallet_no, storage_location, item_code, lot_no, quantity, created_by) VALUES('$trans_type', '$doc_number', '$pallet', '$rack', '$item_code', '$lot_no', $inv_onhand, '$username')";

        if($query = $this->db->query($sql)){
            log_message('info', " > palletizing history saved");
        }else{
            $error = $this->db->error();
        }
    }


    // -- MOVE STOCK DATA -- //
    function move_stock($pallet_source, $pallet_dest, $rack_source, $rack_dest, $item_code, $lot_no, $qty, $username="unknown"){
        log_message('info', " > moving stock data");
        // update stock table index //
        log_message('info', " > updating stock index");
        $sql = "INSERT INTO dsinv002t (item_code, lot_no, pallet_code, str_location, inv_on_hand, inv_allocated, created_by) VALUES ('$item_code', '$lot_no', '$pallet_dest', '$rack_dest', 0, 0, '$username') ON CONFLICT (item_code, lot_no, pallet_code, str_location) DO NOTHING";
        log_message('debug', $sql);
        
        if($query = $this->db->query($sql)){
            log_message('info', " - stock index updated");
        }else{
            $error = $this->db->error();
        }

        // calculate destination stock data //
        log_message('info', " > calculating destination stock data");  
        $sql = "UPDATE dsinv002t SET 
            inv_on_hand = 
            (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_dest' AND str_location = '$rack_dest') + $qty, 
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_dest' AND str_location = '$rack_dest'";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - destination stock data updated");
        }else{
            $error = $this->db->error();
        }

        // calculate source stock data //
        log_message('info', " > calculating source stock data");  
        $sql = "UPDATE dsinv002t SET 
            inv_on_hand = 
            (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_source' AND str_location = '$rack_source') - $qty, 
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_source' AND str_location = '$rack_source'";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - source stock data updated");
        }else{
            $error = $this->db->error();
        }

        // insert movement data history //
        // trans_type	pallet_source	pallet_desti	location_source	location_desti	item_code	lot_no	quantity	created_by
        log_message('info', " > inserting stock movement history");
        $sql = "INSERT INTO mobt200t 
        (trans_type, pallet_source, pallet_desti, location_source, location_desti, item_code, lot_no, quantity, created_by) VALUES
        ('rack_to_rack', '$pallet_source', '$pallet_dest', '$rack_source', '$rack_dest', '$item_code', '$lot_no', $qty, '$username')";

        if($query = $this->db->query($sql)){
            log_message('info', " - stock movement history saved");
        }else{
            $error = $this->db->error();
        }
    }


    // -- STOCK STAGING -- //
    function staging($pallet_source, $pallet_dest, $rack_source, $rack_dest, $item_code, $lot_no, $qty, $username="unknown"){
        log_message('info', " > moving stock data");
        // update stock table index //
        log_message('info', " > updating stock index");
        $sql = "INSERT INTO dsinv002t (item_code, lot_no, pallet_code, str_location, inv_on_hand, inv_allocated, created_by) VALUES ('$item_code', '$lot_no', '$pallet_dest', '$rack_dest', 0, 0, '$username') ON CONFLICT (item_code, lot_no, pallet_code, str_location) DO NOTHING";
        log_message('debug', $sql);
        
        if($query = $this->db->query($sql)){
            log_message('info', " - stock index updated");
        }else{
            $error = $this->db->error();
        }

        // calculate destination stock data //
        log_message('info', " > calculating destination stock data");  
        $sql = "UPDATE dsinv002t SET 
            inv_on_hand = 
            (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_dest' AND str_location = '$rack_dest') + $qty, 
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_dest' AND str_location = '$rack_dest'";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - destination stock data updated");
        }else{
            $error = $this->db->error();
        }

        // calculate source stock data //
        log_message('info', " > calculating source stock data");  
        $sql = "UPDATE dsinv002t SET 
            inv_on_hand = 
            (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_source' AND str_location = '$rack_source') - $qty, 
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_source' AND str_location = '$rack_source'";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - source stock data updated");
        }else{
            $error = $this->db->error();
        }

        // insert movement data history //
        // trans_type	pallet_source	pallet_desti	location_source	location_desti	item_code	lot_no	quantity	created_by
        log_message('info', " > inserting stock movement history");
        $sql = "INSERT INTO mobt200t 
        (trans_type, pallet_source, pallet_desti, location_source, location_desti, item_code, lot_no, quantity, created_by) VALUES
        ('rack_to_rack', '$pallet_source', '$pallet_dest', '$rack_source', '$rack_dest', '$item_code', '$lot_no', $qty, '$username')";

        if($query = $this->db->query($sql)){
            log_message('info', " - stock movement history saved");
        }else{
            $error = $this->db->error();
        }
    }


    // -- STOCK SPLITTING -- //
    function splitting($pallet_source, $pallet_dest, $rack_source, $rack_dest, $item_code, $lot_no, $qty, $username="unknown"){
        log_message('info', " > moving stock data");
        // update stock table index //
        log_message('info', " > updating stock index");
        $sql = "INSERT INTO dsinv002t (item_code, lot_no, pallet_code, str_location, inv_on_hand, inv_allocated, created_by) VALUES ('$item_code', '$lot_no', '$pallet_dest', '$rack_dest', 0, 0, '$username') ON CONFLICT (item_code, lot_no, pallet_code, str_location) DO NOTHING";
        log_message('debug', $sql);
        
        if($query = $this->db->query($sql)){
            log_message('info', " - stock index updated");
        }else{
            $error = $this->db->error();
        }

        // calculate destination stock data //
        log_message('info', " > calculating destination stock data");  
        $sql = "UPDATE dsinv002t SET 
            inv_on_hand = 
            (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_dest' AND str_location = '$rack_dest') + $qty, 
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_dest' AND str_location = '$rack_dest'";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - destination stock data updated");
        }else{
            $error = $this->db->error();
        }

        // calculate source stock data //
        log_message('info', " > calculating source stock data");  
        $sql = "UPDATE dsinv002t SET 
            inv_on_hand = 
            (SELECT inv_on_hand FROM dsinv002t WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_source' AND str_location = '$rack_source') - $qty, 
            last_updated_by = '$username' 
            WHERE item_code = '$item_code' AND lot_no = '$lot_no' AND pallet_code = '$pallet_source' AND str_location = '$rack_source'";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - source stock data updated");
        }else{
            $error = $this->db->error();
        }

        // insert movement data history //
        // trans_type	pallet_source	pallet_desti	location_source	location_desti	item_code	lot_no	quantity	created_by
        log_message('info', " > inserting stock movement history");
        $sql = "INSERT INTO mobt200t 
        (trans_type, pallet_source, pallet_desti, location_source, location_desti, item_code, lot_no, quantity, created_by) VALUES
        ('rack_to_rack', '$pallet_source', '$pallet_dest', '$rack_source', '$rack_dest', '$item_code', '$lot_no', $qty, '$username')";

        if($query = $this->db->query($sql)){
            log_message('info', " - stock movement history saved");
        }else{
            $error = $this->db->error();
        }
    }


    // - ALLOCATE STOCK QTY - //
    function allocate_stock($item_code, $lot_no, $rack, $pallet, $qty){
        // calculate source stock data //
        log_message('info', " > allocating stock qty to $item_code, $lot_no, $rack, $pallet");  
        $sql = "
        UPDATE dsinv002t dt SET
            inv_allocated = dt.inv_allocated + $qty
        WHERE 
            item_code = '$item_code' AND
            lot_no = '$lot_no' AND
            pallet_code = '$pallet' AND
            str_location = '$rack';";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - allocated stock qty updated");
        }else{
            $error = $this->db->error();
        }
    }


    // - REFRESH / RECALCULATE ALLOCATE STOCK QTY - //
    function refresh_allocated_stock($item_code){
        // calculate source stock data //
        log_message('info', " > recalculating allocated stock qty for $item_code");  
        $sql = "
        UPDATE dsinv002t inv SET 
            inv_allocated = (
                -- on process picking delivery --
                COALESCE((SELECT SUM(inv_qty) FROM mobt400t_temp mtt WHERE 
                mtt.item_code = inv.item_code AND
                mtt.lot_no = inv.lot_no AND
                mtt.pallet_no = inv.pallet_code AND
                mtt.storage_location = inv.str_location), 0) +
                -- on process reservation prod --
                COALESCE((SELECT SUM(quantity) FROM mobt300t_temp mtt WHERE 
                mtt.item_code = inv.item_code AND
                mtt.lot_no = inv.lot_no AND
                mtt.pallet_no = inv.pallet_code AND
                mtt.storage_location = inv.str_location), 0)
            )
        WHERE item_code LIKE '%$item_code%' AND inv_on_hand > '0' AND whs_code != 'VIRTUAL';";
        log_message('debug', $sql);
        if($query = $this->db->query($sql)){
            log_message('info', " - allocated stock qty updated");
        }else{
            $error = $this->db->error();
        }
    }


}

