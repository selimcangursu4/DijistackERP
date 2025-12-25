<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Models\CompanyModule;

class DashboardController extends Controller
{
    public function index($domain)
    {
        $user = Auth::user();
        $company = Company::where('domain', $domain)->firstOrFail();

        // Domain güvenlik kontrolü
        if ($user->company_id !== $company->id) {
            abort(403, 'Bu alana erişim yetkiniz yok');
        }

        return view('dashboard', [
            'userCompany' => $user,
            'company' => $company,
        ]);
    }
}
