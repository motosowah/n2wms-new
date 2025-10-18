<?php

namespace App\Controllers;

class ApiController extends BaseController
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

        echo " > API index";

        // return view('stock_detail', $data);
    }


    // get stock detail //
    public function stock_detail()
    {

        // log_message('error', 'Something went wrong.');
        // log_message('debug', 'This is a debug message.');
        // log_message('info', 'Informational message.');

        log_message('info', ' > parsing stock table data');

        $plant = $this->request->getGet('plant');
        $item_code = $this->request->getGet('item_code');
        $item_desc = $this->request->getGet('item_desc');
        $lot_no = $this->request->getGet('lot_no');
        $rack = $this->request->getGet('rack');
        $pallet = $this->request->getGet('pallet');

        // search stock data //
        $model = new \App\Models\StockModel();
        $results = $model->stock_detail_model($plant, $item_code, "%$item_desc%", $lot_no, $rack, $pallet);

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
        $response['data'] = $data;

        echo json_encode($response);

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
