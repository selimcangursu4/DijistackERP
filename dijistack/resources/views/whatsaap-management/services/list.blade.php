<script>
    const CAN_UPDATE = {{ can('whatsapp-management/services', 'update') ? 'true' : 'false' }};
    const CAN_DELETE = {{ can('whatsapp-management/services', 'delete') ? 'true' : 'false' }};
</script>
@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center bg-primary">
                <h5 class="card-title text-white mb-0">AI Hafıza Kayıtları</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered w-100" id="servicesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>İçerik Başlığı</th>
                            <th>İçerik Özeti</th>
                            <th>Tarih</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">AI Hafıza Kaydını Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editServiceForm">
                    @csrf
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Başlık</label>
                            <input type="text" name="title" id="edit_title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">İçerik (Botun Bilgisi)</label>
                            <textarea name="content" id="edit_content" class="form-control" rows="10" required></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="fa fa-info-circle"></i> Not: İçeriği güncellediğinizde AI için gerekli olan <b>Vektör
                                (Embedding)</b> verisi otomatik olarak yeniden hesaplanacaktır.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</button>
                        <button type="submit" class="btn btn-success" id="updateBtn">Değişiklikleri Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            let table = $('#servicesTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('whatsapp.services.fetch', ['domain' => request()->route('domain')]) }}",
                    type: 'GET'
                },
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/tr.json'
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'content',
                        name: 'content'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // 2. Düzenle Butonuna Tıklama (Modalı Aç)
            $(document).on('click', '.edit-btn', function() {
                if (!CAN_UPDATE) {
                    Swal.fire('Yetkiniz Yok', 'Bu işlem için yetkiniz bulunmamaktadır.', 'warning');
                    return;
                }

                let id = $(this).data('id');
                let title = $(this).data('title');
                let content = $(this).data('content');

                $('#edit_id').val(id);
                $('#edit_title').val(title);
                $('#edit_content').val(content);

                $('#editModal').modal('show');
            });

            // 3. Güncelleme Formu Gönderimi
            $('#editServiceForm').on('submit', function(e) {
                e.preventDefault();
                $('#updateBtn').prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> İşleniyor...');

                $.ajax({
                    url: "{{ route('whatsapp.services.update', ['domain' => request()->route('domain')]) }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#editModal').modal('hide');
                        Swal.fire('Başarılı!', 'Bilgi ve Vektör verisi güncellendi.',
                            'success');
                        table.ajax.reload(null, false);
                    },
                    error: function(err) {
                        Swal.fire('Hata!', 'Güncelleme sırasında bir sorun oluştu.', 'error');
                    },
                    complete: function() {
                        $('#updateBtn').prop('disabled', false).text('Değişiklikleri Kaydet');
                    }
                });
            });

            // 4. Silme İşlemi
            $(document).on('click', '.delete-btn', function() {

                if (!CAN_DELETE) {
                    Swal.fire('Yetkiniz Yok', 'Bu işlem için yetkiniz bulunmamaktadır.', 'warning');
                    return;
                }

                let id = $(this).data('id');
                let domain = "{{ request()->route('domain') }}";

                Swal.fire({
                    title: 'Emin misiniz?',
                    text: "Bu kayıt silindiğinde AI artık bu bilgiyi kullanamayacak!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Evet, Sil!',
                    cancelButtonText: 'İptal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/" + domain + "/whatsapp-management/services/delete/" +
                                id,
                            type: 'POST',
                            data: {
                                _token: "{{ csrf_token() }}",
                                _method: "DELETE"
                            },
                            success: function() {
                                $('#servicesTable').DataTable().ajax.reload(null,
                                false);
                                Swal.fire('Silindi!', 'Kayıt başarıyla kaldırıldı.',
                                    'success');
                            },
                            error: function(xhr) {
                                if (xhr.status === 403) {
                                    Swal.fire('Yetkiniz Yok',
                                        'Bu işlem için yetkiniz bulunmamaktadır.',
                                        'warning');
                                } else {
                                    Swal.fire('Hata!', 'Silme sırasında sorun oluştu.',
                                        'error');
                                }
                            }
                        });
                    }
                });
            });

        });
    </script>
@endsection
