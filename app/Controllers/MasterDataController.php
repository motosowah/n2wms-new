<?php

namespace App\Controllers;

class MasterDataController extends BaseController
{
    public function index()
    {
        echo " > master data index page";
        // $data = [
        //     'nav' => 'Stock Detail',
        //     'username' => session()->get('username'),
        // ];

        // // authentication required //
        // if (!session()->get('isLoggedIn')) {
        //     return redirect()->to('/login');
        // }

        // return view('stock_detail_view', $data);
    }

    
    // - get item description from item code - //
    public function get_item_desc(){
        $item_code = $this->request->getGet('item_code');
        // echo $item_code;
        $model = new \App\Models\MasterDataModel();
        $result = $model->get_item_descr($item_code);
        // print_r($result);
        
        // echo $item_desc;
        $response['status'] = "ok";
        if($result){
            $item_desc = $result['item_desc'];
            $response['item_desc'] = $item_desc;
        }else{
            $response['item_desc'] = "";
        }
        

        echo json_encode($response);
    }


    // - search item - //
    public function search_item(){
        $get = $this->request->getGet();
        $plant = $get['plant'];
        $search_text = $get['search_text'];

        // $item_code = $this->request->getGet('item_code');
        // echo $item_code;
        $model = new \App\Models\MasterDataModel();
        $result = $model->search_item($plant, $search_text);

        // print_r($result);
        
        // prepare item list response//
        $item_list = Array();
        foreach ($result as $row) {
            $item_code = $row['item_code'];
            $item_name = $row['item_desc'];
            $data = ["label" => "$item_name", "value" => "$item_code", "description" => "$item_name"];
            array_push($item_list, $data);
        }

        $response['status'] = "ok";
        $response['data'] = $item_list;

        // echo json_encode($response);
        return $this->response->setJSON($response);
    }


    // - search pallet code - //
    public function search_pallet(){
        $get = $this->request->getGet();
        $plant = $get['plant'];
        $search_text = $get['search'];

        // $item_code = $this->request->getGet('item_code');
        // echo $item_code;
        $model = new \App\Models\MasterDataModel();
        $result = $model->search_pallet($plant, $search_text);

        // print_r($result);
        
        // prepare data list response//
        $data_list = Array();
        foreach ($result as $row) {
            $pallet_code = $row['pallet_code'];
            $data = ["label" => "$pallet_code", "value" => "$pallet_code", "description" => "$pallet_code"];
            array_push($data_list, $data);
        }

        $response['status'] = "ok";
        $response['data'] = $data_list;

        // echo json_encode($response);
        return $this->response->setJSON($response);
    }


    // - search rack code - //
    public function search_rack(){
        $get = $this->request->getGet();
        $plant = $get['plant'];
        $search_text = $get['search'];

        // $item_code = $this->request->getGet('item_code');
        // echo $item_code;
        $model = new \App\Models\MasterDataModel();
        $result = $model->search_rack($plant, $search_text);

        // print_r($result);
        
        // prepare data list response//
        $data_list = Array();
        foreach ($result as $row) {
            $rack_code = $row['rack_code'];
            $data = ["label" => "$rack_code", "value" => "$rack_code", "description" => "$rack_code"];
            array_push($data_list, $data);
        }

        $response['status'] = "ok";
        $response['data'] = $data_list;

        // echo json_encode($response);
        return $this->response->setJSON($response);
    }


    // - check pallet code - //
    public function check_pallet_code(){
        $pallet = $this->request->getGet('pallet');
        log_message('info', " > checking pallet code $pallet");
        $model = new \App\Models\MasterDataModel();
        $results = $model->check_pallet($pallet);
        // if pallet code valid //
        if(floatval($results['valid']) > 0){
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
