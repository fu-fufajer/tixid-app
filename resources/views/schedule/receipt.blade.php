@extends('templates.app')

@section('content')
    <div class="card w-50 d-block mx-auto my-5 p-4">
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('tickets.export_pdf', $ticket['id']) }}" class="btn btn-secondary">Unduh PDF</a>
        </div>
        <div class="card-body d-flex justfy-content-center flex-wrap">
            @foreach ($ticket['rows_of_seats'] as $kursi)
                <div class="me-3">
                    <b>{{ $ticket['schedule']['cinema']['name'] }}</b>
                    <hr>
                    <b>{{ $ticket['schedule']['movie']['title'] }}</b>
                    <p>Tanggal: {{ \Carbon\Carbon::parse($ticket['ticketPayment']['booked_date'])->format('d F, Y') }}</p> {{-- F: nama bulan --}}
                    <p>Waktu: {{ \Carbon\Carbon::parse($ticket['hours'])->format('H:1') }}</p>
                    <p>Kursi: {{ $kursi }}</p>
                    <p>Harga Tiket: Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>
@endsection