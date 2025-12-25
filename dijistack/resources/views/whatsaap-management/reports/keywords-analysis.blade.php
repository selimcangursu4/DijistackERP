@extends('partials.master')
@section('main')
    <div class="container-fluid">
        <h3 class="fw-bold mb-4">
            <i class="fas fa-key text-primary me-2"></i> Anahtar Kelime İstihbarat Merkezi
        </h3>
        <div class="row g-3 mb-4">
            @foreach ([['Toplam Anahtar Kelime', $topKeywords->sum('count'), 'bg-primary'], ['En Olumlu Kelime', $mostPositiveKeyword['word'] . ' (' . number_format($mostPositiveKeyword['score'], 1) . ')', 'bg-success'], ['En Riskli Kelime', $problematicKeywords->keys()->first(), 'bg-danger'], ['En Popüler Kelime', $topKeywords->keys()->first(), 'bg-info']] as $k)
                <div class="col-md-3">
                    <div class="card p-3 text-white text-center {{ $k[2] }}">
                        <small>{{ $k[0] }}</small>
                        <h4>{{ $k[1] }}</h4>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card mb-4 shadow-sm">
            <div class="card-header fw-bold">📊 En Çok Konuşulan Konular</div>
            <div class="table-responsive p-3">
                <table id="keywordsTable" class="table table-sm table-hover text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kelime</th>
                            <th>Hacim</th>
                            <th>Pozitif %</th>
                            <th>Negatif %</th>
                            <th>Müşteri Memnuniyet Skoru</th>
                            <th>Müşteri Algısı</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($topKeywords as $word => $k)
                            <tr>
                                <td class="fw-bold">{{ $word }}</td>
                                <td>{{ $k['count'] }}</td>
                                <td class="text-success">{{ $k['positive_pct'] }}%</td>
                                <td class="text-danger">{{ $k['negative_pct'] }}%</td>
                                <td>{{ $k['avg_csat'] ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $k['emotion_score'] < 0 ? 'bg-danger' : 'bg-success' }}">
                                        {{ $k['emotion_score'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header fw-bold text-success">En Olumlu Konular</div>
                    <div class="card-body">
                        @foreach ($mostPositiveKeywords as $word => $k)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $word }}</span>
                                <span class="badge bg-success">{{ $k['positive_pct'] }}%</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow-sm border-danger border-top border-4">
                    <div class="card-header fw-bold text-danger">Riskli Konular</div>
                    <div class="card-body">
                        @foreach ($problematicKeywords as $word => $k)
                            <div class="d-flex justify-content-between border-bottom py-2">
                                <span>{{ $word }}</span>
                                <span class="badge bg-danger">{{ $k['negative_pct'] }}% Negatif</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#keywordsTable').DataTable({
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                order: [
                    [1, 'desc']
                ],
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/tr.json"
                }
            });
        });
    </script>
@endsection
