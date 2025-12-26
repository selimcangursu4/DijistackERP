@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title text-white mb-0">Yeni Servis Kaydı Oluştur</h5>
                <small>Servis kaydı oluşturmak için müşteri bilgilerini eksiksiz giriniz.</small>
            </div>
            <div class="card-body">
                <form>
                    @csrf
                    <div class="mb-4">
                        <h6 class="mb-2">Müşteri Bilgileri</h6>
                        <p class="text-muted small">
                            Bu bölümde servis kaydı oluşturulacak müşteriyi seçebilirsiniz. Eğer müşteri sistemde mevcut
                            değilse, yeni müşteri oluşturabilirsiniz.
                        </p>
                        <div class="row align-items-end">
                            <div class="col-md-12">
                                <div class="mb-3 position-relative">
                                    <label class="form-label">Müşteri Seçimi</label>
                                    <input type="search" id="customerSearch" class="form-control"
                                        placeholder="Müşteri ara...">
                                    <div id="customerResult" class="list-group position-absolute w-100 shadow d-none"
                                        style="z-index:1000;"></div>
                                </div>
                                <input type="hidden" name="customer_id" id="selectedCustomer">
                                <input type="hidden" name="customer_id" id="selectedCustomer">
                                <div class="col-md-12">
                                    <a href="#" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#createCustomerModal">
                                        <i class="bi bi-person-plus-fill"></i> Yeni Müşteri Oluştur
                                    </a>
                                    <p class="text-muted small mt-2">
                                        Eğer aradığınız müşteri kaydı sistemde bulunmuyorsa, lütfen "Yeni Müşteri Oluştur"
                                        butonunu kullanarak kayıt ekleyiniz.
                                    </p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <h6 class="mb-2">Ürün Bilgileri</h6>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="customer_name" class="form-label">Ürün Bilgisi</label>
                                        <select class="form-select" id="customer_name" name="customer_name">
                                            <option value="">Ürün Seçiniz</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="imei" class="form-label">Ürün İmei Numarası</label>
                                        <input type="text" class="form-control" id="imei" name="imei"
                                            placeholder="Ürün İmei Numarası">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="invoice_date" class="form-label">Fatura Tarihi</label>
                                        <input type="date" class="form-control" id="invoice_date" name="invoice_date">
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <h6 class="mb-2">Arıza Bilgileri</h6>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="fault_category" class="form-label">Ürün Arıza Kategorisi</label>
                                        <select class="form-select" id="fault_category_id" name="fault_category_id">
                                            <option value="">Arıza Kategorisi Seçiniz</option>
                                            @foreach ($faultCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="fault_category" class="form-label">Arıza Açıklaması</label>
                                        <textarea class="form-control" name="fault_description" id="fault_description" cols="10" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <h6 class="mb-2">Diğer Bilgileri</h6>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="service_priority_id" class="form-label">Servis Önceliği</label>
                                        <select class="form-select" id="service_priority_id" name="service_priority_id">
                                            <option value="">Servis Önceliği Seçiniz</option>
                                            @foreach ($servicePriorityStatus as $priority)
                                                <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="rack_section_id" class="form-label">Raf/Bölüm</label>
                                        <select class="form-select" id="rack_section_id" name="rack_section_id">
                                            <option value="">Raf/Bölüm Seçiniz</option>
                                            @foreach ($serviceStorageLocation as $storage)
                                                <option value="{{ $storage->id }}">{{ $storage->rack }}
                                                    {{ $storage->shelf }} {{ $storage->bin }}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="delivery_method_id" class="form-label">Teslimat Yöntemi</label>
                                        <select class="form-select" id="delivery_method_id" name="delivery_method_id">
                                            <option value="">Teslimat Yöntemi Seçiniz</option>
                                            @foreach ($serviceDeliveryMethods as $delivery)
                                                <option value="{{ $delivery->id }}">{{ $delivery->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="additional_note" class="form-label">Ek Not</label>
                                        <textarea class="form-control" name="additional_note" id="additional_note" cols="30" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Servis Etiketleri</label>
                                    <p class="text-muted small">Servis kaydına uygun etiketleri seçiniz.</p>
                                    <div class="mb-3">
                                        <div class="d-flex flex-wrap gap-3">
                                            @foreach ($serviceTicket as $ticket)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="service_ticket[]" id="service_ticket_{{ $ticket['id'] }}"
                                                        value="{{ $ticket['id'] }}">
                                                    <label class="form-check-label"
                                                        for="service_ticket_{{ $ticket['id'] }}">
                                                        {{ $ticket['name'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 float-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Servis Kaydını Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="createCustomerModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered ">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title text-white">Yeni Müşteri Kaydı</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="createCustomerForm">
                    @csrf
                    <div class="modal-body">
                        <h6 class="border-bottom pb-2 mb-3">Genel Bilgiler</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Müşteri Tipi</label>
                                <select class="form-select" name="customer_type_id" id="customer_type_id">
                                    <option value="1">Bireysel</option>
                                    <option value="2">Kurumsal</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" name="fullname" id="fullname" required>
                            </div>
                            <div class="col-md-6 corporate-field d-none">
                                <label class="form-label">Firma Adı</label>
                                <input type="text" class="form-control" name="company_name" id="company_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">E-posta</label>
                                <input type="email" class="form-control" name="email" id="email">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Telefon</label>
                                <input type="text" class="form-control" name="phone" id="phone" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">İkinci Telefon</label>
                                <input type="text" class="form-control" name="phone_secondary" id="phone_secondary">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Tercih Edilen İletişim</label>
                                <select class="form-select" name="customer_preferred_contact_method_id"
                                    id="customer_preferred_contact_method_id">
                                    <option value="1">Telefon</option>
                                    <option value="2">E-posta</option>
                                    <option value="3">WhatsApp</option>
                                </select>
                            </div>
                        </div>
                        <h6 class="border-bottom pb-2 mb-3">Adres Bilgileri</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Adres</label>
                                <textarea class="form-control" name="address" id="address" rows="2"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Ulke</label>
                                <select class="form-select" id="country_id" name="country_id">
                                    <option value="">Seciniz</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ $country->baslik }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sehir</label>
                                <select class="form-select" id="city_id" name="city_id" disabled></select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Ilce</label>
                                <select class="form-select" id="district_id" name="district_id" disabled></select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Posta Kodu</label>
                                <input type="text" class="form-control" name="postal_code" id="postal_code">
                            </div>
                        </div>
                        <h6 class="border-bottom pb-2 mb-3 corporate-field d-none">Kurumsal Bilgiler</h6>
                        <div class="row g-3 mb-4 corporate-field d-none">

                            <div class="col-md-6">
                                <label class="form-label">Vergi Dairesi</label>
                                <input type="text" class="form-control" id="tax_office" name="tax_office">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Vergi No</label>
                                <input type="text" class="form-control" id="tax_number" name="tax_number">
                            </div>
                        </div>
                        <h6 class="border-bottom pb-2 mb-3">Ek Bilgiler</h6>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Notlar</label>
                                <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="button" id="customerInsertButton" class="btn btn-primary">Müşteriyi
                            Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            const domain = "{{ request()->route('domain') }}";
            // Kullanıcı müşteri arama alanına yazdıkça tetiklenir
            $('#customerSearch').on('keyup', function() {
                let query = $(this).val();
                if (query.length < 2) {
                    $('#customerResult').addClass('d-none');
                    return;
                }
                $.ajax({
                    url: "/" + domain + "/customers/search",
                    data: {
                        q: query
                    },
                    success: function(res) {
                        let html = '';
                        if (res.length > 0) {

                            res.forEach(function(item) {
                                html += `
                          <a href="javascript:void(0)" 
                             class="list-group-item list-group-item-action"
                             data-id="${item.id}"
                             data-name="${item.name}">
                             ${item.name} — ${item.phone}
                          </a>`;
                            });
                            $('#customerResult').html(html).removeClass('d-none');

                        } else {
                            $('#customerResult').addClass('d-none');
                        }
                    }
                });
            });
            // Listeden bir müşteri seçildiğinde çalışır
            $(document).on('click', '#customerResult a', function() {
                $('#customerSearch').val($(this).data('name'));
                $('#selectedCustomer').val($(this).data('id'));
                $('#customerResult').addClass('d-none');
            });
            // Müşteri tipi Kurumsal mı Bireysel mi kontrol eden fonksiyon
            function toggleCorporate() {
                let type = $('#customer_type_id').val();

                // Kurumsal seçiliyse kurumsal alanları göster
                if (type == 2) {
                    $('.corporate-field').removeClass('d-none');
                } else {
                    // Bireysel ise kurumsal alanları gizle
                    $('.corporate-field').addClass('d-none');
                }
            }
            toggleCorporate();
            // Müşteri tipi değiştiğinde tekrar kontrol et
            $('#customer_type_id').on('change', toggleCorporate);
            // Ülkeye Göre Şehir Şehire Göre İlçelerin Listelenmesi
            $('#country_id').change(function() {
                let countryId = $(this).val();
                $('#city_id').prop('disabled', true).html('');
                $('#district_id').prop('disabled', true).html('');

                if (countryId) {
                    $.get('/location/cities/' + countryId, function(data) {
                        let options = '<option value="">Seciniz</option>';
                        data.forEach(function(city) {
                            options += `<option value="${city.id}">${city.baslik}</option>`;
                        });
                        $('#city_id').html(options).prop('disabled', false);
                    });
                }
            });
            $('#city_id').change(function() {
                let cityId = $(this).val();
                $('#district_id').prop('disabled', true).html('');

                if (cityId) {
                    $.get('/location/districts/' + cityId, function(data) {
                        let options = '<option value="">Seciniz</option>';
                        data.forEach(function(district) {
                            options +=
                                `<option value="${district.id}">${district.baslik}</option>`;
                        });
                        $('#district_id').html(options).prop('disabled', false);
                    });
                }
            });
            // Yeni Müşteri Ekle
            $('#customerInsertButton').click(function(e) {
                e.preventDefault();
                let data = {
                    _token: "{{ csrf_token() }}",
                    customer_type_id: $('#customer_type_id').val(),
                    fullname: $('#fullname').val(),
                    company_name: $('#company_name').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    phone_secondary: $('#phone_secondary').val(),
                    customer_preferred_contact_method_id: $('#customer_preferred_contact_method_id')
                        .val(),
                    address: $('#address').val(),
                    country_id: $('#country_id').val(),
                    city_id: $('#city_id').val(),
                    district_id: $('#district_id').val(),
                    postal_code: $('#postal_code').val(),
                    tax_office: $('#tax_office').val(),
                    tax_number: $('#tax_number').val(),
                    notes: $('#notes').val()
                };

                $.ajax({
                    type: "POST",
                    url: "/" + domain + "/customers/store",
                    data: data,
                    success: function(res) {
                        $('#customerSearch').val(res.name);
                        $('#selectedCustomer').val(res.id);
                        $('#createCustomerModal').modal('hide');
                        $('#customerCreateForm')[0].reset();
                    },
                    error: function(xhr) {
                        alert("Müşteri kaydedilirken hata oluştu!");
                    }
                });
            });
        });
    </script>
@endsection
