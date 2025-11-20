@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4">
        <div class="card-body">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                        type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Ticket
                        Aktif</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                        type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Ticket
                        Non-AKtif</button>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab"
                    tabindex="0">
                    <h5 class="my-4">
                        Data Tiket Aktif, {{ Auth::user()->name }}
                    </h5>
                    <div class="d-flex flex-wrap">
                        @foreach ($ticketActive as $ticket)
                            <div class="w-25 me-4 p-4" style="border: 1px solid #eaeaea; border-radius: 5px">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <b>{{ $ticket['schedule']['cinema']['name'] }}</b>
                                    </div>
                                    <div>
                                        <h5 class="m-0">STUDIO</h5>
                                    </div>
                                </div>
                                <hr>
                                <div class="ticket-body text-start">
                                    <p class="ticket-title mb-1">{{ $ticket['schedule']['movie']['title'] }}</p>
                                    <div class="ticket-details">
                                        <small>Tanggal: </small>
                                        {{ \Carbon\Carbon::parse($ticket['ticketPayment']['booked_date'])->format('d F, Y') }}
                                        <br>
                                        <small>Waktu: </small> {{ \Carbon\Carbon::parse($ticket['hours'])->format('H:i') }}
                                        <br>
                                        <small>Kursi: </small>{{ implode(', ', $ticket['rows_of_seats']) }}<br>
                                        @php
                                            $price = $ticket['total_price'] + 4000 * $ticket['quantity'];
                                        @endphp
                                        <small>Harga Bayar: </small> Rp. {{ number_format($price, 0, ',', '.') }}
                                    </div>
                                    <a href="{{ route('tickets.export_pdf', $ticket['id']) }}"
                                        class="btn btn-secondary mt-3">
                                        Unduf PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
                    tabindex="0">
                    <h5 class="my-4">
                        Data Tiket Tidak Aktif, {{ Auth::user()->name }}
                    </h5>
                    <div class="d-flex flex-wrap">
                        @foreach ($ticketNonActive as $ticket)
                            <div class="w-25 me-4 p-4" style="border: 1px solid #eaeaea; border-radius: 5px">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <b>{{ $ticket['schedule']['cinema']['name'] }}</b>
                                    </div>
                                    <div>
                                        <h5 class="m-0">STUDIO</h5>
                                    </div>
                                </div>
                                <hr>
                                <div class="ticket-body text-start">
                                    <p class="ticket-title mb-1">{{ $ticket['schedule']['movie']['title'] }}</p>
                                    <div class="ticket-details">
                                        <small>Tanggal: </small> <b class="text-danger">{{ \Carbon\Carbon::parse($ticket['ticketPayment']['booked_date'])->format('d F, Y') }}</b>
                                        <br>
                                        <small>Waktu: </small> {{ \Carbon\Carbon::parse($ticket['hours'])->format('H:i') }}
                                        <br>
                                        <small>Kursi: </small>{{ implode(', ', $ticket['rows_of_seats']) }}<br>
                                        @php
                                            $price = $ticket['total_price'] + 4000 * $ticket['quantity'];
                                        @endphp
                                        <small>Harga Bayar: </small> Rp. {{ number_format($price, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
