<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    // Login Sayfası Görünümü
    public function showLoginForm()
    {
        return view('login');
    }
    // Login İşlemi
    public function login(Request $request){

    }
    // Çıkış İşlemi
    public function logout(Request $request){

    }
    // Statik Kullanıcı Oluşturma
    public function createStaticUser()
    {

    }
}
