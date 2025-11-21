<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Tket</title>
    <style>
        .tickets-wrapper {
            width: 50%;
            margin-left: 20%;
        }

        .ticket-item {
            width: 340px padding: 10px 22px;
        }

        .studio-title {
            margin: 0;
        }

        .separator {
            margin: 10px 0;
            height: 1px;
            border: none;
            background: rgba(0, 0, 0, 0.2);
        }

        .ticket-title {
            margin: 0 0 8px 0;
            font-weight: bold;
        }

        .ticket-details small {
            font-weight: bold;
            display: inline-block;
            width: 60px;
        }
    </style>
</head>

<body>
    <div class="tickets-wrapper">
        @foreach ($ticket['rows_of_seats'] as $item)
            <div class="ticket-item">
                <div class="ticket-header">
                    <div><b>{{ $ticket['schedule']['cinema']['name'] }}</b></div>
                    <div>
                        <h5 class="studio-title">STUDIO</h5>
                    </div>
                </div>
                <hr class="separator">
                <div class="ticket-body">
                    <p class="ticket-title">{{ $ticket['schedule']['movie']['title'] }}</p>
                    <div class="ticket-details">
                        <small>Tanggal: </small>
                        {{ \Carbon\Carbon::parse($ticket['ticket_payment']['booked_date'])->format('d F, Y') }} <br>
                        <small>Waktu: </small> {{ \Carbon\Carbon::parse($ticket['hours'])->format('H:i') }} <br>
                        <small>Kursi: </small> {{ $item }} <br>
                        <small>Price: </small> Rp. {{ number_format($ticket['schedule']['price'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
