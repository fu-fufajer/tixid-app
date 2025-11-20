@extends('templates.app')

@section('content')
    @if (Session::get('success'))
        {{-- Auth::user() : mengambil data pengguna yang login --}}
        {{-- format : Auth:user()->column_di_fillable --}}
        <div class="alert alert-success w-100">{{ Session::get('success') }} <b>Selamat datang, {{ Auth::user()->name }}</b>
        </div>
    @endif
    <div class="container mt-5">
        <h4>Grafik Pembelian Tiket</h4>
        <div class="row mt-5">
            <div class="col-6">
                <h5>Pembelian Tiket Bulan {{ now()->format('F') }}</h5>
                <canvas id="chartBar"></canvas>
            </div>
            <div class="col-2">

            </div>
            <div class="col-4">
                <h5>Perbandingan Film aktif dan Non-aktif</h5>
                <canvas id="chartPie"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let labelChartBar = null;
        let dataChartBar = null;
        let dataChartPie = null;
        // dijalankan ketika browser sudah generate kode HTML nya ( pas di refresh )
        $(function() {
            $.ajax({
                url: "{{ route('admin.tickets.chart') }}",
                method: 'GET',
                success: function(response) {
                    labelChartBar = response.labels;
                    dataChartBar = response.data;
                    showChart();
                },
                error: function(err) {
                    alert('Gagal mengambil data untuk chart tiket!');
                }
            })
            $.ajax({
                url: "{{ route('admin.movies.chart') }}",
                method: 'GET',
                success: function(response) {
                    dataChartPie = response.data;
                    showChartPie()
                },
                error: function(err) {
                    alert('Gagal mengambil data untuk chart film!');
                }
            })
        });

        function showChart() {
            const ctx = document.getElementById('chartBar');

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labelChartBar,
                    datasets: [{
                        label: 'Pembelian Tiket Bulan ini',
                        data: dataChartBar,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        function showChartPie() {
            const ctx2 = document.getElementById('chartPie');

            new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: [
                        'Film Aktif',
                        'Film tidak aktif'
                    ],
                    datasets: [{
                        label: 'Perbandingan data film aktif',
                        data: dataChartPie,
                        backgroundColor: [
                            'rgb(255, 99, 132)',
                            'rgb(54, 162, 235)'
                        ],
                        hoverOffset: 4
                    }]
                }
            });
        }
    </script>
@endpush
