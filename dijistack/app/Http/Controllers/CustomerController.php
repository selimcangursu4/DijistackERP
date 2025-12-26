<?php

namespace App\Http\Controllers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Exception;

class CustomerController extends Controller
{
    // Yeni Teknik Servis Kaydı Oluşturda Müşteri Arama
    public function technicalServiceCustomerSearch(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $search = trim($request->q);

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $customers = Customer::where("company_id", $companyId)
            ->where(function ($query) use ($search) {
                $query
                    ->where("fullname", "LIKE", "%{$search}%")
                    ->orWhere("company_name", "LIKE", "%{$search}%");
            })
            ->orderBy("fullname")
            ->limit(10)
            ->get();

         return response()->json(
            $customers->map(function ($c) {
                return [
                    "id" => $c->id,
                    "name" => trim($c->fullname . " — " . $c->company_name),
                    "phone" => $c->phone,
                    "email" => $c->email,
                ];
            })
        );
    }
    // Yeni Teknik Servis Kaydında Müşteri Oluştur
    public function technicalServiceCustomerStore(Request $request)
    {
        try {
            $customer = Customer::create([
                "company_id" => auth()->user()->company_id,
                "customer_type_id" => $request->customer_type_id,
                "fullname" => $request->fullname,
                "company_name" => $request->company_name,
                "email" => $request->email,
                "phone" => $request->phone,
                "phone_secondary" => $request->phone_secondary,
                "address" => $request->address,
                "country_id" => $request->country_id,
                "city_id" => $request->city_id,
                "district_id" => $request->district_id,
                "postal_code" => $request->postal_code,
                "tax_office" => $request->tax_office,
                "tax_number" => $request->tax_number,
                "notes" => $request->notes,
                "customer_status_id" => 1,
                "created_by" => auth()->id(),
                "customer_preferred_contact_method_id" =>
                    $request->customer_preferred_contact_method_id,
            ]);

            return response()->json([
                "success" => true,
                "id" => $customer->id,
                "name" => $customer->fullname ?: $customer->company_name,
            ]);
        } catch (\Throwable $e) {
            Log::error("Müşteri kayıt hatası", [
                "user_id" => auth()->id(),
                "request" => $request->all(),
                "error" => $e->getMessage(),
                "file" => $e->getFile(),
                "line" => $e->getLine(),
            ]);

            return response()->json(
                [
                    "success" => false,
                    "message" =>
                        "Müşteri oluşturulamadı. Sistem yöneticisi bilgilendirildi.",
                ],
                500
            );
        }
    }
}
