@extends('partials.master') @section('main')
    <div class="container-fluid">
        <h3 class="fw-bold mb-3">
            <i class="fas fa-brain text-danger me-2"></i> Duygu Analizi Merkezi
        </h3>
        @if ($alert)
            <div class="alert alert-danger fw-bold mb-4"> ⚠️ Müşteri memnuniyet riski tespit edildi! Bot & duygu metrikleri
                kritik seviyede. </div>
            @endif <div class="row g-3 mb-4"> @php $kpis = [ ['Ortalama Memnuniyet (CSAT)', number_format($systemHealth->avg_csat,2), 'bg-success','text-white'], ['Genel Duygu Skoru', number_format($systemHealth->emotion_score,2), 'bg-info','text-white'], ['Yanıtsız Kalma Oranı', number_format($systemHealth->fallback_rate,1).'%', 'bg-warning text-white'], ['Canlı Temsilciye Aktarım', number_format($systemHealth->handover_rate,1).'%', 'bg-info','text-white'], ['Toplam Mesaj Sayısı', $systemHealth->total, 'bg-secondary','text-white'], ['Ortalama Token Kullanımı', number_format($systemHealth->avg_token,0), 'bg-primary','text-white'] ]; @endphp @foreach ($kpis as $k)
                    <div class="col-md-2">
                        <div class="card p-3 text-center text-white {{ $k[2] }}">
                            <small>{{ $k[0] }}</small>
                            <h4 class="mb-0">{{ $k[1] }}</h4>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row g-3 mb-2">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white fw-bold">📊 Günlük Duygu Dağılımı</div>
                        <div class="card-body">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white fw-bold">📅 Aylık Duygu Dağılımı</div>
                        <div class="card-body">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white fw-bold">🗓️ Yıllık Duygu Dağılımı</div>
                        <div class="card-body">
                            <canvas id="yearlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white fw-bold">Konu Başlık Analizi</div>
                        <div class="card-body">
                            @foreach ($intentStats as $i)
                                <div class="d-flex justify-content-between border-bottom py-1">
                                    <span>{{ $i->intent }}</span>
                                    <span class="fw-bold">{{ $i->total }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script>
        function buildDataset(data) {
            return {
                labels: data.map(i => i.period),
                datasets: [{
                    label: 'Pozitif',
                    data: data.map(i => i.positive),
                    borderWidth: 2
                }, {
                    label: 'Negatif',
                    data: data.map(i => i.negative),
                    borderWidth: 2
                }, {
                    label: 'Nötr',
                    data: data.map(i => i.neutral),
                    borderWidth: 2
                }]
            };
        }
        new Chart(dailyChart, {
            type: 'bar',
            data: buildDataset(@json($daily)),
            options: {
                responsive: true
            }
        });
        new Chart(monthlyChart, {
            type: 'bar',
            data: buildDataset(@json($monthly)),
            options: {
                responsive: true
            }
        });
        new Chart(yearlyChart, {
            type: 'bar',
            data: buildDataset(@json($yearly)),
            options: {
                responsive: true
            }
        });
    </script>
@endsection
