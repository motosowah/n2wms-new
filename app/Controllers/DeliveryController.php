<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;

class DeliveryController extends BaseController
{
    public function index()
    {
        // set default date //
        $cur_month = date("Y-m-");
        $default_date_from = $cur_month."01";
        $default_date_to = date("Y-")."12-31";
        $data = [
            'nav' => 'Delivery',
            'username' => session()->get('username'),
            'cur_month' => $cur_month,
            'default_date_from' => $default_date_from,
            'default_date_to' => $default_date_to,
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('delivery_view', $data);
    }


    // - test function - //
    public function test(){
        echo "<pre>";
        echo "\n > test function";
        $available_list = Array();
        $req_qty = 300;
        echo "\n > req qty $req_qty";
        // create available list  //
        $available_list = [
            30,
            45,
            600,
        ];

        print_r($available_list);

        // distributing requirement qty into available list //
        $allocated_list = Array();
        $i = 0;
        while ($i < count($available_list)) {
            $available_qty = $available_list[$i];
            // check current available qty //
            if($available_qty >= $req_qty){
                echo "\n > stock = $available_qty, requirement = $req_qty. - stock suffice - ";
                $allocated_qty = $req_qty;
                echo "\n > allocated_qty = $allocated_qty";
                $req_qty -= $allocated_qty;
                array_push($allocated_list, $allocated_qty);
            }elseif($available_qty < $req_qty){
                echo "\n > stock = $available_qty, requirement = $req_qty. - insufficient stock - ";
                $allocated_qty = $available_qty;
                echo "\n > allocated_qty = $allocated_qty";
                $req_qty -= $allocated_qty;
                array_push($allocated_list, $allocated_qty);
            }

            echo "\n > available_qty = $available_qty";
            echo "\n > remaining requirement qty = $req_qty";
            if($req_qty <= 0){
                echo "\n - done -";
                break;
            }else{
                echo "\n - outstanding stock -";
            }
            $i++;
        }

        print_r($allocated_list);


        
        echo "</pre>";
        
    }


    // - GET SAP DATA - //
    public function get_sap_data(){
        log_message('info', ' >> get delivery sap stock data');

        $date_from = $this->request->getGet('date_from');
        if(strlen($date_from) < 1){
            $date_from = '2000-01-01';
        }
        $date_to = $this->request->getGet('date_to');
        if(strlen($date_to) < 1){
            $date_to = '2000-01-01';
        }

        $plant = $this->request->getGet('plant');
        $do_no = $this->request->getGet('do_no');
        
        $model = new \App\Models\DeliveryModel();

        $results = $model->get_sap_data($date_from, $date_to, $plant, $do_no);
        
        // parse data rows //
        $data = Array();
        foreach ($results as $row) {
            $line = Array();
            $id = $row['id_t'];
            $plant = $row['shipping_point_receiving_pt'];
            $do_no = $row['delivery'];
            $do_date = $row['deliv_date_from_to'];
            $customer = $row['name_of_the_ship_to_party'];
            $item_code = $row['material'];
            $item_desc = $row['description'];
            $lot_no = $row['batch'];
            $qty = $row['total_weight'];
            $unit = $row['weight_unit'];
            $created_by = $row['created_by_sys'];
            $created_time = $row['f_created_date'];
            $actions = "<button type=\"button\" class=\"btn btn-sm btn-danger d-flex align-items-center justify-content-center\" aria-label=\"Delete\" title=\"Delete\" onclick=\"delete_sap_data($id);\"><i class=\"bi bi-trash\"></i></button>";
            // $actions = " - actions - ";

            array_push($line, $id);
            array_push($line, $plant);     // plant //
            array_push($line, $do_no);    // DO number //
            array_push($line, $do_date);      // delivery date //
            array_push($line, $customer);   // customer //
            array_push($line, $item_code);
            array_push($line, $item_desc);
            array_push($line, $lot_no);
            array_push($line, $qty);
            array_push($line, $unit);
            array_push($line, $created_by);
            array_push($line, $created_time);
            array_push($line, $actions);        
            array_push($data, $line);
        }
        $response['data'] = $data;
        echo json_encode($response);

    }


    // - GET WAITING LIST DO GENERATE - //
    public function get_generate(){

        log_message('info', ' >> get delivery generate DO list');

        $model = new \App\Models\DeliveryModel();
        $results = $model->get_generate();
                
        // parse data rows //
        $data = Array();
        foreach ($results as $row) {
            $line = Array();
            $plant = $row['plant'];
            $do_no = $row['do_no'];
            $do_date = $row['do_date'];
            $customer = $row['customer'];
            $qty_do = $row['delivery_qty'];
            $qty_picking_process = $row['on_process_picking_qty'];
            $qty_picking_finished = $row['finished_picking_qty'];
            $qty_balance = $row['outstanding_stock'];
            $status = 'status';
            $actions = "<a href='delivery/generate/$do_no' target='_blank'><button type=\"button\" class=\"btn btn-sm btn-primary\" aria-label=\"Generate\" title=\"Generate\"><i class=\"bi bi-gear\"></i></button></a>";

            array_push($line, $plant);     // plant //
            array_push($line, $do_no);    // DO number //
            array_push($line, $do_date);      // delivery date //
            array_push($line, $customer);   // customer //
            array_push($line, $qty_do);    // qty DO //
            array_push($line, $qty_picking_process);      // on process picking //
            array_push($line, $qty_picking_finished);      // finished picking qty //
            array_push($line, $qty_balance);    // balance //
            array_push($line, $status);      // status //
            
            array_push($line, $actions);        
            array_push($data, $line);
        }
        $response['data'] = $data;
        echo json_encode($response);
    }


    // - GET ON PROCESS PICKING - //
    public function get_on_process(){

        log_message('info', ' >> get delivery on process list');

        $model = new \App\Models\DeliveryModel();
        $results = $model->get_on_process();
        
        // parse data rows //
        $data = Array();
        foreach ($results as $row) {
            $line = Array();
            $id = $row['id_t'];
            $do_no = $row['doc_number'];
            $plant = $row['plant'];
            $do_date = $row['do_date'];
            $rack = $row['storage_location'];
            $pallet = $row['pallet_no'];
            $item_code = $row['item_code'];
            $item_desc = $row['item_desc'];
            $lot_no = $row['lot_no'];
            $qty = $row['quantity'];
            $created_by = $row['created_by'];
            $created_time = $row['f_created_date'];
            $actions = "
            <button onclick=\"edit_picking($id)\" type=\"button\" class=\"btn btn-sm btn-secondary\" aria-label=\"Process Picking\" title=\"Edit Picking\"><i class=\"bi bi-pencil-square\"></i></button>
            
            <button onclick=\"process_picking($id)\" type=\"button\" class=\"btn btn-sm btn-success\" aria-label=\"Process Picking\" title=\"Process Picking\"><i class=\"bi bi-upload\"></i></button>

            <button onclick=\"delete_on_process($id)\" type=\"button\" class=\"btn btn-sm btn-danger\" aria-label=\"Delete\" title=\"Delete\"><i class=\"bi bi-trash\"></i></button>
            ";

            array_push($line, $id);     // ID //
            array_push($line, $plant);
            array_push($line, $do_no);    // DO number //
            array_push($line, $do_date);      // delivery date //
            array_push($line, $rack);   // rack //
            array_push($line, $pallet);    // pallet //
            array_push($line, $item_code);      
            array_push($line, $item_desc);    
            array_push($line, $lot_no);
            array_push($line, $qty);
            array_push($line, $created_by);
            array_push($line, $created_time);
            array_push($line, $actions);        
            array_push($data, $line);
        }
        $response['data'] = $data;
        echo json_encode($response);
    }


    // - GET FINISHED PICKING - //
    public function get_finished(){
        log_message('info', ' >> get delivery finished picking data');

        $date_from = $this->request->getGet('date_from');
        if(strlen($date_from) < 1){
            $date_from = '2000-01-01';
        }
        $date_to = $this->request->getGet('date_to');
        if(strlen($date_to) < 1){
            $date_to = '2000-01-01';
        }

        $plant = $this->request->getGet('plant');

        $model = new \App\Models\DeliveryModel();

        $results = $model->get_finished($date_from, $date_to, $plant);
        
        // parse data rows //
        $data = Array();
        foreach ($results as $row) {
            $line = Array();
            $id = $row['id_t'];
            $do_no = $row['doc_number'];
            $plant = $row['plant'];
            $do_date = $row['do_date'];
            $customer = $row['customer'];
            $item_code = $row['item_code'];
            $item_desc = $row['item_desc'];
            $lot_no = $row['lot_no'];
            $qty = $row['qty'];
            $unit = "KG";
            $rack = $row['rack'];
            $pallet = $row['pallet'];
            $created_by = $row['created_by'];
            $created_time = $row['f_created_date'];
            $actions = "<button type=\"button\" class=\"btn btn-sm btn-danger d-flex align-items-center justify-content-center\" aria-label=\"Delete\" title=\"Delete\"><i class=\"bi bi-trash\"></i></button>";

            array_push($line, $id);
            array_push($line, $plant);
            array_push($line, $do_no);    // DO number //
            array_push($line, $do_date);      // delivery date //
            array_push($line, $customer);   // customer //
            array_push($line, $item_code);
            array_push($line, $item_desc);
            array_push($line, $lot_no);
            array_push($line, $rack);    // rack //
            array_push($line, $pallet);       // pallet //
            array_push($line, $qty);
            array_push($line, $unit);
            array_push($line, $created_by);
            array_push($line, $created_time);
            array_push($line, $actions);        
            array_push($data, $line);
        }
        $response['data'] = $data;
        echo json_encode($response);
    }


    // - GET BALANCE DO - //
    public function get_balance(){

        log_message('info', ' >> get delivery balance do data');

        $date_from = $this->request->getGet('date_from');
        if(strlen($date_from) < 1){
            $date_from = '2000-01-01';
        }
        $date_to = $this->request->getGet('date_to');
        if(strlen($date_to) < 1){
            $date_to = '2000-01-01';
        }

        $plant = $this->request->getGet('plant');
        $do_no = $this->request->getGet('do_no');

        $model = new \App\Models\DeliveryModel();

        $results = $model->get_balance($date_from, $date_to, $plant, $do_no);

/*
        // if search parameter not empty //
        $search = $date_from.$date_to.$plant.$item_code.$item_desc.$lot_no;
        if(strlen($search) > 0){    // search sap data //
            // $results = $model->get_sap_data($date_from, $date_to, $plant, $item_code, $item_desc, $lot_no);
        }else{  // get only latest sap data //
            $results = $model->get_balance_latest();
        }
*/

        // $performance = $db->getPerformanceData();
        // print_r($performance);

        // parse data rows //
        $data = Array();
        foreach ($results as $row) {
            $line = Array();
            // $id = $row['id_t'];
            $plant = $row['plant'];
            $do_no = $row['do_no'];
            $do_date = $row['do_date'];;
            $customer = $row['customer'];;
            $item_code = $row['item_code'];
            $item_desc = $row['item_desc'];
            $lot_no = $row['lot_no'];
            $qty_do = $row['delivery_qty'];
            $on_process_picking_qty = $row['picking_qty'];
            $finished_picking_qty = $row['picked_qty'];
            $outstanding_stock = $row['balance'];
            $photo = $row['images'] ?? " - no photo -";
            $actions = ' - actions - ';

            // array_push($line, $id);
            array_push($line, $plant);     // plant //
            array_push($line, $do_no);    // DO number //
            array_push($line, $do_date);      // delivery date //
            array_push($line, $customer);   // customer //
            array_push($line, $item_code);
            array_push($line, $item_desc);
            array_push($line, $lot_no);
            array_push($line, $qty_do);
            array_push($line, $on_process_picking_qty);
            array_push($line, $finished_picking_qty);
            array_push($line, $outstanding_stock);
            array_push($line, $photo);
            array_push($line, $actions);
            // array_push($line, $actions);

            array_push($data, $line);
        }
        $response['data'] = $data;
        echo json_encode($response);
    }


    // - GENERATE DO - //
    public function generate($do_no = ""){
        
        $model = new \App\Models\DeliveryModel();

        log_message('info', " >> generate delivery DO $do_no");

        echo "<pre>";

        $session = session()->get();
        print_r($session);
        $username = session()->get('username');
        

        echo "\n > checking generate balance for do $do_no \n";
        // $sql = "SELECT balance FROM delivery_generate_balance_view WHERE delivery = '$do_no' LIMIT 1;";
        $sql = "SELECT outstanding_stock FROM _delivery_generate_balance_view WHERE do_no = '$do_no' LIMIT 1;";
        $rows = $model->db->query($sql)->getResultArray();
        print_r($rows);
        $do_balance = floatval($rows[0]['outstanding_stock']);
        var_dump($do_balance);
        // if do not balanced //
        if($do_balance > 0){
            // get item from delivery sap data //
            // $do_no = "2001990490";
            $sql = "SELECT * FROM dssls100t WHERE delivery = '$do_no';";
            $results = $model->db->query($sql)->getResultArray();
            // get items inside do //
            $do_items = Array();
            foreach ($results as $row) {
                $item = Array();
                $item['id'] = $row['id_t'];
                $item['plant'] = $row['shipping_point_receiving_pt'];
                $item['item_code'] = $row['material'];
                $item['item_desc'] = $row['description'];
                $item['lot_no'] = $row['batch'];
                $item['qty'] = $row['actual_delivery_qty'];
                array_push($do_items, $item);
            }

            // print_r($do_items);

            echo " > processing each item inside do $do_no \n";
            $do_item = 1;
            $do_items_count = count($do_items);
            foreach ($do_items as $item){
                echo "\n ====================================== \n";
                echo "  - processing (item $do_item of $do_items_count) \n";
                // print_r($item);
                $hid = $item['id'];
                $plant = $item['plant'];
                $item_code = $item['item_code'];
                $item_desc = $item['item_desc'];
                $lot_no = $item['lot_no'];
                $req_qty = $item['qty'];
                
                echo "\n > checking available stock for $plant $item_code $item_desc $lot_no";
                echo "\n  - requested qty = $req_qty";

                // get available items //
                $sql = "SELECT * FROM _stock_balance_view WHERE plant = '$plant' AND item_code = '$item_code' AND lot_no = '$lot_no' AND available_stock > 0 ORDER BY str_location;";
                $available_items = $model->db->query($sql)->getResultArray();
                echo "\n available_items = ";
                print_r($available_items);

                // if available items not empty //
                if(count($available_items) > 0){
                    echo "\n > trying to allocate stock for $plant $item_code $item_desc $lot_no req_qty = $req_qty \n";
                    // get available qty from list //
                    $available_list = Array();
                    foreach ($available_items as $item) {
                        array_push($available_list, $item['available_stock']);
                    }

                    // echo "\n > available_list: ";
                    // print_r($available_list);

                    // distributing requirement qty into available list //
                    $allocated_list = Array();
                    $i = 0;
                    while ($i < count($available_list)) {
                        $available_qty = $available_list[$i];
                        // check current available qty //
                        if($available_qty >= $req_qty){
                            echo "\n  - available_qty = $available_qty, req_qty = $req_qty. - stock suffice - done >";
                            $allocated_qty = $req_qty;
                            echo "\n  - allocated_qty = $allocated_qty";
                            $req_qty -= $allocated_qty;
                            array_push($allocated_list, $allocated_qty);
                        }elseif($available_qty < $req_qty){
                            echo "\n  - available_qty = $available_qty, req_qty = $req_qty. - insufficient stock -  checking next item >";
                            $allocated_qty = $available_qty;
                            echo "\n  - allocated_qty = $allocated_qty";
                            $req_qty -= $allocated_qty;
                            array_push($allocated_list, $allocated_qty);
                        }

                        // echo "\n > available_qty = $available_qty";
                        echo "\n  - remaining requirement qty = $req_qty";
                        if($req_qty <= 0){
                            echo "\n - done -";
                            break;
                        }else{
                            echo "\n - outstanding stock -";
                        }
                        $i++;
                    }

                    // allocating required qty into current stock //
                    $i = 0;
                    foreach ($allocated_list as $qty) {
                        $current_available_item = $available_items[$i];
                        // echo "\n > current_available_item = \n";
                        // print_r($current_available_item);
                        
                        $current_plant = $current_available_item['plant'];
                        $current_item_code = $current_available_item['item_code'];
                        $current_item_desc = $current_available_item['item_desc'];
                        $current_lot_no = $current_available_item['lot_no'];
                        $current_pallet = $current_available_item['pallet_code'];
                        $current_rack = $current_available_item['str_location'];
                        $current_available_qty = $current_available_item['available_stock'];
                        $current_allocated_qty = $qty;

                        echo "\n  - request allocated to $current_plant $current_item_code $current_item_desc $current_lot_no $current_pallet $current_rack $current_allocated_qty";

                        echo "\n > creating delivery picking list";
                        echo "\n  - $hid $do_no $current_plant $current_item_code $current_item_desc $current_lot_no $current_pallet $current_rack $current_allocated_qty";
                        $sql = "INSERT INTO delivery_on_process (trans_type, doc_number, hid, pallet_no, storage_location, item_code, lot_no, inv_qty, inv_unit, str_qty, str_unit, created_by, created_date, checked, images, item_desc) VALUES ('Delivery', '$do_no', '$hid', '$current_pallet', '$current_rack', '$current_item_code', '$current_lot_no', $current_allocated_qty, NULL, NULL, NULL, '$username', now(), NULL, NULL, '$current_item_desc');";
                        // $query = $model->db->query($sql);

                        $sql = "INSERT INTO mobt400t_temp (trans_type, doc_number, hid, pallet_no, storage_location, item_code, lot_no, inv_qty, inv_unit, str_qty, str_unit, created_by, created_date, checked, images, item_desc) VALUES ('Delivery', '$do_no', '$hid', '$current_pallet', '$current_rack', '$current_item_code', '$current_lot_no', $current_allocated_qty, NULL, NULL, NULL, '$username', now(), NULL, NULL, '$current_item_desc');";
                        $query = $model->db->query($sql);

                        echo "\n > updating stock allocated qty";
                        echo "\n  - $current_plant $current_item_code $current_item_desc $current_lot_no $current_pallet $current_rack $current_allocated_qty";
                        $sql = "UPDATE dsinv002t SET inv_allocated = (inv_allocated + $current_allocated_qty), last_updated_by = 'test_user' WHERE item_code = '$current_item_code' AND lot_no = '$current_lot_no' AND pallet_code = '$current_pallet' AND str_location = '$current_rack' AND whs_code = '$current_plant';";
                        $query = $model->db->query($sql);

                        $i++;
                    }  
                }else{
                    echo "\n --> no stock available to allocate ";
                }

                $do_item++;
                echo "\n ====================================== \n";
            }
        }else{
            // do already balance, nothing to do //
            echo "\n > progress on $do_no already completed. can't generate. \n";
        }

        echo "</pre>";

    }


    // - create new delivery data - //
    public function create_delivery(){
        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $postData = $this->request->getPost(); // Gets all POST data as an associative array
        // create new delivery data to insert //
        $data = array(
                'deliv_date_from_to'=> date("Y-m-d", strtotime($postData['do_date'])),
                'goods_issue_date'=> date("Y-m-d", strtotime($postData['do_date'])),
                'delivery'=> $postData['do_no'],
                'shipping_point_receiving_pt'=> $postData['plant'],  // plant //
                'name_of_the_ship_to_party'=> $postData['customer'],  // customer //
                'material'=> $postData['item_code'],
                'description'=> $postData['item_desc'],
                'batch'=> $postData['lot_no'],
                'delivery_quantity'=> str_replace(',','',$postData['qty']),
                'sales_unit'=> "KG",
                'total_weight'=> str_replace(',','',$postData['qty']),
                'weight_unit'=> "KG",
                'created_by'=> session()->get('username'),
                'actual_delivery_qty'=>str_replace(',','',$postData['qty']),
                'base_unit_of_measure'=> "KG",
                'location_of_the_sold_to_party'=> "customer",
                'id_import'=> date('YmdHis'),
                'created_by_sys'=> session()->get('username'),
            );

        // print_r($postData);
        // Or to get a specific field
        $name = $this->request->getPost('name');

        $model = new \App\Models\DeliveryModel();
        $query = $model->create_delivery($data);
        if($query){
            $response['status'] = "ok";
            $response['message'] = "delivery data inserted";
        }else{
            $response['status'] = "error";
            $response['message'] = "failed to insert delivery data";
        }
        

        // send response //
        echo json_encode($response);
    }


    // - DELETE SAP DATA - //
    public function delete_sap($do_id = ""){
        $model = new \App\Models\DeliveryModel();
        // echo "<pre>";
        // get delivery detail data //
        $sql = "SELECT * FROM dssls100t WHERE id_t = ?;";
        $query = $model->db->query($sql, [$do_id]);
        // log_message('debug', $this->db->getLastQuery());
        $sap_data = $query->getResultArray();

        // print_r($sap_data);
        foreach($sap_data as $data){
            // print_r($data);
            $do_no = $data['delivery'];
            $item_code = $data['material'];
            $lot_no = $data['batch'];

            // copy deleted finished picking data //
            $sql = "INSERT INTO mobt400t_cancel SELECT * FROM mobt400t WHERE doc_number = '$do_no' AND item_code = '$item_code' AND lot_no = '$lot_no';";
            $query = $model->db->query($sql, []);

            // delete finished picking data //
            $sql = "DELETE FROM mobt400t WHERE doc_number = '$do_no' AND item_code = '$item_code' AND lot_no = '$lot_no';";
            $query = $model->db->query($sql, []);

            // delete on process picking data //
            $sql = "DELETE FROM mobt400t_temp WHERE doc_number = '$do_no' AND item_code = '$item_code' AND lot_no = '$lot_no';";
            $query = $model->db->query($sql, []);

            // delete sap data source //
            $sql = "DELETE FROM dssls100t WHERE id_t = ?";
            $query = $model->db->query($sql, [$do_id]);
        }

        // echo "</pre>";
        
        if($query){
            $response['status'] = "ok";
            $response['message'] = "delivery data deleted";
        }else{
            $response['status'] = "error";
            $response['message'] = "failed";
        }
        return $this->response->setJSON($response);
        
    }


    // - DELETE ON PROCESS PICKING - //
    public function delete_on_process($id = ""){
        log_message('info', "delete on process delivery id #$id");
        $model = new \App\Models\DeliveryModel();

        // get on process picking data //
        $sql = "SELECT * FROM mobt400t_temp WHERE id_t = ?;";
        $query = $model->db->query($sql, [$id]);
        // log_message('debug', $this->db->getLastQuery());
        $results = $query->getResultArray();

        foreach($results as $data){
            // get picking data //
            $do_no = $data['doc_number'];
            $item_code = $data['item_code'];
            $lot_no = $data['lot_no'];
            $rack = $data['storage_location'];
            $pallet = $data['pallet_no'];
            $qty = $data['inv_qty'];

            // deduct the allocated stock qty //
            $sql = "UPDATE dsinv002t dt SET inv_allocated = dt.inv_allocated - ? WHERE 	dt.item_code = ? AND dt.lot_no = ? AND dt.pallet_code = ? AND dt.str_location = ?;";
            $query = $model->db->query($sql, [$qty, $item_code, $lot_no, $pallet, $rack]);
        
        }

        // delete on process picking data //
        $sql = "DELETE FROM mobt400t_temp WHERE id_t = ?;";
        $query = $model->db->query($sql, [$id]);
        
        if($query){
            $response['status'] = "ok";
            $response['message'] = "on process picking delivery id $id deleted";
        }else{
            $response['status'] = "error";
            $response['message'] = "failed";
        }
        return $this->response->setJSON($response);
        
    }


    // - EXECUTE PROCESS PICKING DELIVERY - //
    public function process_picking($id = ""){
        $username = session()->get('username');
        log_message('info', "execute process picking delivery id #$id");
        $model = new \App\Models\DeliveryModel();

        // get on process picking data //
        $sql = "SELECT * FROM mobt400t_temp WHERE id_t = $id;";
        $query = $model->db->query($sql, []);
        // log_message('debug', $this->db->getLastQuery());
        $results = $query->getResultArray();

        foreach($results as $data){
            // get picking data //
            $do_no = $data['doc_number'];
            $item_code = $data['item_code'];
            $lot_no = $data['lot_no'];
            $rack = $data['storage_location'];
            $pallet = $data['pallet_no'];
            $qty = $data['inv_qty'];

            // update stock qty //
            // $sql = "UPDATE dsinv002t dt SET inv_on_hand = dt.inv_on_hand - $qty, inv_allocated = dt.inv_allocated - $qty WHERE dt.item_code = '$item_code' AND dt.lot_no = '$lot_no' AND dt.pallet_code = '$pallet' AND dt.str_location = '$rack';";
            // $query = $model->db->query($sql, []);
            // log_message('debug', $sql);

            // copy on process picking data into finished picking data //
            $sql = "INSERT INTO mobt400t (trans_type, doc_number, hid, pallet_no, storage_location, item_code, lot_no, inv_qty, created_by) SELECT trans_type, doc_number, hid, pallet_no, storage_location, item_code, lot_no, inv_qty, '$username' FROM mobt400t_temp mtt WHERE mtt.id_t = $id;";
            $query = $model->db->query($sql, []);
            log_message('debug', $sql);

            // delete on process picking data //
            $sql = "DELETE FROM mobt400t_temp WHERE id_t = $id;";
            $query = $model->db->query($sql, []);
            log_message('debug', $sql);
        
        }

        
        if($query){
            $response['status'] = "ok";
            $response['message'] = "execute picking delivery id $id completed";
        }else{
            $response['status'] = "error";
            $response['message'] = "failed";
        }
        return $this->response->setJSON($response);
        
    }


    // - OPEN ON PROCESS PICKING DATA FOR EDITING - //
    public function open_picking($id){
        $username = session()->get('username');
        log_message('info', "opening on process picking id #$id");
        $model = new \App\Models\DeliveryModel();

        $row = $model->db->table('mobt400t_temp')
             ->where('id_t', $id)
             ->get()
             ->getRowArray();

        log_message('debug', print_r($row, true));

        $response['status'] = "ok";
        $response['data'] = $row;
        $response['message'] = "on process picking delivery id $id opened";
        return $this->response->setJSON($response);
        
    }


    // - SAVE MODIFIED PICKING DATA - //
    public function save_picking(){
        log_message('debug', "get values: ".print_r($this->request->getGet(), TRUE));

        $username = session()->get('username');
        $id = $this->request->getGet('id');
        $do_no = $this->request->getGet('do_no');
        $item_code = $this->request->getGet('item_code');
        $lot_no = $this->request->getGet('lot_no');
        $rack = $this->request->getGet('rack');
        $pallet = $this->request->getGet('pallet');
        $qty = $this->request->getGet('picking_qty');
        log_message('info', "checking stock for picking id #$id");
        // checking current available stock on selected location //
        $model = new \App\Models\DeliveryModel();
        $row = $model->db->table('_stock_balance')
             ->where('item_code', $item_code)
             ->where('lot_no', $lot_no)
             ->where('rack', $rack)
             ->where('pallet', $pallet)
             ->get()
             ->getRowArray();

        log_message('debug', print_r($row, true));
        $response = Array();
        if(isset($row)){    // if stock data found //
            log_message('info', ' > stock data found, checking for available stock qty');
            // check the available stock qty //
            $available_stock = $row['available_stock'];
            if(floatval($available_stock) >= floatval($qty)){
                // stock enough, proceed to update allocated qty //
                log_message('info', ' > stock enough, proceed to update allocated qty');
                // check if its new or existing picking data //
                $row = $model->db->table('mobt400t_temp')
                    ->where('doc_number', $do_no)
                    ->where('item_code', $item_code)
                    ->where('lot_no', $lot_no)
                    ->where('storage_location', $rack)
                    ->where('pallet_no', $pallet)
                    ->get()
                    ->getRowArray();

                // if picking data exists //
                if(isset($row)){    
                    log_message('info', ' > picking data already exists, updating data ...');
                    $builder = $model->db->table('mobt400t_temp');
                    $data = [
                        'doc_number' => $do_no,
                        'item_code' => $item_code,
                        'lot_no' => $lot_no,
                        'storage_location' => $rack,
                        'pallet_no' => $pallet,
                        'inv_qty' => $qty
                    ];
                    $builder->where('id_t', $id)
                        ->update($data);
                // if picking data not found //
                }else{
                    log_message('info', ' > picking data not found, creating new data ...');
                    // update delivery on process picking table //
                    $builder = $model->db->table('mobt400t_temp');
                    $data = [
                        'doc_number' => $do_no,
                        'item_code' => $item_code,
                        'lot_no' => $lot_no,
                        'storage_location' => $rack,
                        'pallet_no' => $pallet,
                        'inv_qty' => $qty,
                        'created_by' => $username
                    ];
                    $builder->insert($data);

                }
/*
                // update delivery on process picking table //
                $sql = "INSERT INTO mobt400t_temp (doc_number, item_code, lot_no, storage_location, pallet_no)
                VALUES ('$do_no', '$item_code', '$lot_no', '$rack', '$pallet')
                ON CONFLICT (doc_number, item_code, lot_no, storage_location, pallet_no) DO UPDATE
                SET storage_location = '$rack',
                    pallet_no = '$pallet',
                    inv_qty = '$qty'
                WHERE t.doc_number = '$do_no' AND t.item_code = '$item_code' AND t.lot_no = '$lot_no' AND t.storage_location = '$rack' AND t.pallet_no = '$pallet'    
                ;";
                log_message('debug', $sql);
                $query = $model->db->query($sql, []);
                /*
                $builder = $model->db->table('mobt400t_temp');
                $builder->where('id_t', $id)
                        ->update([
                            'storage_location' => $rack,
                            'pallet_no' => $pallet,
                            'inv_qty' => $qty
                        ]);
                */
                // refresh allocated stock qty //
                $model = new \App\Models\StockModel();
                $model->refresh_allocated_stock($item_code);
                $response['status'] = "ok";
                $response['message'] = "on process picking data updated";
            }else{
                // stock not enough, return error //
                log_message('info', ' > available stock is not enough on selected location');
                $response['status'] = "error";
                $response['message'] = "available stock is not  enough on selected location";
            }

        }else{      // if stock data not found //
            log_message('info', ' > stock data not found');
            $response['status'] = "error";
            $response['message'] = "no available stock on selected location";
        }

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
}
