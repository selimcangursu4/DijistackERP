<?php

namespace App\Http\Controllers;
use App\Models\ServiceWarranty;
use Illuminate\Http\Request;

class ServiceWarrantyController extends Controller
{
    public function checkImei(Request $request)
    {
       $warranty = ServiceWarranty::where('imei', $request->imei)->first();

       if ($warranty) {
        return response()->json([
            'found' => true,
            'invoice_date' => $warranty->invoice_date
        ]);
       }

      return response()->json(['found' => false]);
    }
}
