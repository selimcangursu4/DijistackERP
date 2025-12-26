@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title text-white mb-0">Servis Kayıt Filtreleme</h5>
                <small>Servis kayıt filtreleme alanıdır.</small>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="service_id" class="form-label"><b>Servis Numarası:</b></label>
                            <input type="text" class="form-control" id="service_id" name="service_id">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="product_id" class="form-label"><b>Ürün Bilgisi:</b></label>
                            <select class="form-select" id="product_id" name="product_id">
                                <option value="">Ürün Seçiniz..</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="imei" class="form-label"><b>İmei Numarası:</b></label>
                            <input type="text" class="form-control" id="imei" name="imei">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="fault_category_id" class="form-label"><b>Sorun Türü:</b></label>
                            <select class="form-select" id="fault_category_id" name="fault_category_id">
                                <option value="">Sorun Türü Seçiniz..</option>
                                @foreach ($faultCategories as $fault)
                                    <option value="{{ $fault->id }}">{{ $fault->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="priority_status_id" class="form-label"><b>Öncelik Durumu:</b></label>
                            <select class="form-select" id="priority_status_id" name="priority_status_id">
                                <option value="">Öncelik Türü Seçiniz..</option>
                                @foreach ($priorityStatus as $priority)
                                    <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="service_status_id" class="form-label"><b>Servis Durumu:</b></label>
                            <select class="form-select" id="service_status_id" name="service_status_id">
                                <option value="">Servis Durumu Seçiniz..</option>
                                @foreach ($serviceStatus as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="created_at" class="form-label"><b>Oluşturma Tarihi:</b></label>
                            <input type="text" class="form-control" id="created_at" name="created_at">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="btn btn-primary float-end">Servis Kayıtlarını Filtrele</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title text-white mb-0">Tüm Servis Kayıtları</h5>
                <small>Tüm servis kayıtları burada listelenir.</small>
            </div>
            <div class="card-body">
                <div class="app-datatable-default overflow-auto">
                    <table class="w-100 display app-data-table default-data-table" id="example">
                        <thead>
                            <tr>
                                <th>Servis Numarası</th>
                                <th>Müşteri</th>
                                <th>Ürün</th>
                                <th>Seri No</th>
                                <th>Sorun Türü</th>
                                <th>Öncelik</th>
                                <th>Durum</th>
                                <th>Tahmini Bitiş</th>
                                <th>Oluşturma Tarihi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>
                                    <button class="btn btn-light-success icon-btn b-r-4" type="button">
                                        <i class="ti ti-edit text-success"></i>
                                    </button>
                                    <button class="btn btn-light-danger icon-btn b-r-4 delete-btn" type="button">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {

            let table = $('#example').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 10,
                responsive: true,

                ajax: {
                    url: "{{ route('technical-service.fetch', request()->route('domain')) }}",
                    data: function(d) {
                        d.service_id = $('#service_id').val();
                        d.product_id = $('#product_id').val();
                        d.imei = $('#imei').val();
                        d.fault_category_id = $('#fault_category_id').val();
                        d.priority_status_id = $('#priority_status_id').val();
                        d.service_status_id = $('#service_status_id').val();
                        d.created_at = $('#created_at').val();
                    }
                },

                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            if (row.customer.fullname && row.customer.fullname.trim() !== '') {
                                return row.customer.fullname;
                            }
                            return row.customer.company_name ?? '-';
                        }
                    },

                    {
                        data: 'product.name',
                        name: 'product.name'
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number'
                    },
                    {
                        data: 'fault_category.name'
                    },

                    {
                        data: 'priority.name',
                        name: 'priority.name'
                    },
                    {
                        data: 'status.name',
                        name: 'status.name'
                    },
                    {
                        data: 'estimated_completion_date',
                        render: function(data) {
                            if (!data) return '-';
                            let d = new Date(data);
                            return d.toLocaleString('tr-TR', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                            });
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data) {
                            if (!data) return '-';
                            let d = new Date(data);
                            return d.toLocaleString('tr-TR', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                            });
                        }
                    },
                    {
                        data: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],

                language: {
                    search: "Ara:",
                    lengthMenu: "Sayfa başına _MENU_ kayıt",
                    info: "_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor",
                    paginate: {
                        previous: "Önceki",
                        next: "Sonraki"
                    },
                    zeroRecords: "Kayıt bulunamadı",
                    processing: "Yükleniyor..."
                }
            });

            // Filtre Butonu
            $('.btn-primary').on('click', function() {
                table.draw();
            });

        });
    </script>
@endsection
