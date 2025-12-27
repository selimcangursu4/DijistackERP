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
                        <table id="warrantyTable" class="table table-bordered table-striped table-hover">
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
                                <tr>
                                    <td>1</td>
                                    <td>Wiky Watch 5 Plus</td>
                                    <td>123456789012345</td>
                                    <td>2025-01-10</td>
                                    <td>2026-01-10</td>
                                    <td><span class="badge bg-success">Garanti Var</span></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Wiky Watch S</td>
                                    <td>987654321098765</td>
                                    <td>2024-07-05</td>
                                    <td>2025-07-05</td>
                                    <td><span class="badge bg-danger">Garanti Yok</span></td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Wiky Tablet X</td>
                                    <td>456789123456789</td>
                                    <td>2025-03-15</td>
                                    <td>2026-03-15</td>
                                    <td><span class="badge bg-success">Garanti Var</span></td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Wiky Phone Z</td>
                                    <td>321654987321654</td>
                                    <td>2024-12-01</td>
                                    <td>2025-12-01</td>
                                    <td><span class="badge bg-success">Garanti Var</span></td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Wiky Tablet S</td>
                                    <td>654987321654987</td>
                                    <td>2024-10-20</td>
                                    <td>2025-10-20</td>
                                    <td><span class="badge bg-danger">Garanti Yok</span></td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="tab-pane fade" id="tabService">
                        <h5 class="mb-3">Teknik Servis Detayları</h5>



                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servis ID:</strong></div>
                            <div class="col-md-8"><span class="text-muted">#1024</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Müşteri:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Ahmet Yılmaz</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Ürün:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Wiky Watch 5 Plus</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Seri Numarası:</strong></div>
                            <div class="col-md-8"><span class="text-muted">123456789012345</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Arıza Kategorisi:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Donanım Sorunu</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Arıza Açıklaması:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Ekran donuyor ve dokunmatik tepki
                                    vermiyor.</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servis Önceliği:</strong></div>
                            <div class="col-md-8"><span class="badge bg-warning">Yüksek</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servis Durumu:</strong></div>
                            <div class="col-md-8"><span class="badge bg-primary">Devam Ediyor</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Raf/Bölüm:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Raf 3 / Bölüm B</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Tahmini Tamamlanma:</strong></div>
                            <div class="col-md-8"><span class="text-muted">2025-01-20</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Gerçek Tamamlanma:</strong></div>
                            <div class="col-md-8"><span class="text-muted">-</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Fatura Tarihi:</strong></div>
                            <div class="col-md-8"><span class="text-muted">2025-01-10</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Parça Gerekiyor:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Evet</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Teslimat Yöntemi:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Kargo</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Ek Notlar:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Müşteri ekran değişimi istiyor, hızlı
                                    teslimat talep edildi.</span></div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Servisi Yapan Kullanıcı:</strong></div>
                            <div class="col-md-8"><span class="text-muted">Teknisyen: Ali Vural</span></div>
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
                                <tr>
                                    <td>1</td>
                                    <td>Servis kaydı oluşturuldu</td>
                                    <td>Ali Vural</td>
                                    <td><span class="badge bg-primary">Devam Ediyor</span></td>
                                    <td>2025-01-10 10:15</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Arıza tespit edildi: Ekran donuyor</td>
                                    <td>Ali Vural</td>
                                    <td><span class="badge bg-warning">Beklemede</span></td>
                                    <td>2025-01-10 14:20</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Parça siparişi verildi</td>
                                    <td>Ayşe Demir</td>
                                    <td><span class="badge bg-success">Tamamlandı</span></td>
                                    <td>2025-01-11 09:05</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Servis tamamlandı</td>
                                    <td>Ali Vural</td>
                                    <td><span class="badge bg-success">Tamamlandı</span></td>
                                    <td>2025-01-15 16:30</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Müşteri bilgilendirildi</td>
                                    <td>Ayşe Demir</td>
                                    <td><span class="badge bg-primary">Devam Ediyor</span></td>
                                    <td>2025-01-15 17:00</td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="tab-pane fade" id="tabNotes">
                        <h5 class="mb-3">Servis Notları</h5>
                        <table id="notesTable" class="table table-bordered table-striped table-hover">
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


                        <table id="smsTable" class="table table-bordered table-striped table-hover">
                            <thead class="table">
                                <tr>
                                    <th>#</th>
                                    <th>Modül</th>
                                    <th>Kayıt ID</th>
                                    <th>Alıcı</th>
                                    <th>Telefon</th>
                                    <th>Mesaj</th>
                                    <th>Gönderen</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Teknik Servis</td>
                                    <td>102</td>
                                    <td>Ahmet Yılmaz</td>
                                    <td>05551234567</td>
                                    <td>Servis kaydınız oluşturuldu.</td>
                                    <td>Ali Vural</td>
                                    <td><span class="badge bg-primary">Gönderildi</span></td>
                                    <td>2025-01-10 10:15</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Satış</td>
                                    <td>205</td>
                                    <td>Ayşe Demir</td>
                                    <td>05559876543</td>
                                    <td>Satın aldığınız ürün kargoya verildi.</td>
                                    <td>Ayşe Demir</td>
                                    <td><span class="badge bg-success">Gönderildi</span></td>
                                    <td>2025-01-10 14:20</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>İnsan Kaynakları</td>
                                    <td>12</td>
                                    <td>Mehmet Kara</td>
                                    <td>05550123456</td>
                                    <td>Toplantı hatırlatması: 15:00</td>
                                    <td>HR Admin</td>
                                    <td><span class="badge bg-warning">Beklemede</span></td>
                                    <td>2025-01-11 09:05</td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Teknik Servis</td>
                                    <td>103</td>
                                    <td>Fatma Yıldız</td>
                                    <td>05552345678</td>
                                    <td>Parça değişimi tamamlandı.</td>
                                    <td>Ali Vural</td>
                                    <td><span class="badge bg-primary">Gönderildi</span></td>
                                    <td>2025-01-12 11:45</td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Satış</td>
                                    <td>208</td>
                                    <td>Emre Aydın</td>
                                    <td>05553456789</td>
                                    <td>Fatura oluşturuldu.</td>
                                    <td>Ayşe Demir</td>
                                    <td><span class="badge bg-danger">Hata</span></td>
                                    <td>2025-01-12 13:30</td>
                                </tr>
                            </tbody>
                        </table>
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
    <script>
        $(document).ready(function() {
            $('#warrantyTable').DataTable({
                "pageLength": 5,
                "lengthChange": false,
                "ordering": true,
                "searching": false,
                "info": true,
                "autoWidth": false,
                "language": {
                    "search": "Ara:",
                    "paginate": {
                        "previous": "Önceki",
                        "next": "Sonraki"
                    },
                    "info": "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
                    "infoEmpty": "0 kayıttan 0 - 0 gösteriliyor",
                    "emptyTable": "Tabloda veri bulunmamaktadır"
                }
            });
            $('#activityTable').DataTable({
                "pageLength": 5,
                "lengthChange": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "searching": false,
                "language": {
                    "search": "Ara:",
                    "paginate": {
                        "previous": "Önceki",
                        "next": "Sonraki"
                    },
                    "info": "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
                    "infoEmpty": "0 kayıttan 0 - 0 gösteriliyor",
                    "emptyTable": "Tabloda veri bulunmamaktadır"
                }
            });
            $('#notesTable').DataTable({
                "pageLength": 5,
                "lengthChange": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "searching": false,
                "language": {
                    "paginate": {
                        "previous": "Önceki",
                        "next": "Sonraki"
                    },
                    "info": "_TOTAL_ kayıttan _START_ - _END_ gösteriliyor",
                    "infoEmpty": "0 kayıttan 0 - 0 gösteriliyor",
                    "emptyTable": "Tabloda veri bulunmamaktadır"
                }
            });
        });
    </script>
@endsection
