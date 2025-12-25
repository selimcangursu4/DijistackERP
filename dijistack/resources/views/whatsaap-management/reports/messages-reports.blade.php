@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center">
                <h3 class="fw-bold"><i class="fas fa-chart-bar text-primary me-2"></i>Mesaj Trafik ve Hacim Analizi</h3>
                <span class="badge bg-soft-info text-info p-2">Veri Kapsamı: Tüm Zamanlar</span>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 border-top border-primary border-4 text-center p-3">
                    <small class="text-muted fw-bold">TOPLAM MESAJ TRAFİĞİ</small>
                    <h2 class="fw-bold">{{ number_format($stats->total_messages) }}</h2>
                    <div class="small">
                        <span class="text-success"><i class="fas fa-arrow-down"></i>
                            {{ number_format($stats->incoming_count) }} Gelen</span>
                        <span class="text-primary ms-2"><i class="fas fa-arrow-up"></i>
                            {{ number_format($stats->outgoing_count) }} Giden</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 border-top border-success border-4 text-center p-3">
                    <small class="text-muted fw-bold">SON 30 GÜN HACMİ</small>
                    <h2 class="fw-bold">{{ number_format($stats->last_30_days_count) }}</h2>
                    <p class="mb-0 text-muted small">Aktif operasyonel yük</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 border-top border-info border-4 text-center p-3">
                    <small class="text-muted fw-bold">ORT. MESAJ UZUNLUĞU</small>
                    <h2 class="fw-bold">{{ round($stats->avg_char_count) }} <span class="small text-muted">Krk.</span></h2>
                    <p class="mb-0 text-muted small">İleti başına veri boyutu</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0 border-top border-warning border-4 text-center p-3">
                    <small class="text-muted fw-bold">GELEN/GİDEN ORANI</small>
                    <h2 class="fw-bold">1 : {{ number_format($stats->outgoing_count / max($stats->incoming_count, 1), 1) }}
                    </h2>
                    <p class="mb-0 text-muted small">Yanıt verme katsayısı</p>
                </div>
            </div>
        </div>

        {{-- Grafikler --}}
        <div class="row mb-4">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white fw-bold">Günlük Mesaj Trendi (Son 30 Gün)</div>
                    <div class="card-body">
                        <canvas id="dailyVolumeChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Duygu Analizi </div>
                    <div class="card-body">
                        <canvas id="sentimentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Niyet Analizi</div>
                    <div class="card-body">
                        <canvas id="intentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Aylık Toplam Mesaj Büyümesi</div>
                    <div class="card-body">
                        <canvas id="monthlyBarChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="fas fa-user-headset text-warning me-2"></i>
                            İnsan Müdahale Gerektiren Mesajlar
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="handoverTable" class="table table-striped table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Müşteri</th>
                                            <th>Telefon</th>
                                            <th>Mesaj</th>
                                            <th>Intent</th>
                                            <th>Duygu</th>
                                            <th>CSAT</th>
                                            <th>Tarih</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($handoverMessages as $msg)
                                            <tr>
                                                <td>{{ $msg->id }}</td>
                                                <td>
                                                    {{ $msg->customer_name ?? 'Bilinmeyen' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('whatsapp.chat', ['session' => $msg->session_id]) }}"
                                                        class="fw-bold text-success">
                                                        {{ $msg->customer_phone }}
                                                    </a>
                                                </td>
                                                <td style="max-width:300px;">
                                                    <span title="{{ $msg->message }}">
                                                        {{ \Illuminate\Support\Str::limit($msg->message, 80) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $msg->intent ?? '-' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($msg->sentiment === 'positive')
                                                        <span class="badge bg-success">Pozitif</span>
                                                    @elseif($msg->sentiment === 'negative')
                                                        <span class="badge bg-danger">Negatif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Nötr</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $msg->predicted_csat ?? '-' }}
                                                </td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($msg->created_at)->format('d.m.Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Yanıtsız Mesajlar (Fallback)</div>
                    <div class="card-body">
                        <p class="mb-0 small text-muted">Toplam: {{ $stats->fallback_count }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Müşteri Memnuniyeti (CSAT)</div>
                    <div class="card-body">
                        <p class="mb-0 small text-muted">Ortalama Puan: {{ round($csat->avg_csat, 1) ?? 0 }}</p>
                        <p class="mb-0 small text-success">Memnun: {{ $csat->satisfied ?? 0 }}</p>
                        <p class="mb-0 small text-danger">Memnun Değil: {{ $csat->unsatisfied ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">Token Kullanımı</div>
                    <div class="card-body">
                        <p class="mb-0 small text-muted">Toplam Token: {{ $tokenUsage->total_tokens ?? 0 }}</p>
                        <p class="mb-0 small text-primary">Ortalama / Mesaj:
                            {{ round($tokenUsage->avg_tokens_per_msg, 1) ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Günlük Mesaj (Line Chart)
        new Chart(document.getElementById('dailyVolumeChart'), {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyVolume->pluck('date')) !!},
                datasets: [{
                        label: 'Gelen',
                        data: {!! json_encode($dailyVolume->pluck('incoming')) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Giden',
                        data: {!! json_encode($dailyVolume->pluck('outgoing')) !!},
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });



        // Aylık Mesaj (Bar)
        new Chart(document.getElementById('monthlyBarChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyVolume->pluck('month')) !!},
                datasets: [{
                        label: 'Gelen',
                        data: {!! json_encode($monthlyVolume->pluck('incoming')) !!},
                        backgroundColor: '#0d6efd'
                    },
                    {
                        label: 'Giden',
                        data: {!! json_encode($monthlyVolume->pluck('outgoing')) !!},
                        backgroundColor: '#198754'
                    }
                ]
            }
        });

        // Sentiment (Pie)
        new Chart(document.getElementById('sentimentChart'), {
            type: 'pie',
            data: {
                labels: ['Pozitif', 'Nötr', 'Negatif'],
                datasets: [{
                    data: [
                        {{ $sentimentCounts->positive }},
                        {{ $sentimentCounts->neutral }},
                        {{ $sentimentCounts->negative }}
                    ],
                    backgroundColor: ['#198754', '#0dcaf0', '#dc3545']
                }]
            }
        });

        // Intent (Bar)
        new Chart(document.getElementById('intentChart'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($intentCounts->pluck('intent')) !!},
                datasets: [{
                    label: 'Mesaj Sayısı',
                    data: {!! json_encode($intentCounts->pluck('count')) !!},
                    backgroundColor: '#6610f2'
                }]
            }
        });
    </script>
@endsection
