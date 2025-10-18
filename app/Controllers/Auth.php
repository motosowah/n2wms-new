<?php

namespace App\Controllers;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        return view('auth/login');
    }

    public function doLogin()
    {
        $session = session();
        $model = new UserModel();

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $model->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            // Login success
            $session->set([
                // 'user_id' => $user['id'],
                'username' => $user['username'],
                'isLoggedIn' => true
            ]);
            return redirect()->to('/dashboard');
        } else {
            // Login failed
            return redirect()->back()->with('error', 'Invalid credentials')->withInput();
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
