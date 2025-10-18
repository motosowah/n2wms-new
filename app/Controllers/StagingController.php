<?php

namespace App\Controllers;

class StagingController extends BaseController
{
    public function index()
    {
        $data = [
            'nav' => 'Staging',
            'username' => session()->get('username'),
            'default_plant' => 'ID1A',
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('transaction/staging_view', $data);
    }


    // -- SAVE STAGING DATA -- //
    public function save()
    {
        log_message('info', ' > saving staging data');

        $item_data = Array();
        $plant = $this->request->getGet('plant');
        $pallet = $this->request->getGet('pallet');
        $rack_dest = $this->request->getGet('rack');
        $username = session()->get('username');
        $item_data['username'] = $username;

        // check for destination rack code //
        log_message('info', " > checking destination rack code $rack_dest");
        $model = new \App\Models\MasterDataModel();
        $results = $model->check_rack($rack_dest);
        // if rack code valid //
        if(floatval($results['valid']) > 0){
            // log_message('debug', print_r($item_data, TRUE));
            log_message('info', " > get list of items inside pallet source $pallet");

            // get items inside pallet //
            $model = new \App\Models\StockModel();
            $results = $model->get_stock_detail("%%", "%%", "%%", "%%", "%%", "%$pallet%");

            // if items empty //
            if(count($results) > 0){
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
                    array_push($line, $data);

                    // log_message('info', " > moving stock data");
                    // log_message('debug', print_r($data, TRUE));

                    // moving each stock //
                    $query = $model->move_stock($data['pallet'], $data['pallet'], $data['rack'], $rack_dest, $data['item_code'], $data['lot_no'], $data['qty_available'], $username);

                    if($query){
                        log_message('info', " - stock data moved ");
                    }
                }
                
                $response['status'] = "ok";
                $response['data'] = print_r($results, TRUE);
            }else{
                $response['status'] = "error";
                $response['message'] = "Cannot staging empty pallet!";
                log_message('info', " - pallet empty, stopped");
            }
        }else{
            log_message('info', " - rack code $rack_dest not found");
            $response['status'] = "error";
            $response['message'] = "Destination rack <strong>$rack_dest</strong> not found!";
        }

        

        // log_message('debug', print_r($results, true));
        // print_r($results);

        

        return $this->response->setJSON($response);

    }


    // - check pallet code - //
    public function check_pallet_code(){
        $pallet = $this->request->getGet('pallet');
        log_message('info', " > checking pallet code $pallet");
        $model = new \App\Models\MasterDataModel();
        $results = $model->check_pallet($pallet);
        // if pallet code valid //
        if($results['valid']){
            $response['status'] = "ok";
            $response['message'] = "pallet code valid";
            log_message('info', " - pallet code $pallet valid");
        }else{
            $response['status'] = "error";
            $response['message'] = "pallet code not found";
            log_message('info', " - pallet code $pallet not found");
        }
        return $this->response->setJSON($response);
    }


    // - check rack code - //
    public function check_rack_code(){
        $rack = $this->request->getGet('rack');
        log_message('info', " > checking rack code $rack");
        $model = new \App\Models\MasterDataModel();
        $results = $model->check_rack($rack);
        // if pallet code valid //
        if($results['valid']){
            $response['status'] = "ok";
            $response['message'] = "rack code valid";
            log_message('info', " - rack code $rack valid");
        }else{
            $response['status'] = "error";
            $response['message'] = "rack code not found";
            log_message('info', " - rack code $rack not found");
        }
        return $this->response->setJSON($response);
    }


    // - get items inside pallet - //
    public function get_item_pallet(){
        $pallet = $this->request->getGet('pallet');
        log_message('info', " > get item from pallet $pallet");

        // search stock data //
        $model = new \App\Models\StockModel();
        $results = $model->get_stock_detail("%%", "%%", "%%", "%%", "%%", "%$pallet%");

        // log_message('debug', print_r($results, true));
        // print_r($results);

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
            array_push($line, $data);
        }
        // print_r($line);

        $response['status'] = "ok";
        $response['data'] = $line;

        return $this->response->setJSON($response);

    }

}
