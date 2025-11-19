@extends('templates.app')

@section('content')
    <div class="container my-5 p-4 card" style="margin-bottom: 10% !important">
        <div class="card-body">
            <h5 class="mb-5">RINGKASAN PEMESANAN</h5>
            <div class="d-flex">
                <img src="{{ asset('storage/' . $ticket['schedule']['movie']['poster']) }}" width="120">
                <div class="ms-4">
                    <b class="text-warning">{{ $ticket['schedule']['cinema']['name'] }}</b>
                    <b> - {{ $ticket['schedule']['movie']['title'] }}</b>
                    <table>
                        <tr>
                            <td>Genre : </td>
                            <td style="width: 50px"></td>
                            <td><b>{{ $ticket['schedule']['movie']['genre'] }}</b></td>
                        </tr>
                        <tr>
                            <td>Sutradara : </td>
                            <td style="width: 50px"></td>
                            <td><b>{{ $ticket['schedule']['movie']['director'] }}</b></td>
                        </tr>
                        <tr>
                            <td>Durasi : </td>
                            <td style="width: 50px"></td>
                            <td><b>{{ $ticket['schedule']['movie']['duration'] }}</b></td>
                        </tr>
                        <tr>
                            <td>Rating Usia : </td>
                            <td style="width: 50px"></td>
                            <td><b><span
                                        class="badge badge-danger">{{ $ticket['schedule']['movie']['age_rating'] }}</span></b>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <b class="text-secondary">NOMOR PESANAN : {{ $ticket['id'] }}</b>
            <hr>
            <b>Detail Pesanan</b>
            <table>
                <tr>
                    <td>Kursi Dipilih</td>
                    <td style="width: 50px"></td>
                    <td><b>{{ implode(', ', $ticket['rows_of_seats']) }}</b></td> {{-- implode : array jadi string dipisahkan dengan tanda ', ' --}}
                </tr>
                <tr>
                    <td>Harga Tiket</td>
                    <td style="width: 50px"></td>
                    <td>Rp. <b>{{ number_format($ticket['schedule']['price']) }}</b>
                        <b class="text-secondary">x {{ $ticket['quantity'] }}</b>
                    </td>
                </tr>
                <tr>
                    <td>Biaya Layanan</td>
                    <td style="width: 50px"></td>
                    <td>Rp. 4.000 <b class="text-secondary">x {{ $ticket['quantity'] }}</b></td>
                </tr>
            </table>
            <p>Gunakan Promo : </p>
            <select name="promo_id" id="promo_id" class="form-select" onchange="selectPromo(this)">
                @if (count($promos) < 1)
                    <option disabled hidden selected>Tidak ada promo aktif saat ini</option>
                @else
                    <option disabled hidden selected>Pilih promo</option>
                    @foreach ($promos as $promo)
                        <option value="{{ $promo['id'] }}">{{ $promo['promo_code'] }} -
                            {{ $promo['type'] == 'percent' ? $promo['discount'] . '%' : 'Rp. ' . number_format($promo['discount'], 0, ',', '.') }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>
    <input type="hidden" name="ticket_id" value="{{ $ticket['id'] }}" id="ticket_id">
    <div class="fixed-bottom w-100 p-4 text-white text-center" style="background: #112646; cursor: pointer;"
        onclick="createQR()"><b>BAYAR SEKARANG</b></div>
@endsection

@push('script')
    <script>
        let promoId = null;

        function selectPromo(element) {
            // isi nilai promo dari value select
            promoId = element.value;
        }

        function createQR() {
            let data = {
                _token: "{{ csrf_token() }}",
                promo_id: promoId,
                ticket_id: $("#ticket_id").val() // hapus spasi antara $ dan {
            };

            $.ajax({
                url: "{{ route('tickets.payment') }}",
                type: "POST",
                data: data,
                success: function(response) {
                    const ticketId = response.data.ticket_id;
                    window.location.href = `/tickets/${ticketId}/payment`;
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat mengirim data!");
                    console.error(xhr.responseText);
                }
            })
        }
    </script>
@endpush
