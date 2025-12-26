<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Cities;
use App\Models\District;

class LocationController extends Controller
{
    public function countries()
    {
        return Country::orderBy('baslik')->get();
    }

    public function cities($countryId)
    {
        return Cities::where('ulke_id', $countryId)
            ->orderBy('baslik')
            ->get();
    }

    public function districts($cityId)
    {
        return District::where('sehir_id', $cityId)
            ->orderBy('baslik')
            ->get();
    }
}
