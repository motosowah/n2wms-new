<?php

namespace App\Controllers;

class MobileController extends BaseController
{

    // - main page - //
    public function index()
    {
        $data = [
            'nav' => 'WMS New',
            'username' => session()->get('username'),
        ];

        

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        // display requested page //
        return view('mobile/main_view', $data);
    }


    // - palletizing page - //
    public function palletizing()
    {
        $data = [
            'nav' => 'Palletizing',
            'username' => session()->get('username'),
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        // display requested page //
        return view('mobile/palletizing_view', $data);

    }


    // - staging page - //
    public function staging()
    {
        $data = [
            'nav' => 'Staging',
            'username' => session()->get('username'),
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        // display requested page //
        return view('mobile/staging_view', $data);

    }


    // - palletizing add item page - //
    public function add_item()
    {
        $data = [
            'nav' => 'Add Item',
            'username' => session()->get('username'),
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        // display requested page //
        return view('mobile/add_item_view', $data);

    }


    // - add new temporary palletizing items - //
    public function add_temp_pltz(){
        $username = session()->get('username');
        $response = Array();
        $item_code = $this->request->getGet('item_code');
        $item_desc = $this->request->getGet('item_desc');
        $lot_no = trim(strtoupper($this->request->getGet('lot_no')));
        $qty_pcs = $this->request->getGet('qty_pcs');
        $packaging = trim(strtoupper($this->request->getGet('packaging')));
        $packing_size = $this->request->getGet('packing_size');
        $total_qty = $this->request->getGet('total_qty');

        // insert into temporary palletizing table //
        $data = [
            'item_code' => $item_code,
            'item_desc' => $item_desc,
            'lot_no' => $lot_no,
            'qty_pcs' => $qty_pcs,
            'packaging' => $packaging,
            'packing_size' => $packing_size,
            'total_qty' => $total_qty,
            'username' => $username,
        ];
        
        $model = new \App\Models\WmsModel();
        // check for current username items count //
        $builder = $model->db->table('_tmp_pltz_items');
        $builder->where('username', $username);
        $count = $builder->countAllResults();
        log_message('debug', $count);
        // limit to maximum 10 items per username //
        if(floatval($count) < 10){
            // insert item into list //
            $builder = $model->db->table('_tmp_pltz_items');
            $builder->insert($data);
            $response['status'] = "ok";
            $response['message'] = "item inserted";

        }else{
            $response['status'] = "error";
            $response['message'] = "Maksimum hanya 10 item yang diperbolehkan dalam sekali palletizing!";
        }

        return $this->response->setJSON($response);
        
    }


    // - open temporary palletizing item for editing - //
    public function edit_temp_pltz(){
        $username = session()->get('username');
        $id = $this->request->getGet('id');
        $response = Array();
        
        $model = new \App\Models\WmsModel();
        $row = $model->db->table('_tmp_pltz_items')
             ->where('username', $username)
             ->where('id', $id)
             ->get()
             ->getRowArray();
        // print_r($row);

        $data = [
            'nav' => 'Edit Item',
            'username' => session()->get('username'),
            'id' => $row['id'],
            'item_code' => $row['item_code'],
            'item_desc' => $row['item_desc'],
            'lot_no' => $row['lot_no'],
            'qty_pcs' => $row['qty_pcs'],
            'packaging' => $row['packaging'],
            'packing_size' => $row['packing_size'],
            'total_qty' => $row['total_qty'],
        ];

        // print_r($data);

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        // display requested page //
        return view('mobile/edit_item_view', $data);

    }


    // - save temporary palletizing items - //
    public function save_temp_pltz(){
        $response = Array();

        $username = session()->get('username');
        $id = $this->request->getGet('id');
        $lot_no = trim(strtoupper($this->request->getGet('lot_no')));
        $qty_pcs = $this->request->getGet('qty_pcs');
        $packaging = trim(strtoupper($this->request->getGet('packaging')));
        $packing_size = $this->request->getGet('packing_size');
        $total_qty = $this->request->getGet('total_qty');

        // insert into temporary palletizing table //
        $data = [
            'lot_no' => $lot_no,
            'qty_pcs' => $qty_pcs,
            'packaging' => $packaging,
            'packing_size' => $packing_size,
            'total_qty' => $total_qty,
            'username' => $username,
        ];
        
        log_message('info', " > updating temporary palletizing item $id");
        // update temporary item //
        $model = new \App\Models\WmsModel();
        $builder = $model->db->table('_tmp_pltz_items');
        $builder->where('id', $id);
        $builder->update($data);
        // Get number of affected rows
        $updatedRows = $model->db->affectedRows();

        log_message('info', $updatedRows." rows updated");

        $response['status'] = "ok";
        $response['message'] = "item updated";

        return $this->response->setJSON($response);
        
    }


    // - delete temporary palletizing items - //
    public function delete_temp_pltz(){
        $response = Array();

        $username = session()->get('username');
        $id = $this->request->getGet('id');
        
        log_message('info', " > deleting temporary palletizing item $id");
        // update temporary item //
        $model = new \App\Models\WmsModel();
        $builder = $model->db->table('_tmp_pltz_items');
        $builder->where('id', $id);
        $builder->delete();
        // Get number of affected rows
        $updatedRows = $model->db->affectedRows();

        log_message('info', $updatedRows." rows updated");

        $response['status'] = "ok";
        $response['message'] = "item deleted";

        return $this->response->setJSON($response);
        
    }


    // - clear temporary palletizing items - //
    public function clear_temp_pltz(){
        $response = Array();

        $username = session()->get('username');
        
        log_message('info', " > clearing temporary palletizing item for username $username");
        // clear table rows //
        $model = new \App\Models\WmsModel();
        $builder = $model->db->table('_tmp_pltz_items');
        $builder->where('username', $username);
        $builder->delete();
        // Get number of affected rows
        $updatedRows = $model->db->affectedRows();

        log_message('info', $updatedRows." rows updated");

        $response['status'] = "ok";
        $response['message'] = "items cleared";

        return $this->response->setJSON($response);
        
    }


    // - validate pallet code - //
    public function validate_pallet($pallet){
        $response = Array();

        $username = session()->get('username');
        
        log_message('info', " > checking pallet code $pallet");
        // check pallet master view //
        $model = new \App\Models\WmsModel();
        $builder = $model->db->table('_pallet_master_view');
        $builder->where('pallet_code', $pallet);
        $count = $builder->countAllResults();
        log_message('debug', $count);
        // build response //
        if(floatval($count) > 0){
            // if pallet code found //
            $response['status'] = "ok";
            $response['message'] = "pallet code valid";
        }else{
            $response['status'] = "error";
            $response['message'] = "pallet code not found";
        }

        return $this->response->setJSON($response);
        
    }


    // - validate rack code - //
    public function validate_rack($rack){
        $response = Array();

        $username = session()->get('username');
        
        log_message('info', " > checking rack code $rack");
        // check rack master view //
        $model = new \App\Models\WmsModel();
        $builder = $model->db->table('_rack_master_view');
        $builder->where('rack_code', $rack);
        $count = $builder->countAllResults();
        log_message('debug', $count);
        // build response //
        if(floatval($count) > 0){
            // if pallet code found //
            $response['status'] = "ok";
            $response['message'] = "rack code valid";
        }else{
            $response['status'] = "error";
            $response['message'] = "rack code not found";
        }

        return $this->response->setJSON($response);
        
    }


    // - get list of temporary palletizing items - //
    public function get_temp_pltz(){
        $username = session()->get('username');
        $response = Array();
        
        $model = new \App\Models\WmsModel();
        $row = $model->db->table('_tmp_pltz_items')
             ->where('username', $username)
             ->orderBy('id')
             ->get()
             ->getResultArray();

        // print_r($row);
        // items list array //
        $items_list = Array();
        // init html response //
        $html = "";
        foreach ($row as $item) {
            // create items list //
            $data = Array();
            $data['item_code'] = $item['item_code'];
            $data['item_code'] = $item['item_code'];
            $data['lot_no'] = $item['lot_no'];
            $data['total_qty'] = $item['total_qty'];

            array_push($items_list, $data);

            $id = $item['id'];
            $item_code = $item['item_code'];
            $item_desc = $item['item_desc'];
            $lot_no = $item['lot_no'];
            $qty_pcs = $item['qty_pcs'];
            $packaging = $item['packaging'];
            $packing_size = $item['packing_size'];
            $total_qty = $item['total_qty'];

            $html .= "
            <a href=\"/mobile/edit_temp_pltz?id=$id\">
            <div class=\"card-stacked\">
            <div class=\"card-content\">
            <div class=\"row\"><div class=\"input-field col s12\">
            <input id=\"item_code_$id\" type=\"text\" class=\"\" value=\"$item_code\" readonly>
            <label for=\"item_code_$id\">Item Code</label>
            </div>

            <div class=\"input-field col s12\">
                <input id=\"item_desc\" type=\"text\" class=\"\" value=\"$item_desc\" readonly>
                <label for=\"item_desc\">Item Description</label>
            </div>

            <div class=\"input-field col s12\">
                <input id=\"lot_no\" type=\"text\" class=\"\" value=\"$lot_no\" readonly>
                <label for=\"lot_no\">Lot Number</label>
            </div>

            <div class=\"row\">
            <div class=\"input-field col s4\">
                <input placeholder=\"Qty (PCS)\" id=\"qty_pcs\" type=\"number\" class=\"\" style=\"font-size: 1.5em;\" value=\"$qty_pcs\" readonly>
                <label for=\"qty_pcs\">Qty (PCS)</label>
            </div>
            <div class=\"input-field col s4\">
                <input type=\"text\" id=\"packaging\" class=\"autocomplete\" value=\"$packaging\" readonly>
                <label for=\"packaging\">Packaging</label>
            </div>
            <div class=\"input-field col s4\">
                <input placeholder=\"Size (KG)\" id=\"size\" type=\"number\" class=\"\" style=\"font-size: 1.5em;\"value=\"$packing_size\" readonly>
                <label for=\"size\">Size (KG)</label>
            </div>
            </div>

            <div class=\"input-field col s12\">
            <input id=\"total_qty\" type=\"number\" class=\"\" value=\"$total_qty\" style=\"font-size: 2em;\" readonly>
            <label for=\"total_qty\">Total Qty (KG)</label>
            </div>
            </div></div></a>";
        }

        // echo $html;

        $response['status'] = "ok";
        $response['html'] = $html;
        $response['items_list'] = $items_list;

        return $this->response->setJSON($response);
        
    }


    // get stock detail //
    public function get_stock_detail()
    {

        // log_message('error', 'Something went wrong.');
        // log_message('debug', 'This is a debug message.');
        // log_message('info', 'Informational message.');

        log_message('info', ' > parsing stock data table');

        $plant = $this->request->getGet('plant');
        $item_code = $this->request->getGet('item_code');
        $item_desc = $this->request->getGet('item_desc');
        $lot_no = $this->request->getGet('lot_no');
        $rack = $this->request->getGet('rack');
        $pallet = $this->request->getGet('pallet');

        // if no search parameter requested //
        $empty = $this->request->getGet('empty');
        if($empty == "yes"){
            // create empty data //
            $data = [];
        }else{
            // search stock data //
            $model = new \App\Models\StockModel();
            $results = $model->get_stock_detail("$plant", "%$item_code%", "%$item_desc%", "%$lot_no%", "%$rack%", "%$pallet%");

            // parse data row //
            $data = Array();
            foreach ($results as $row) {
                $line = Array();            
                array_push($line, $row['whs_code']);
                array_push($line, $row['item_code']);
                array_push($line, $row['item_desc']);
                array_push($line, $row['lot_no']);
                array_push($line, $row['str_location']);
                array_push($line, $row['pallet_code']);
                array_push($line, $row['inv_on_hand']);
                array_push($line, $row['inv_allocated']);
                array_push($line, $row['available_stock']);
                $actions = "";
                array_push($line, $actions);        
                array_push($data, $line);
            }
            
        }

        
        $response['data'] = $data;

        echo json_encode($response);

    }


    // get stock inside pallet //
    public function get_stock_pallet(){
        $pallet = $this->request->getGet('pallet');
        log_message('info', " > get stock data from pallet $pallet");

        // search stock data //
        $model = new \App\Models\StockModel();
        $results = $model->get_stock_detail("%%", "%%", "%%", "%%", "%%", "%$pallet%");

        // parse data row //
        $line = Array();
        foreach ($results as $row) {
            $data = Array();
            $data['plant'] = $row['whs_code'];
            $data['item_code'] = $row['item_code'];
            $data['item_desc'] = $row['item_desc'];
            $data['lot_no'] = $row['lot_no'];
            $data['rack'] = $row['str_location'];
            $data['pallet'] = $row['pallet_code'];
            $data['qty_on_hand'] = $row['inv_on_hand'];
            $data['qty_allocated'] = $row['inv_allocated'];
            $data['qty_available'] = $row['available_stock'];
            
            array_push($data, $line);
        }
        $response['data'] = $line;

        echo json_encode($response);

    }


    // - autocomplete pallet code - //
    public function search_pallet(){
        // get search text //
        $search = strtoupper($this->request->getGet('search'));
        // search from database
        $model = new \App\Models\WmsModel();
        $row = $model->db->table('_pallet_master_view')
             ->like('pallet_code', $search)
             ->orderBy('pallet_code', 'ASC')
             ->limit(10)
             ->get()
             ->getResultArray();
        
        // init results //
        $results = Array();
        foreach ($row as $data) {
            array_push($results, $data['pallet_code']);
        }

        return $this->response->setJSON($results);
    }


    // - autocomplete rack code - //
    public function search_rack(){
        // get search text //
        $search = strtoupper($this->request->getGet('search'));
        // search from database
        $model = new \App\Models\WmsModel();
        $row = $model->db->table('_rack_master_view')
             ->like('rack_code', $search)
             ->orderBy('rack_code', 'ASC')
             ->limit(10)
             ->get()
             ->getResultArray();
        
        // init results //
        $results = Array();
        foreach ($row as $data) {
            array_push($results, $data['rack_code']);
        }

        return $this->response->setJSON($results);
    }


    // - search item - //
    public function search_item(){
        // get search text //
        $search = strtoupper($this->request->getGet('search'));
        // search from database
        $model = new \App\Models\WmsModel();
        $row = $model->db->table('_item_master')
             ->like('item_desc', $search)
             ->orderBy('item_desc', 'ASC')
             ->limit(10)
             ->get()
             ->getResultArray();
        
        // init results //
        $results = Array();
        $html = "";
        foreach ($row as $data) {
            $item_code = $data['item_code'];
            $item_desc = $data['item_desc'];
            $html .= "
            <div class='box' onclick=\"choose_item('$item_code','$item_desc');\">
            <div class='green-text'>$item_code</div>
            <b>$item_desc</b>
            </div>
            ";
            // print_r($data);
            // array_push($results, $data['rack_code']);
        }

        // init response //
        $response = Array();
        $response['status'] = "ok";
        $response['html'] = $html;

        return $this->response->setJSON($response);
        
    }


    public function search()
    {
        $item_desc = $this->request->getGet('item_desc');
        // echo $item_desc;

        // search stock data //
        $model = new \App\Models\StockModel();
        $results = $model->stockSearch([
            'item_desc' => "%$item_desc%",
        ]);

        print_r($results);

        // build table data //
        $data = Array();
        foreach ($results as $row) {
            $line = Array();            
            array_push($line, $row['whs_code']);
            array_push($line, $row['item_code']);
            array_push($line, $row['item_desc']);
            array_push($line, $row['lot_no']);
            array_push($line, $row['str_location']);
            array_push($line, $row['pallet_code']);
            array_push($line, $row['inv_on_hand']);
            $actions = "";
            array_push($line, $actions);        
            array_push($data, $line);
        }
        $response['data'] = $data;

        echo json_encode($response);

        // print_r($results);

        // var_dump($results);
        
        // return view('products/search', ['results' => $results]);
    }


    // - extract scanned barcode - //
    function barcode(){
        $response = Array();
        $scan = $this->request->getGet('scan');
        $scan = trim($scan);
        $scan = trim($scan, "\x1D");  // clean GS1 chars //
        // echo strlen($scan)." chars length";

        // validate raw barcode length //
        $length = strlen($scan);
        if ($length == 98){
            // separate GS1 datamatrix parts //
            $parts = explode(chr(29), $scan);
            // extract item code //
            $group0 = $parts[0];
            $item_code = substr($group0, 19);
            $response['item_code'] = $item_code;
            
            // - get item description from item master table - //
            $model = new \App\Models\WmsModel();
            $row = $model->db->table('_item_master')
                ->where('item_code', $item_code)
                ->get()
                ->getRowArray();
            
            // if item code valid //
            if($row){
                $response['status'] = "ok";
                $response['item_code'] = $item_code;
                $response['item_desc'] = $row['item_desc'];
                // extract lot number //
                $group2 = $parts[2];
                $lot_no = substr($group2, 2);
                $response['lot_no'] = $lot_no;
                
                // extract packing size //
                $group3 = $parts[3];
                $size = substr($group3, 7);
                $response['size'] = floatval($size);
            }else{
                $response['status'] = "error";
                $response['message'] = "item code $item_code not found";
            }

        }else{
            // invalid barcode length //
            log_message('error', " > invalid barcode length ($length chars)");
            $response['status'] = "error";
            $response['message'] = "invalid barcode";
        }

        
        

        return $this->response->setJSON($response);
    }

/*
    // - get stock detail - //
    public function get_stock_detail()
    {
        $data = [
            'nav' => 'Stock Detail',
            'username' => session()->get('username'),
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('stock_detail', $data);
    }
*/        
}
