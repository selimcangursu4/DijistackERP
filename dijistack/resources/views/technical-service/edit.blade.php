@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                <i class="iconoir-send"></i> SMS Gönder
            </button>
            <button class="btn btn-warning btn-sm d-flex align-items-center gap-1"><i class="iconoir-pc-check"></i> Öncelik
                Talep</button>
            <button class="btn btn-secondary btn-sm d-flex align-items-center gap-1"><i class="iconoir-printing-page"></i>
                Servis Formu</button>
            <button class="btn btn-success btn-sm d-flex align-items-center gap-1"><i class="iconoir-user-badge-check"></i>
                Teknisyen Ata</button>
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
                <span class="badge bg-danger">YÜKSEK</span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card border-primary">
                <div class="card-header">
                    <h5> <small>Kalan Yasal Süre</small></h5>
                </div>
                <div class="card-body text-center">
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 50%;" aria-valuenow="50"
                            aria-valuemin="0" aria-valuemax="100">
                            5 Gün Kaldı
                        </div>
                    </div>
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
                        <h5>Müşteri Bilgileri</h5>
                        <div class="text-muted small">Müşteri bilgileri burada gösterilecek.</div>
                    </div>

                    <div class="tab-pane fade" id="tabDevice">
                        <h5>Garanti & Cihaz</h5>
                        <div class="text-muted small">Garanti ve cihaz detayları burada gösterilecek.</div>
                    </div>

                    <div class="tab-pane fade" id="tabService">
                        <h5>Teknik Servis Detayları</h5>
                        <div class="text-muted small">Servis teknik detayları burada yer alacak.</div>
                    </div>

                    <div class="tab-pane fade" id="tabHistory">
                        <h5>Servis İşlem Geçmişi</h5>
                        <div class="text-muted small">Tüm işlem geçmişi zaman çizelgesi.</div>
                    </div>

                    <div class="tab-pane fade" id="tabNotes">
                        <h5>Servis Notları</h5>
                        <div class="text-muted small">Teknisyen ve yönetici notları.</div>
                    </div>

                    <div class="tab-pane fade" id="tabSms">
                        <h5>SMS Kayıtları</h5>
                        <div class="text-muted small">Müşteriye gönderilen SMS geçmişi.</div>
                    </div>

                    <div class="tab-pane fade" id="tabRequests">
                        <h5>Oluşturulan Talepler</h5>
                        <div class="text-muted small">Parça, iade ve ek iş talepleri.</div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
