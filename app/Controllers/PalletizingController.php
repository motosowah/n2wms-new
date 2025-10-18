<?php

namespace App\Controllers;

class PalletizingController extends BaseController
{
    public function index()
    {
        $data = [
            'nav' => 'Palletizing',
            'username' => session()->get('username'),
            'default_plant' => 'ID1A',
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('transaction/palletizing_view', $data);
    }


    // -- SAVE PALLETIZING DATA -- //
    public function save()
    {
        log_message('info', ' > saving palletizing data');

        $item_data = Array();

        $plant = $this->request->getGet('plant');
        $item_data['plant'] = $plant;
        $transaction = $this->request->getGet('transaction');
        $item_data['transaction'] = $transaction;
        $doc_no = $this->request->getGet('doc_no');
        $item_data['doc_no'] = $doc_no;
        $pallet = $this->request->getGet('pallet');
        $item_data['pallet'] = $plant;
        $rack = $this->request->getGet('rack');
        $item_data['rack'] = $rack;
        $item_code = $this->request->getGet('item_code');
        $item_data['item_code'] = $item_code;
        $item_desc = $this->request->getGet('item_desc');
        $item_data['item_desc'] = $item_desc;
        $lot_no = $this->request->getGet('lot_no');
        $item_data['lot_no'] = $lot_no;
        $qty = $this->request->getGet('qty');
        $item_data['qty'] = $qty;
        $username = session()->get('username');
        $item_data['username'] = $username;

        log_message('debug', print_r($item_data, TRUE));
        
        // search stock data //
        $model = new \App\Models\StockModel();
        // $results = $model->get_stock_detail($plant, "%$item_code%", "%$item_desc%", "%$lot_no%", "%$rack%", "%$pallet%");
        $query = $model->insert_stock($transaction, $doc_no, $item_code, $lot_no, $rack, $pallet, $qty, 0, $username);

        $response['status'] = "ok";
        $response['data'] = print_r($item_data, TRUE);

        echo json_encode($response);

    }


    // -- SAVE PALLETIZING DATA FROM MOBILE -- //
    public function save_mobile()
    {
        log_message('info', ' > saving palletizing data from mobile device');

        $username = session()->get('username');
        $transaction = $this->request->getGet('transaction');
        $doc_no = trim($this->request->getGet('doc_no'));
        $pallet = trim($this->request->getGet('pallet'));
        $rack = trim($this->request->getGet('rack'));
        $items_list = $this->request->getGet('items_list');

        log_message('debug', print_r($this->request->getGet(), TRUE));

        foreach ($items_list as $item) {
            // print_r($item);
            
            $item_code = trim($item['item_code']);
            $lot_no = trim($item['lot_no']);
            log_message('debug', "lot_no: '$lot_no'");
            $qty = $item['total_qty'];

            // insert new stock data //
            $model = new \App\Models\StockModel();
            $query = $model->insert_stock($transaction, $doc_no, $item_code, $lot_no, $rack, $pallet, $qty, 0, $username);
        }
/*
        $item_data = Array();

        $plant = $this->request->getGet('plant');
        

        log_message('debug', print_r($item_data, TRUE));
*/
/*        
        // insert new stock data //
        $model = new \App\Models\StockModel();
        $query = $model->insert_stock($transaction, $doc_no, $item_code, $lot_no, $rack, $pallet, $qty, 0, $username);
*/
        $response['status'] = "ok";
        $response['message'] = "palletizing items saved";

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
