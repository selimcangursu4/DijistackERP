<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TechnicalService;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Module;
use App\Models\ServiceFaultCategory;
use App\Models\ServicePriorityStatus;
use App\Models\ServiceStorageLocation;
use App\Models\ServiceDeliveryMethod;
use App\Models\ServiceTicket;
use App\Models\Country;
use App\Models\ServiceWarranty;
use App\Models\ServiceStatus;
use App\Models\ServiceActivities;
use App\Models\ServiceRecordNote;
use App\Models\SmsLog;
use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestStatus;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class TechnicalServiceController extends Controller
{
    // Teknik Servis Kayıtlarının Listelenmesi Sayfası
    public function list($domain)
    {
        if (!can("technical-service/list", "read")) {
            return view("no-authority");
        }
        $company = auth()->user()->company_id;
        $products = Product::where("company_id", $company)->get();
        $faultCategories = ServiceFaultCategory::where(
            "company_id",
            $company
        )->get();
        $priorityStatus = ServicePriorityStatus::all();
        $serviceStatus = ServiceStatus::where("company_id", $company)->get();
        return view(
            "technical-service.list",
            compact(
                "company",
                "products",
                "faultCategories",
                "priorityStatus",
                "serviceStatus"
            )
        );
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
                $editUrl = route("technical-service.edit", [
                    "domain" => request()->route("domain"),
                    "id" => $row->id,
                ]);

                return '
        <div class="d-flex gap-1">
            <a href="' .
                    $editUrl .
                    '" class="btn btn-light-success icon-btn" title="Düzenle">
                <i class="ti ti-edit text-success"></i>
            </a>

            <button class="btn btn-light-danger icon-btn delete-btn" 
                    data-id="' .
                    $row->id .
                    '" 
                    title="Sil">
                <i class="ti ti-trash"></i>
            </button>
        </div>';
            })
            ->rawColumns(["actions"])
            ->make(true);
    }
    // Teknik Servis Kayıt Detay Sayfası
    public function edit($domain, $id)
    {
        if (!can("technical-service/list", "read")) {
            return view("no-authority");
        }
        $company = auth()->user()->company_id;
        $service = TechnicalService::select([
            "technical_services.*",
            "customers.fullname as customer_name",
            "customers.company_name as company_name",
            "customers.phone as customer_phone",
            "products.name as product_name",
            "service_fault_categories.name as fault_category",
            "service_priority_statues.name as priority_status",
            "service_statues.name as service_status",
            "service_delivery_methods.name as delivery_methods",
            // Storage Location (rack - shelf - bin birlestiriliyor)
            DB::raw(
                "CONCAT(service_storage_locations.rack,' / ',service_storage_locations.shelf,' / ',service_storage_locations.bin) as storage_location"
            ),
            "users.name as created_by_user",
        ])
            ->leftJoin(
                "customers",
                "technical_services.customer_id",
                "=",
                "customers.id"
            )
            ->leftJoin(
                "products",
                "technical_services.product_id",
                "=",
                "products.id"
            )
            ->leftJoin(
                "service_fault_categories",
                "technical_services.service_fault_category_id",
                "=",
                "service_fault_categories.id"
            )
            ->leftJoin(
                "service_priority_statues",
                "technical_services.service_priority_id",
                "=",
                "service_priority_statues.id"
            )
            ->leftJoin(
                "service_statues",
                "technical_services.service_status_id",
                "=",
                "service_statues.id"
            )
            ->leftJoin(
                "service_storage_locations",
                "technical_services.rack_section_id",
                "=",
                "service_storage_locations.id"
            )
            ->leftJoin("users", "technical_services.user_id", "=", "users.id")
            ->leftJoin(
                "service_delivery_methods",
                "technical_services.delivery_method_id",
                "=",
                "service_delivery_methods.id"
            )
            ->where("technical_services.company_id", $company)
            ->where("technical_services.id", $id)
            ->firstOrFail();
        $customers = Customer::select(
            "customers.*",
            "ulkeler.baslik as ulke",
            "sehirler.baslik as sehir",
            "ilceler.baslik as ilce"
        )
            ->leftJoin("ulkeler", "customers.country_id", "=", "ulkeler.id")
            ->leftJoin("sehirler", "customers.city_id", "=", "sehirler.id")
            ->leftJoin("ilceler", "customers.district_id", "=", "ilceler.id")
            ->where("customers.id", $service->customer_id)
            ->first();
        
            $remainingDays = null;
$progressPercent = 0;

if ($service->estimated_completion_date) {
    $today = Carbon::today();
    $estimatedDate = Carbon::parse($service->estimated_completion_date);

    $totalDays = Carbon::parse($service->created_at)->diffInDays($estimatedDate);
    $remainingDays = $today->diffInDays($estimatedDate, false);

    $usedDays = $totalDays - $remainingDays;

    if ($totalDays > 0) {
        $progressPercent = min(100, max(0, ($usedDays / $totalDays) * 100));
    }
}
        return view(
            "technical-service.edit",
            compact("service", "customers", "domain","remainingDays","progressPercent")
        );
    }
    // Teknik Servis Detay Sayfası Garanti Durumu Listelenmesi
    public function serviceProductWarrantyStatuses($domain, $id)
    {
        try {
            $company = auth()->user()->company_id;
            $service = TechnicalService::where(
                "company_id",
                $company
            )->findOrFail($id);
            $warranties = ServiceWarranty::select([
                "service_warranties.id",
                "service_warranties.imei",
                "service_warranties.invoice_date",
                "service_warranties.warranty_end_date",
                "service_warranties.warranty_status",
                "service_warranties.product_id",
            ])
                ->with("product:id,name")
                ->where("service_warranties.company_id", $company)
                ->where("service_warranties.imei", $service->serial_number);

            return DataTables::of($warranties)
                ->addIndexColumn()

                ->addColumn("product_name", function ($row) {
                    return $row->product->name ?? "Bilinmiyor";
                })

                ->editColumn("warranty_status", function ($row) {
                    $status = $row->warranty_status ?? "Belirsiz";
                    $badgeClass =
                        $status === "Garanti Var" ? "bg-success" : "bg-danger";

                    return '<span class="badge ' .
                        $badgeClass .
                        '">' .
                        $status .
                        "</span>";
                })

                ->editColumn("invoice_date", function ($row) {
                    return $row->invoice_date
                        ? Carbon::parse($row->invoice_date)->format("d.m.Y")
                        : "-";
                })

                ->editColumn("warranty_end_date", function ($row) {
                    return $row->warranty_end_date
                        ? Carbon::parse($row->warranty_end_date)->format(
                            "d.m.Y"
                        )
                        : "-";
                })

                ->rawColumns(["warranty_status"])
                ->make(true);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    "error" => true,
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
    }
    // Teknik Servis Detay Sayfasında Servis Aktivitelerinin Listelenmesi
    public function serviceActivitiesFetch(Request $request, $domain, $id)
    {
        $activities = ServiceActivities::select([
            "service_activities.id",
            "service_activities.description",
            "service_activities.created_at",
            "users.name as user_name",
            "service_statues.name as status_name",
        ])
            ->leftJoin("users", "service_activities.user_id", "=", "users.id")
            ->leftJoin(
                "service_statues",
                "service_activities.service_status_id",
                "=",
                "service_statues.id"
            )
            ->where("service_activities.company_id", auth()->user()->company_id)
            ->where("service_activities.service_id", $id)
            ->orderBy("service_activities.created_at", "desc");

        return DataTables::of($activities)
            ->addIndexColumn()
            ->editColumn("created_at", function ($row) {
                return Carbon::parse($row->created_at)->format("d.m.Y H:i");
            })
            ->filterColumn("user_name", function ($query, $keyword) {
                $query->whereRaw("users.name like ?", ["%{$keyword}%"]);
            })
            ->filterColumn("status_name", function ($query, $keyword) {
                $query->whereRaw("service_statues.name like ?", [
                    "%{$keyword}%",
                ]);
            })
            ->make(true);
    }
    // Teknik Servis Detay Sayfası Servis Notlarını Listele
    public function serviceNotesFetch(Request $request, $domain, $id)
    {
        try {
            $notes = ServiceRecordNote::select([
                "service_record_notes.id",
                "service_record_notes.notes",
                "service_record_notes.created_at",
                "users.name as user_name",
            ])
                ->leftJoin(
                    "users",
                    "users.id",
                    "=",
                    "service_record_notes.user_id"
                )
                ->where(
                    "service_record_notes.company_id",
                    auth()->user()->company_id
                )
                ->where("service_record_notes.service_id", $id)
                ->orderBy("service_record_notes.created_at", "desc");

            return DataTables::of($notes)
                ->addIndexColumn()
                ->editColumn("created_at", function ($row) {
                    return $row->created_at->format("d.m.Y H:i");
                })
                ->make(true);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    "error" => true,
                    "message" => $e->getMessage(),
                ],
                500
            );
        }
    }
    // Teknik Servis Detay Sms Log Kayıtları Listele
    public function singleFetchSmsLog(Request $request, $domain, $id)
    {
        try {
            $companyId = auth()->user()->company_id;

            $query = SmsLog::where("company_id", $companyId)
                ->where("module_record_id", $id)
                ->latest();

            return DataTables::of($query)

                ->addColumn("module_name", function ($row) {
                    return optional(Module::find($row->module_id))->name ?? "-";
                })

                ->addColumn("sender_name", function ($row) {
                    return optional(User::find($row->sent_by))->name ?? "-";
                })

                ->addColumn("status_badge", function ($row) {
                    return match ($row->status) {
                        "Gönderildi"
                            => '<span class="badge bg-success">Gönderildi</span>',
                        "Beklemede"
                            => '<span class="badge bg-warning">Beklemede</span>',
                        "Hata" => '<span class="badge bg-danger">Hata</span>',
                    };
                })

                ->editColumn("created_at", function ($row) {
                    return $row->created_at->format("d.m.Y H:i");
                })

                ->rawColumns(["status_badge"])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                "draw" => intval($request->draw),
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => $e->getMessage(),
            ]);
        }
    }
    // Teknik Servis Detay Kayıt Hakkında Oluşturulan Talepler
    public function singleServiceRequestFetch(Request $request, $domain, $id)
    {
      $companyId = auth()->user()->company_id;

      $requests = ServiceRequest::where('company_id', $companyId)
        ->where('service_id', $id)
        ->with(['creator','statusRelation','priorityRelation','moduleRelation']);

       return DataTables::of($requests)

        ->editColumn('creator', fn($row) => $row->creator->name ?? '-')
        ->editColumn('status', fn($row) => $row->statusRelation->name ?? '-')
        ->editColumn('priority', fn($row) => $row->priorityRelation->name ?? '-')
        ->editColumn('module', fn($row) => $row->moduleRelation->name ?? '-')
        ->editColumn('created_at', fn($row) => $row->created_at->format('d.m.Y H:i'))
        ->addColumn('action', function ($row) {
            return '
                <a href="#" 
                   class="btn btn-sm btn-primary">Detay</a>
            ';
        })

        ->rawColumns(['action'])
        ->make(true);
    }
}
