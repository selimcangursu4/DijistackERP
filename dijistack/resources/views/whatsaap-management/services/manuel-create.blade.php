<script>
    const CAN_CREATE = {{ can('whatsapp_services', 'create') ? 'true' : 'false' }};
</script>
@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <h5 class="card-title mb-0 text-white">WhatsApp AI Ürün veya Hizmet Manuel Ekleme</h5>
            </div>
            <div class="card-body">
                <form id="manuelServiceForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Ürün/Hizmet Adı veya Başlık</label>
                        <input type="text" name="title" id="title" class="form-control"
                            placeholder="Örn: İade Politikası veya Lazer Epilasyon Hizmeti" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Botun Bilmesi Gereken Bilgiler (İçerik)</label>
                        <textarea name="content" id="content" class="form-control" rows="8"
                            placeholder="Botun bu hizmetle ilgili müşteriye ne cevap vermesini istiyorsanız buraya detaylıca yazın. Fiyat, süre, randevu şartları vb."
                            required></textarea>
                        <div class="form-text text-info">Not: Ne kadar detaylı yazarsanız bot o kadar doğru cevap verir.
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="saveBtn" class="btn btn-primary">
                            <span class="spinner-border spinner-border-sm d-none" id="btnLoader"></span>
                            Sisteme Öğret ve Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('#manuelServiceForm').on('submit', function(e) {
            e.preventDefault();

            if (!CAN_CREATE) {
                Swal.fire({
                    icon: 'error',
                    title: 'Yetkisiz İşlem',
                    text: 'Bu işlem için yetkiniz bulunmamaktadır.',
                });
                return;
            }

            const btn = $('#saveBtn');
            const loader = $('#btnLoader');

            btn.prop('disabled', true);
            loader.removeClass('d-none');

            $.ajax({
                url: "{{ route('whatsapp.services.store', ['domain' => request()->route('domain')]) }}",
                method: "POST",
                data: $(this).serialize(),
                success: function(res) {
                    Swal.fire('Başarılı!', 'Bilgi başarıyla AI hafızasına eklendi.', 'success');
                    $('#manuelServiceForm')[0].reset();
                },
                error: function(xhr) {

                    if (xhr.status === 403) {
                        Swal.fire('Yetkisiz!', 'Bu işlem için yetkiniz bulunmamaktadır.', 'error');
                    } else {
                        Swal.fire('Hata!', 'Vektör oluşturulurken veya kaydedilirken hata oluştu.',
                            'error');
                    }

                },
                complete: function() {
                    btn.prop('disabled', false);
                    loader.addClass('d-none');
                }
            });
        });
    </script>
@endsection
