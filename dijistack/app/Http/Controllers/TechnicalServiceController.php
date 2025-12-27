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
use App\Models\Country;
use App\Models\ServiceWarranty;
use App\Models\ServiceStatus;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class TechnicalServiceController extends Controller
{
    // Teknik Servis Kayıtlarının Listelenmesi Sayfası
    public function list($domain)
    {
        if (!can("technical-service/list", "read")) {
            return view("no-authority");
        }
        $company = auth()->user()->company_id;
        $products = Product::where('company_id',$company)->get();
        $faultCategories = ServiceFaultCategory::where('company_id',$company)->get();
        $priorityStatus = ServicePriorityStatus::all();
        $serviceStatus = ServiceStatus::where('company_id',$company)->get();
        return view("technical-service.list", compact("company","products","faultCategories","priorityStatus","serviceStatus"));
    }
    // Teknik Servis Kaydı Oluşturma Sayfası
    public function create($domain)
    {
        if (!can("technical-service/create", "read")) {
            return view("no-authority");
        }

        $company = auth()->user()->company_id;
        $customers = Customer::where("company_id", $company)->get();
        $products = Product::where("company_id", $company)->get();
        $faultCategories = ServiceFaultCategory::where(
            "company_id",
            $company
        )->get();
        $servicePriorityStatus = ServicePriorityStatus::all();
        $serviceStorageLocation = ServiceStorageLocation::where(
            "status",
            "Aktif"
        )
            ->where("company_id", $company)
            ->get();
        $serviceDeliveryMethods = ServiceDeliveryMethod::where(
            "company_id",
            $company
        )->get();
        $serviceTicket = ServiceTicket::where("company_id", $company)
            ->where("status", "Aktif")
            ->get();
        $countries = Country::orderBy("baslik")->get();

        return view(
            "technical-service.create",
            compact(
                "company",
                "customers",
                "products",
                "faultCategories",
                "servicePriorityStatus",
                "serviceStorageLocation",
                "serviceDeliveryMethods",
                "serviceTicket",
                "countries"
            )
        );
    }
    // Teknik Servis Kaydı Oluşturma Post İşlemi
    public function store(Request $request, $domain)
    {
        if (!can("technical-service/create", "create")) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Bu islemi yapmaya yetkiniz yok",
                ],
                403
            );
        }

        try {
            // 1) Teknik Servis Kaydi Olustur
            $startDate = Carbon::now();
            $estimatedDate = $startDate->copy();

            $addedDays = 0;
            while ($addedDays < 21) {
                $estimatedDate->addDay();

                if (!$estimatedDate->isWeekend()) {
                    $addedDays++;
                }
            }

            $service = TechnicalService::create([
                "company_id" => auth()->user()->company_id,
                "customer_id" => $request->customer_id,
                "product_id" => $request->product_id,
                "serial_number" => $request->imei,
                "service_fault_category_id" => $request->fault_category_id,
                "fault_description" => $request->fault_description,
                "estimated_completion_date" => $estimatedDate->format("Y-m-d"),
                "service_priority_id" => $request->service_priority_id,
                "service_status_id" => 1, // Varsayilan: Kayit Acildi
                "rack_section_id" => $request->rack_section_id,
                "invoice_date" => $request->invoice_date,
                "delivery_method_id" => $request->delivery_method_id,
                "notes" => $request->additional_note,
                "user_id" => auth()->id(),
                "service_ticket" => $request->service_ticket,
            ]);
            // 2) Garanti Hesaplama (Fatura Tarihi + 2 Yil)
            if ($request->invoice_date && $request->imei) {
                $exists = ServiceWarranty::where("imei", $request->imei)
                    ->where("product_id", $request->product_id)
                    ->exists();

                // Eger ayni IMEI + ayni urun zaten varsa -> hicbir sey yapma
                if (!$exists) {
                    $invoiceDate = Carbon::parse($request->invoice_date);
                    $warrantyEndDate = $invoiceDate->copy()->addYears(2);

                    $warrantyStatus = now()->lte($warrantyEndDate)
                        ? "Garanti Var"
                        : "Garanti Yok";

                    ServiceWarranty::create([
                        "company_id" => auth()->user()->company_id,
                        "product_id" => $request->product_id,
                        "imei" => $request->imei,
                        "invoice_date" => $invoiceDate,
                        "warranty_end_date" => $warrantyEndDate,
                        "warranty_status" => $warrantyStatus,
                    ]);
                }
            }
            return response()->json([
                "success" => true,
                "message" => "Servis kaydi basariyla olusturuldu",
            ]);
        } catch (\Throwable $th) {
            \Log::error($th);

            return response()->json(
                [
                    "success" => false,
                    "message" => "Islem sirasinda hata olustu",
                ],
                500
            );
        }
    }
    // Teknik Servis Kayıtlarını Listeleme İşlemi
    public function fetch(Request $request)
    {
        $query = TechnicalService::with([
            "customer",
            "product",
            "faultCategory",
            "priority",
            "status",
        ])->where("company_id", auth()->user()->company_id);

        // Filtreler
        if ($request->service_id) {
            $query->where("id", $request->service_id);
        }

        if ($request->product_id) {
            $query->where("product_id", $request->product_id);
        }

        if ($request->imei) {
            $query->where("serial_number", "like", "%{$request->imei}%");
        }

        if ($request->fault_category_id) {
            $query->where(
                "service_fault_category_id",
                $request->fault_category_id
            );
        }

        if ($request->priority_status_id) {
            $query->where("service_priority_id", $request->priority_status_id);
        }

        if ($request->service_status_id) {
            $query->where("service_status_id", $request->service_status_id);
        }

        if ($request->created_at) {
            $query->whereDate("created_at", $request->created_at);
        }

        return DataTables::of($query)
       ->addColumn("actions", function ($row) {

        $editUrl = route('technical-service.edit', [
            'domain' => request()->route('domain'),
            'id'     => $row->id
        ]);

        return '
        <div class="d-flex gap-1">
            <a href="'.$editUrl.'" class="btn btn-light-success icon-btn" title="Düzenle">
                <i class="ti ti-edit text-success"></i>
            </a>

            <button class="btn btn-light-danger icon-btn delete-btn" 
                    data-id="'.$row->id.'" 
                    title="Sil">
                <i class="ti ti-trash"></i>
            </button>
        </div>';
      })
      ->rawColumns(["actions"])
      ->make(true);

    }
    // Teknik Servis Kayıt Detay Sayfası
    public function edit($domain,$id)
    {
         if (!can("technical-service/list", "read")) {
            return view("no-authority");
        }
        $company = auth()->user()->company_id;
        $service = TechnicalService::where('company_id', $company)
                ->where('id', $id)
                ->firstOrFail();
        $customers = Customer::select('customers.*', 'ulkeler.baslik as ulke', 'sehirler.baslik as sehir', 'ilceler.baslik as ilce')
        ->leftJoin('ulkeler', 'customers.country_id', '=', 'ulkeler.id')
        ->leftJoin('sehirler', 'customers.city_id', '=', 'sehirler.id')
        ->leftJoin('ilceler', 'customers.district_id', '=', 'ilceler.id')
        ->where('customers.id', $service->customer_id)
        ->first();

        return view('technical-service.edit',compact('service','customers'));
    }
}
