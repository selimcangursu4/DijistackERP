@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center bg-primary">
                        <h5 class="card-title text-white mb-0">WhatsApp AI Bağlantı Ekranı</h5>
                        <span id="status-badge" class="badge bg-secondary">Başlatılıyor...</span>
                    </div>
                    <div class="card-body text-center p-5">
                        <div id="qr-container" class="mb-4"
                            style="min-height: 300px; display: flex; align-items: center; justify-content: center;">
                            <div id="loader" class="spinner-border text-primary" style="width: 3rem; height: 3rem;"
                                role="status">
                                <span class="visually-hidden">Yükleniyor...</span>
                            </div>
                            <img id="qrcode" src=""
                                style="display:none; border: 1px solid #ddd; padding: 10px; border-radius: 8px; width: 300px; height: 300px;">
                        </div>

                        <div id="instruction-area">
                            <p id="instruction" class="text-muted">
                                Sistem hazırlanıyor, lütfen bekleyin...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
    <script>
        $(document).ready(function() {
            const companyId = "{{ $company }}";
            const nodeServer = "http://localhost:3000"; // Node.js server URL
            const socket = io(nodeServer);

            // 1. Socket Odasına Katıl
            socket.emit('join_company', companyId);

            // 2. QR Kod Eventi
            socket.on('qr_code', function(qrImage) {
                $('#loader').hide();
                $('#qrcode').attr('src', qrImage).show();
                $('#status-badge').text('QR Kod Bekleniyor').removeClass('bg-secondary bg-success')
                    .addClass('bg-warning text-dark');
                $('#instruction').text(
                    'Lütfen telefonunuzdan WhatsApp > Bağlı Cihazlar > Cihaz Bağla diyerek QR kodu taratın.'
                );
            });

            // 3. Durum Değişikliği (READY vb.)
            socket.on('status', function(data) {
                if (data.status === 'READY') {
                    $('#loader').hide();
                    $('#qrcode').hide();
                    $('#status-badge').text('AKTİF').removeClass('bg-secondary bg-warning').addClass(
                        'bg-success');
                    $('#instruction').html(
                        '<div class="alert alert-success"><b>Bağlantı Başarılı!</b><br>Botunuz şu an aktif ve mesajları yanıtlamaya hazır.</div>'
                    );
                }
            });

            // 4. Bağlantı Koptuğunda
            socket.on('disconnect', () => {
                console.log("Sunucu bağlantısı kesildi.");
            });

            // 5. Node.js Session Başlatma İsteği
            function initWhatsApp() {
                $.ajax({
                    url: nodeServer + '/init-session',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        company_id: companyId,
                        webhook_url: "{{ url('/whatsapp/webhook') }}"
                    }),
                    success: function(response) {
                        console.log("Node.js yanıtı:", response);

                        // --- BU BLOGU EKLE ---
                        if (response.status === 'already_active') {
                            $('#loader').hide();
                            $('#qrcode').hide();
                            $('#status-badge').text('AKTİF').removeClass('bg-secondary bg-warning')
                                .addClass('bg-success');
                            $('#instruction').html(
                                '<div class="alert alert-success"><b>Bağlantı Başarılı!</b><br>Botunuz şu an aktif ve mesajları yanıtlamaya hazır.</div>'
                                );
                        }
                        // ----------------------
                    },
                    error: function(err) {
                        $('#loader').hide();
                        $('#instruction').html(
                            '<span class="text-danger">Node.js sunucusuna bağlanılamadı.</span>');
                    }
                });
            }

            // Başlat
            initWhatsApp();
        });
    </script>
@endsection
