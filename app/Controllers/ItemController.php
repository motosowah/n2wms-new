<?php

namespace App\Controllers;

class ItemController extends BaseController
{
    public function index()
    {
        $data = [
            'nav' => 'Stock Detail',
            'username' => session()->get('username'),
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('stock_detail_view', $data);
    }

    
    // - get item description from item code - //
    public function get_item_desc($item_code = ""){
        // $item_code = $this->request->getGet('item_code');
        // echo $item_code;
        $model = new \App\Models\MasterModel();
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
        $model = new \App\Models\MasterModel();
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
