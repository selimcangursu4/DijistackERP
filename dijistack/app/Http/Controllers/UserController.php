<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class UserController extends Controller
{
  // Giriş İşlemi
  public function login(Request $request)
  {
     $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
     ]);

     if (Auth::attempt($request->only('email', 'password'))) {
        $request->session()->regenerate();

        $user = Auth::user();
        $company = Company::findOrFail($user->company_id);

        return redirect()->route('dashboard', [
            'domain' => $company->domain
        ]);
     }

      return back()->withErrors([
        'email' => 'Email veya şifre hatalı'
      ]);
  }
  // Çıkış İşlemi
  public function logout(Request $request)
  {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Çıkış yapıldı');

  }
  // Statik Kullanıcı Ekle
  public function create(Request $request)
  {
    $user = new User();
    $user->company_id = 1;
    $user->name = "Selimcan Gürsu";
    $user->role_id = 1;
    $user->status = "Aktif";
    $user->email  = "selimcangursu@wikywatch.com.tr";
    $user->deparment_id = 1;
    $user->password = bcrypt('123456');
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Yeni Kullanıcı Başarıyla Kaydedildi!'
    ]);


  }

}
