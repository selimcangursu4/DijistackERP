<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TechnicalService;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceFaultCategory;
use App\Models\ServicePriorityStatus;
use App\Models\ServiceStorageLocation;
use App\Models\ServiceDeliveryMethod;
use App\Models\ServiceTicket;


class TechnicalServiceController extends Controller
{
    public function list($domain)
    {
        if (!can("technical-service/list", "read")) {
            return view('no-authority');
        }
        $company = auth()->user()->company_id;
        return view('technical-service.list', compact('company'));
    }

    public function create($domain)
    {
        if (!can("technical-service/create", "read")) {
            return view('no-authority');
        }
        
        $company = auth()->user()->company_id;
        $customers = Customer::where('company_id', $company)->get();
        $products = Product::where('company_id', $company)->get();  
        $faultCategories = ServiceFaultCategory::where('company_id', $company)->get();
        $servicePriorityStatus = ServicePriorityStatus::all(); 
        $serviceStorageLocation = ServiceStorageLocation::where('status', 'Aktif')
        ->where('company_id', $company)
        ->get();
        $serviceDeliveryMethods = ServiceDeliveryMethod::where('company_id',$company)->get();
        $serviceTicket = ServiceTicket::where('company_id',$company)->where('status',"Aktif")->get();

        return view('technical-service.create',compact('company','customers','products','faultCategories','servicePriorityStatus','serviceStorageLocation','serviceDeliveryMethods','serviceTicket'));
    }
}
