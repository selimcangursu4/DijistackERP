@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">
                <i class="fas fa-brain text-danger me-2"></i>
                Kişi Bazlı AI Analiz Raporları
            </h3>
            <span class="badge bg-soft-dark text-dark border p-2">
                <i class="far fa-calendar-alt me-1"></i> {{ now()->format('d.m.Y') }}
            </span>
        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-primary text-white p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-uppercase fw-bold opacity-75">Toplam Müşteri</small>
                            <h2 class="fw-bold mb-0 text-white">{{ $generalStats->total_people }}</h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-success text-white p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-uppercase fw-bold opacity-75">7 Günlük Yeni</small>
                            <h2 class="fw-bold mb-0 text-white">{{ $generalStats->new_people_7d }}</h2>
                        </div>
                        <i class="fas fa-user-plus fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-dark text-white p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-uppercase fw-bold opacity-75">Ort. Yanıt Hızı</small>
                            <h2 class="fw-bold mb-0 text-white">{{ number_format($responseTime->avg_speed ?? 0, 1) }}s</h2>
                        </div>
                        <i class="fas fa-bolt fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-danger text-white p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-uppercase fw-bold opacity-75">Kayıp (Churn) Riski</small>
                            <h2 class="fw-bold mb-0 text-white">{{ count($churnRisk) }}</h2>
                        </div>
                        <i class="fas fa-user-slash fa-2x opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary py-3">
                        <h5 class="fw-bold mb-0 text-white">
                            <i class="fas fa-smile-beam  text-warning me-2"></i> Zaman Bazlı Mutluluk Analizi (CSAT)
                        </h5> 
                    </div>
                    <div class="card-body mt-3">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">Bugünkü Mutluluk Oranı</small>
                                    <div class="d-flex align-items-center">
                                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($dailyCsat ?? 0, 1) }}</h3>
                                        <span class="ms-2 small text-muted">/ 5.0</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: {{ ($dailyCsat ?? 0) * 20 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">Bu Ayın Ortalaması</small>
                                    <div class="d-flex align-items-center">
                                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($monthlyCsat ?? 0, 1) }}</h3>
                                        <span class="ms-2 small text-muted">/ 5.0</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: {{ ($monthlyCsat ?? 0) * 20 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-1">Yıllık Genel Skor</small>
                                    <div class="d-flex align-items-center">
                                        <h3 class="fw-bold mb-0 text-dark">{{ number_format($yearlyCsat ?? 0, 1) }}</h3>
                                        <span class="ms-2 small text-muted">/ 5.0</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 5px;">
                                        <div class="progress-bar bg-success" style="width: {{ ($yearlyCsat ?? 0) * 20 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="csatTable" class="table table-hover align-middle border-top mb-0">
                                <thead class="table-light">
                                    <tr class="small text-uppercase">
                                        <th>Zaman Dilimi</th>
                                        <th>Toplam Mesaj</th>
                                        <th>Pozitif / Negatif</th>
                                        <th>Mutluluk Skoru</th>
                                        <th class="text-end pe-4">Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="fw-bold">Bugün</span></td>
                                        <td>{{ $dailyStats->total ?? 0 }} Mesaj</td>
                                        <td>
                                            <span class="text-success small"><i class="fas fa-arrow-up"></i>
                                                {{ $dailyStats->positive ?? 0 }}</span>
                                            <span class="text-danger small ms-2"><i class="fas fa-arrow-down"></i>
                                                {{ $dailyStats->negative ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fw-bold text-dark me-2">{{ number_format($dailyCsat ?? 0, 1) }}</span>
                                                <div class="progress shadow-sm" style="height: 6px; width: 60px;">
                                                    <div class="progress-bar bg-primary"
                                                        style="width: {{ ($dailyCsat ?? 0) * 20 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            {!! $dailyCsat >= 4
                                                ? '<span class="badge rounded-pill bg-success">Mükemmel</span>'
                                                : ($dailyCsat >= 3
                                                    ? '<span class="badge rounded-pill bg-warning text-dark">Orta</span>'
                                                    : '<span class="badge rounded-pill bg-danger">Kritik</span>') !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="fw-bold">Bu Ay</span></td>
                                        <td>{{ $monthlyStats->total ?? 0 }} Mesaj</td>
                                        <td>
                                            <span class="text-success small"><i class="fas fa-arrow-up"></i>
                                                {{ $monthlyStats->positive ?? 0 }}</span>
                                            <span class="text-danger small ms-2"><i class="fas fa-arrow-down"></i>
                                                {{ $monthlyStats->negative ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fw-bold text-dark me-2">{{ number_format($monthlyCsat ?? 0, 1) }}</span>
                                                <div class="progress shadow-sm" style="height: 6px; width: 60px;">
                                                    <div class="progress-bar bg-success"
                                                        style="width: {{ ($monthlyCsat ?? 0) * 20 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            {!! $monthlyCsat >= 4
                                                ? '<span class="badge rounded-pill bg-success">Mükemmel</span>'
                                                : ($monthlyCsat >= 3
                                                    ? '<span class="badge rounded-pill bg-warning text-dark">Orta</span>'
                                                    : '<span class="badge rounded-pill bg-danger">Kritik</span>') !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="fw-bold">Bu Yıl</span></td>
                                        <td>{{ $yearlyStats->total ?? 0 }} Mesaj</td>
                                        <td>
                                            <span class="text-success small"><i class="fas fa-arrow-up"></i>
                                                {{ $yearlyStats->positive ?? 0 }}</span>
                                            <span class="text-danger small ms-2"><i class="fas fa-arrow-down"></i>
                                                {{ $yearlyStats->negative ?? 0 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span
                                                    class="fw-bold text-dark me-2">{{ number_format($yearlyCsat ?? 0, 1) }}</span>
                                                <div class="progress shadow-sm" style="height: 6px; width: 60px;">
                                                    <div class="progress-bar bg-info"
                                                        style="width: {{ ($yearlyCsat ?? 0) * 20 }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            {!! $yearlyCsat >= 4
                                                ? '<span class="badge rounded-pill bg-success">Mükemmel</span>'
                                                : ($yearlyCsat >= 3
                                                    ? '<span class="badge rounded-pill bg-warning text-dark">Orta</span>'
                                                    : '<span class="badge rounded-pill bg-danger">Kritik</span>') !!}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white fw-bold border-0 pt-3">Bağlılık Segmentasyonu</div>
                    <div class="card-body">
                        <canvas id="segmentChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white fw-bold border-0 pt-3">Günlük Mesajlaşma Alışkanlıkları (Saatlik)
                    </div>
                    <div class="card-body">
                        <canvas id="hourlyChart" style="height: 250px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary fw-bold border-0 pt-3">
                        <i class="fas fa-chart-line text-white me-2"></i> Günlük Konuşma Hacmi (Son 15 Gün)
                    </div>
                    <div class="card-body">
                        <canvas id="dailyVolumeChart" style="height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 border-top border-primary border-4 h-100">
                    <div class="card-header bg-primary text-white fw-bold border-0 pt-3">Operasyonel İçgörü (Dinamik)</div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold mb-2">Haftalık Performans</label>
                            <div class="d-flex align-items-center">
                                <h3 class="mb-0 me-3">{{ $operationalInsights['this_week_count'] }} <small
                                        class="fs-6 fw-normal">Mesaj</small></h3>
                                <span
                                    class="badge {{ $operationalInsights['growth_rate'] >= 0 ? 'bg-soft-success text-success' : 'bg-soft-danger text-danger' }} p-2">
                                    <i
                                        class="fas fa-arrow-{{ $operationalInsights['growth_rate'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                    %{{ abs($operationalInsights['growth_rate']) }}
                                </span>
                            </div>
                            <p class="text-muted small mt-1">Geçen haftaya göre değişim oranı.</p>
                        </div>

                        <div class="pt-3 border-top">
                            <label class="small text-muted text-uppercase fw-bold mb-2">Zirve Trafik Saati</label>
                            <div class="d-flex align-items-center">
                                <div class="bg-soft-warning p-2 rounded me-3">
                                    <i class="fas fa-clock text-warning"></i>
                                </div>
                                <h4 class="mb-0">{{ $operationalInsights['peak_hour'] }}</h4>
                            </div>
                            <p class="text-muted small mt-1">Sistemin en yoğun çalıştığı zaman aralığı.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary fw-bold d-flex justify-content-between align-items-center">
                        <span class="text-white">Müşteri Değer Matrisi (Top 15)</span>
                        <small class="text-white fw-normal">En aktif etkileşim kuranlar</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table id="loyaltyTable" class="table table-hover align-middle mb-0">
                                <thead class="table-light text-muted small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Müşteri / Telefon</th>
                                        <th>Mesaj Gücü</th>
                                        <th>Token Sarfiyatı</th>
                                        <th>Mutluluk Oranı (CSAT)</th>
                                        <th>Son Görülme</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($loyaltyMatrix as $l)
                                        <tr>
                                            <td class="ps-4">
                                                <strong>{{ $l->name ?? 'İsimsiz Müşteri' }}</strong><br>
                                                <small class="text-muted">{{ $l->phone }}</small>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-soft-primary text-primary border border-primary-subtle">
                                                    {{ $l->total_messages }} Mesaj
                                                </span>
                                            </td>
                                            <td><code>{{ number_format($l->total_tokens) }}</code></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span
                                                        class="me-2 fw-bold text-success small">{{ number_format($l->avg_happiness, 1) }}</span>
                                                    <div class="progress flex-grow-1"
                                                        style="height: 6px; max-width: 100px;">
                                                        <div class="progress-bar bg-success"
                                                            style="width: {{ $l->avg_happiness * 20 }}%"></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td data-order="{{ $l->last_seen }}">
                                                <small>{{ \Carbon\Carbon::parse($l->last_seen)->diffForHumans() }}</small>
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


    </div>
    <script>
        $(document).ready(function() {
            $('#loyaltyTable').DataTable({
                "pageLength": 10,
                "order": [
                    [4, "desc"]
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json"
                },
                "columnDefs": [{
                    "orderable": false,
                    "targets": [0]
                }],
                "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>'
            });

            $('#csatTable').DataTable({
                "paging": false, // 3 satır olduğu için sayfalama kapalı
                "searching": false, // Arama kapalı
                "info": false, // "Şu kadar kayıt gösteriliyor" yazısı kapalı
                "ordering": true, // Sütun başlıklarına basınca sıralasın
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/tr.json"
                },
                "columnDefs": [{
                        "targets": [4],
                        "orderable": false
                    } // Durum sütununda sıralamayı kapat
                ]
            });
        });
        // 1. Bağlılık Segmentasyonu (Doughnut)
        new Chart(document.getElementById('segmentChart'), {
            type: 'doughnut',
            data: {
                labels: ['Yeni', 'Düzenli', 'Sadık'],
                datasets: [{
                    data: [{{ $segmentation['new'] }}, {{ $segmentation['regular'] }},
                        {{ $segmentation['loyal'] }}
                    ],
                    backgroundColor: ['#0d6efd', '#ffc107', '#198754'],
                    borderWidth: 0
                }]
            },
            options: {
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // 2. Saatlik Trafik (Line)
        const hourlyData = {!! json_encode($hourlyDistribution) !!};
        new Chart(document.getElementById('hourlyChart'), {
            type: 'line',
            data: {
                labels: Array.from({
                    length: 24
                }, (_, i) => i + ':00'),
                datasets: [{
                    label: 'Gelen Mesaj',
                    data: Array.from({
                        length: 24
                    }, (_, i) => (hourlyData.find(h => h.hour == i)?.count || 0)),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 3. Günlük Konuşma Hacmi (Bar)
        const dailyData = {!! json_encode($dailyVolume) !!};
        new Chart(document.getElementById('dailyVolumeChart'), {
            type: 'bar',
            data: {
                labels: dailyData.map(d => {
                    const date = new Date(d.date);
                    return date.toLocaleDateString('tr-TR', {
                        day: '2-digit',
                        month: '2-digit'
                    });
                }),
                datasets: [{
                    label: 'Toplam Mesaj',
                    data: dailyData.map(d => d.count),
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>

    <style>
        .bg-soft-primary {
            background-color: rgba(13, 110, 253, 0.1);
        }

        .bg-soft-success {
            background-color: rgba(25, 135, 84, 0.1);
        }

        .bg-soft-danger {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .bg-soft-warning {
            background-color: rgba(255, 193, 7, 0.1);
        }

        .bg-soft-dark {
            background-color: rgba(33, 37, 41, 0.05);
        }

        .progress-bar {
            transition: width 1s ease-in-out;
        }
    </style>
@endsection
