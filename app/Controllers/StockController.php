<?php

namespace App\Controllers;

class StockController extends BaseController
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
