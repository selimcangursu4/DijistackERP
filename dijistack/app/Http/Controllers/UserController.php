<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class UserController extends Controller
{
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


    // Çıkış işlemi
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Çıkış yapıldı');

    }
}
