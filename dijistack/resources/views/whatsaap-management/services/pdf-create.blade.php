<script>
    const CAN_CREATE = {{ can('whatsapp-management/services', 'create') ? 'true' : 'false' }};
</script>
@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary">
                <h5 class="card-title text-white mb-0">WhatsApp AI PDF ile Bilgi Yükleme</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Yüklediğiniz PDF içindeki metinler yapay zeka tarafından analiz edilecek ve botun hafızasına
                    eklenecektir. Büyük dosyaların işlenmesi birkaç dakika sürebilir.
                </div>

                <form id="pdfUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">PDF Dosyası Seçin</label>
                        <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                    </div>

                    <div id="progressArea" class="mb-3 d-none">
                        <label class="form-label d-flex justify-content-between">
                            <span>Dosya İşleniyor...</span>
                            <span id="percent">0%</span>
                        </label>
                        <div class="progress">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" id="uploadBtn" class="btn btn-primary">
                            PDF'i Oku ve Hafızaya Al
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('#pdfUploadForm').on('submit', function(e) {
            e.preventDefault();

            if (!CAN_CREATE) {
                Swal.fire({
                    icon: 'error',
                    title: 'Yetkisiz İşlem',
                    text: 'Bu işlem için yetkiniz bulunmamaktadır.',
                });
                return;
            }

            let formData = new FormData(this);

            $('#uploadBtn').prop('disabled', true);
            $('#progressArea').removeClass('d-none');
            $('#progressBar').css('width', '20%');
            $('#percent').text('20%');

            $.ajax({
                url: "{{ route('whatsapp.services.storePDF', ['domain' => request()->route('domain')]) }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,

                success: function(res) {
                    $('#progressBar').css('width', '100%').addClass('bg-success');
                    $('#percent').text('100%');
                    Swal.fire('Başarılı!', res.message, 'success');
                },

                error: function(xhr) {
                    if (xhr.status === 403) {
                        Swal.fire('Yetkisiz!', 'Bu işlem için yetkiniz bulunmamaktadır.', 'error');
                    } else {
                        Swal.fire('Hata!', 'PDF işlenirken bir sorun oluştu. Logları kontrol edin.',
                            'error');
                    }
                },

                complete: function() {
                    $('#uploadBtn').prop('disabled', false);
                }
            });
        });
    </script>
@endsection
