@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" data-bs-toggle="modal"
                data-bs-target="#smsModal">
                <i class="iconoir-send"></i> SMS Gönder
            </button>
            <button class="btn btn-warning btn-sm d-flex align-items-center gap-1"><i class="iconoir-pc-check"></i> Öncelik
                Talep</button>
            <button class="btn btn-secondary btn-sm d-flex align-items-center gap-1"><i class="iconoir-printing-page"></i>
                Servis Formu</button>
            <button class="btn btn-success btn-sm d-flex align-items-center gap-1"><i class="iconoir-attachment"></i> İşlem
                Ekle</button>
            <button class="btn btn-success btn-sm d-flex align-items-center gap-1"><i class="iconoir-archive"></i> Dosya
                Yükle</button>
            <button class="btn btn-success btn-sm d-flex align-items-center gap-1"><i class="iconoir-notes"></i> Servis
                Notu</button>
            <button class="btn btn-danger btn-sm d-flex align-items-center gap-1"><i class="iconoir-minus-hexagon"></i>
                Kaydı Sil</button>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="alert alert-white d-flex justify-content-between align-items-center">
                <strong class="text-dark">Servis Aciliyeti:</strong>
                @php
                    $priority = strtoupper($service->priority_status);

                    $badgeClass = match ($priority) {
                        'ACİL', 'YÜKSEK' => 'bg-danger',
                        'ORTA' => 'bg-warning',
                        'DÜŞÜK' => 'bg-success',
                        default => 'bg-secondary',
                    };
                @endphp

                <span class="badge {{ $badgeClass }} px-3 py-2">
                    {{ $service->priority_status }}
                </span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-header">
                    <h5> <small>Kalan Yasal Süre</small></h5>
                </div>
                <div class="card-body text-center">
                    @if ($remainingDays !== null)
                        <div class="progress" style="height: 22px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ round($progressPercent) }}%;" aria-valuenow="{{ round($progressPercent) }}"
                                aria-valuemin="0" aria-valuemax="100">
                                {{ $remainingDays > 0 ? $remainingDays . ' Gun Kaldi' : 'Suresi Doldu' }}
                            </div>
                        </div>
                    @else
                        <span class="text-muted">Tahmini bitis tarihi girilmemis</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="list-group shadow-sm" id="serviceTabs" role="tablist">
                <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#tabCustomer">
                    Müşteri Bilgileri
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tabDevice">
                    Garanti & Cihaz
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tabService">
                    Teknik Servis Detayları
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tabHistory">
                    Servis İşlem Geçmişi
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tabNotes">
                    Servis Notları
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tabSms">
                    SMS Kayıtları
                </a>
                <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#tabRequests">
                    Oluşturulan Talepler
                </a>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body tab-content">
                    <div class="tab-pane fade show active" id="tabCustomer">
                        <h5 class="mb-3">Müşteri Bilgileri</h5>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <strong>Müşteri Tipi:</strong>
                                <span class="text-muted">
                                    {{ $customers->customer_type_id == 1 ? 'Bireysel Müşteri' : ($customers->customer_type_id == 2 ? 'Kurumsal Müşteri' : 'Bilinmiyor') }}
                                </span>
                            </div>
                            <div class="col-md-6 mt-2">
                                <strong>Ad Soyad:</strong> <span class="text-muted">{{ $customers->fullname }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Firma Adı:</strong> <span class="text-muted">{{ $customers->company_name }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Email:</strong> <span class="text-muted">{{ $customers->email }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Telefon:</strong> <span class="text-muted">{{ $customers->phone }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>İkincil Telefon:</strong> <span
                                    class="text-muted">{{ $customers->phone_secondary }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Adres:</strong> <span class="text-muted">{{ $customers->address }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <strong>Ülke:</strong> <span
                                    class="text-muted">{{ $customers->ulke ?? 'Bilinmiyor' }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Şehir:</strong> <span
                                    class="text-muted">{{ $customers->sehir ?? 'Bilinmiyor' }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>İlçe:</strong> <span
                                    class="text-muted">{{ $customers->ilce ?? 'Bilinmiyor' }}</span>
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4">
                                <strong>Posta Kodu:</strong> <span class="text-muted">{{ $customers->postal_code }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Vergi Dairesi:</strong> <span
                                    class="text-muted">{{ $customers->tax_office }}</span>
                            </div>
                            <div class="col-md-4">
                                <strong>Vergi No:</strong> <span class="text-muted">{{ $customers->tax_number }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Durum:</strong>
                                <span
                                    class="badge {{ $customers->customer_status_id == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $customers->customer_status_id == 1 ? 'Aktif' : 'Pasif' }}
                                </span>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <strong>Notlar:</strong>
                                <p class="text-muted mb-0">{{ $customers->notes }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabDevice">
                        <h5 class="mb-3">Garanti & Cihaz</h5>
                        <table id="warrantyTable" style="width: 100% !important"
                            class="table table-bordered table-striped table-hover">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Ürün</th>
                                    <th>IMEI</th>
                                    <th>Fatura Tarihi</th>
                                    <th>Garanti Bitiş Tarihi</th>
                                    <th>Garanti Durumu</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="tabService">
                        <h5 class="mb-3">Teknik Servis Detayları</h5>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servis ID:</strong></div>
                            <div class="col-md-8"><span class="text-muted">#{{ $service->id }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Müşteri:</strong></div>
                            <div class="col-md-8">
                                <span class="text-muted">{{ $service->company_name ?? $service->customer_name }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Ürün:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->product_name }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Seri Numarası:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->serial_number }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Arıza Kategorisi:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->fault_category }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Arıza Açıklaması:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->fault_description }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servis Önceliği:</strong></div>
                            <div class="col-md-8">
                                <span class="badge bg-warning">{{ $service->priority_status }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servis Durumu:</strong></div>
                            <div class="col-md-8">
                                <span class="badge bg-primary">{{ $service->service_status }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Raf/Bölüm:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->storage_location ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Tahmini Tamamlanma:</strong></div>
                            <div class="col-md-8">
                                <span class="text-muted">
                                    {{ $service->estimated_completion_date
                                        ? \Carbon\Carbon::parse($service->estimated_completion_date)->format('d.m.Y')
                                        : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Gerçek Tamamlanma:</strong></div>
                            <div class="col-md-8">
                                <span class="text-muted">
                                    {{ $service->actual_completion_date
                                        ? \Carbon\Carbon::parse($service->actual_completion_date)->format('d.m.Y')
                                        : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Fatura Tarihi:</strong></div>
                            <div class="col-md-8">
                                <span class="text-muted">
                                    {{ $service->invoice_date ? \Carbon\Carbon::parse($service->invoice_date)->format('d.m.Y') : '-' }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Parça Gerekiyor:</strong></div>
                            <div class="col-md-8">
                                <span class="text-muted">{{ $service->need_part ? 'Evet' : 'Hayır' }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Teslimat Yöntemi:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->delivery_methods ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Ek Notlar:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->notes ?? '-' }}</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servisi Yapan Kullanıcı:</strong></div>
                            <div class="col-md-8"><span class="text-muted">{{ $service->created_by_user }}</span></div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="tabHistory">
                        <h5 class="mb-3">Servis İşlem Geçmişi</h5>
                        <table id="activityTable" class="table table-bordered table-striped table-hover">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Açıklama</th>
                                    <th>Kullanıcı</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="tabNotes">
                        <h5 class="mb-3">Servis Notları</h5>
                        <table id="notesTable" class="table table-bordered table-striped table-hover w-100">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Not</th>
                                    <th>Oluşturan</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Parça değişimi gerekiyor, müşteri bilgilendirildi.</td>
                                    <td>Ali Vural</td>
                                    <td>2025-01-10 10:15</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Servis önceliği yüksek olarak güncellendi.</td>
                                    <td>Ayşe Demir</td>
                                    <td>2025-01-10 14:20</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Garanti durumu kontrol edildi, geçerli.</td>
                                    <td>Ali Vural</td>
                                    <td>2025-01-11 09:05</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Müşteri bilgilendirildi, teslimat bekleniyor.</td>
                                    <td>Ayşe Demir</td>
                                    <td>2025-01-11 15:30</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Ek not: Ekran değişimi hızlı yapılacak.</td>
                                    <td>Ali Vural</td>
                                    <td>2025-01-12 11:45</td>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                    <div class="tab-pane fade" id="tabSms">
                        <h5 class="mb-3">SMS Kayıtları</h5>
                        <table id="smsTable" class="table table-bordered table-striped table-hover w-100">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Modül</th>
                                    <th>Alıcı</th>
                                    <th>Telefon</th>
                                    <th>Mesaj</th>
                                    <th>Gönderen</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="tabRequests">
                        <h5 class="mb-3">Oluşturulan Talepler</h5>
                        <table id="requestsTable" class="table table-bordered table-striped table-hover w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Başlık</th>
                                    <th>İlgili Birim</th>
                                    <th>Talep Türü</th>
                                    <th>Öncelik</th>
                                    <th>Durum</th>
                                    <th>Oluşturan</th>
                                    <th>Oluşturma Tarihi</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="modal fade" id="smsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white d-flex align-items-center justify-content-between">
                        <span><i class="iconoir-send me-2"></i> Müşteriye SMS Gönder</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Telefon</label>
                                <input type="text" id="phone" class="form-control"
                                    value="{{ $service->customer_phone }}" readonly>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Mesaj</label>
                                <textarea class="form-control" id="message" rows="4" placeholder="Gönderilecek SMS içeriğini yazınız..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                            <button type="button" id="sendSms" class="btn btn-primary">
                                <i class="iconoir-send"></i> Gönder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#warrantyTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('technical-service.warranty-data', ['domain' => request()->route('domain'), 'id' => $service->id]) }}",
                order: [
                    [4, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'product_name',
                        name: 'product.name'
                    },
                    {
                        data: 'imei',
                        name: 'imei'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'warranty_end_date',
                        name: 'warranty_end_date'
                    },
                    {
                        data: 'warranty_status',
                        name: 'warranty_status'
                    }
                ],
            });
            $('#activityTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('technical-service.activities-fetch', ['domain' => request()->route('domain'), 'id' => $service->id]) }}",
                    error: function(xhr, error, thrown) {
                        console.group("DataTable AJAX Error");
                        console.error("Status:", xhr.status);
                        console.error("Response:", xhr.responseText);
                        console.groupEnd();
                    }
                },
                order: [
                    [4, 'desc']
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'description',
                        name: 'service_activities.description'
                    },
                    {
                        data: 'user_name',
                        name: 'users.name'
                    },
                    {
                        data: 'status_name',
                        name: 'service_statues.name'
                    },
                    {
                        data: 'created_at',
                        name: 'service_activities.created_at'
                    }
                ],
                pageLength: 5,
                lengthChange: false,
                ordering: true,
                searching: false,
                info: true,
                autoWidth: false,
                language: {
                    paginate: {
                        previous: "Önceki",
                        next: "Sonraki"
                    },
                    info: "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
                    infoEmpty: "0 kayıttan 0 - 0 gösteriliyor",
                    emptyTable: "Tabloda veri bulunmamaktadır"
                }
            });
            $('#notesTable').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 5,
                lengthChange: false,
                searching: false,
                ajax: "{{ route('technical-service.notes-fetch', ['domain' => request()->route('domain'), 'id' => $service->id]) }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'note',
                        name: 'service_record_notes.note'
                    },
                    {
                        data: 'user_name',
                        name: 'users.name'
                    },
                    {
                        data: 'created_at',
                        name: 'service_record_notes.created_at'
                    }
                ],
                language: {
                    paginate: {
                        previous: "Önceki",
                        next: "Sonraki"
                    },
                    info: "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
                    infoEmpty: "0 kayıttan 0 - 0 gösteriliyor",
                    emptyTable: "Tabloda veri bulunmamaktadır"
                }
            });
            $('#smsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('technical-service.sms-logs-fetch', ['domain' => request()->route('domain'), 'id' => $service->id]) }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'module_name',
                        orderable: false
                    },
                    {
                        data: 'recipient_name'
                    },
                    {
                        data: 'recipient_phone'
                    },
                    {
                        data: 'message'
                    },
                    {
                        data: 'sender_name',
                        orderable: false
                    },
                    {
                        data: 'status_badge',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'created_at'
                    }
                ],
                order: [
                    [0, 'desc']
                ]
            });
            $('#requestsTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('technical-service.request-fetch', ['domain' => request()->route('domain'), 'id' => $service->id]) }}",
                order: [
                    [0, "desc"]
                ],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'title'
                    },
                    {
                        data: "module"
                    },
                    {
                        data: 'request_type'
                    },
                    {
                        data: 'priority'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'creator'
                    },
                    {
                        data: 'created_at'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        

        });
    </script>
@endsection
