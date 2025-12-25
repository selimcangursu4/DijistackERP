<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function technicalServiceCustomerSearch(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $query = $request->q;

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where('company_id', $companyId)
            ->where(function($q) use ($query){
                $q->where('fullname', 'like', "%{$query}%")
                  ->orWhere('company_name', 'like', "%{$query}%")
                  ->orWhere('email', 'like', "%{$query}%")
                  ->orWhere('phone', 'like', "%{$query}%")
                  ->orWhere('phone_secondary', 'like', "%{$query}%");
            })
            ->orderBy('fullname')
            ->limit(10)
            ->get([
                'id',
                'fullname',
                'company_name',
                'phone',
                'email'
            ]);

        return response()->json(
            $customers->map(function($c){
                return [
                    'id'   => $c->id,
                    'name' => $c->fullname ?: $c->company_name ,
                    'phone'=> $c->phone,
                    'email'=> $c->email
                ];
            })
        );
    }
}
