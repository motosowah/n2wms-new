<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        // $db = \Config\Database::connect();
        // $query = $db->query("SELECT version();");
        // $result = $query->getRow();

        // echo "PostgreSQL Version: " . $result->version;

        
        $data = [
            'nav' => 'Dashboard',
            'username' => session()->get('username'),
        ];

        // authentication required //
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('dashboard', $data); // automatically uses the layout
    }
}
