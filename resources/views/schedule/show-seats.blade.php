@extends('templates.app')

@section('content')
    <div class="container card my-5 p-4" style="margin-bottom: 10% !important">
        <div class="card-body">
            <div>
                <b>{{ $schedule['cinema']['name'] }}</b>
                {{-- now() : ambil tanggal hari ini, format d (tgl) F (nama bulan) Y (tahun) --}}
                <br>
                <b>{{ now()->format('d F, Y') }} || {{ $hour }}</b>
            </div>
            <div class="alert my-2 alert-secondary">
                <i class="fa-solid fa-info text-danger me-3"></i>Anak berusia 2 tahun keatas wajib membeli tiket.
            </div>
            <div class="d-flex justify-content-center">
                <div class="row w-50">
                    <div class="col-4 d-flex">
                        <div class="me-2"style="width: 20px; height: 20px; background: #112646"></div>Kursi Tersedia
                    </div>
                    <div class="col-4 d-flex">
                        <div class="me-2"style="width: 20px; height: 20px; background: #eaeaea"></div>Kursi Terjual
                    </div>
                    <div class="col-4 d-flex">
                        <div class="me-2"style="width: 20px; height: 20px; background: blue"></div>Kursi Dipilih
                    </div>
                </div>
            </div>
            @php
                // membuat data A-H untuk baris kursi
                $row = range('A', 'H');
                // membuat data 1-18 untuk nomor kursi
                $col = range(1, 18);
            @endphp
            {{-- looping baris A-H --}}
            @foreach ($row as $baris)
                <div class="d-flex justify-content-center">
                    {{-- looping angka kursi --}}
                    @foreach ($col as $nomorKursi)
                        {{-- jika kursi nomor 7 kasi space kosong untuk jalan kursi --}}
                        @if ($nomorKursi == 7)
                            <div style="width: 45px"></div>
                        @endif
                        @php
                            $seat = $baris . '-' . $nomorKursi;
                        @endphp
                        @if (in_array($seat, $seatsFormat))
                            <div style="background: #b3b3b3; color: black; text-align: center; padding-top: 10px; width: 45px; height: 45px; border-radius: 10px; margin: 10px 3px; cursor: default">
                                {{ $baris }}-{{ $nomorKursi }}
                            </div>
                        @else
                            <div style="background: #112646; color: white; text-align: center; padding-top: 10px; width: 45px; height: 45px; border-radius: 10px; margin: 10px 3px; cursor: pointer;"
                                onclick="selectedSeats('{{ $schedule->price }}', '{{ $baris }}', '{{ $nomorKursi }}', this)">
                                {{ $baris }}-{{ $nomorKursi }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

    <div class="fixed-bottom w-100 bg-light pt-4 text-center">
        <b class="text-center">LAYAR BIOSKOP</b>
        <div class="row mt-4" style="border: 1px solid #eaeaea">
            <div class="col-6 p-4 text-center" style="border: 1px solid #eaeaea">
                <h5>Total Harga</h5>
                <h5 id="totalPrice">Rp. -</h5>
            </div>
            <div class="col-6 p-4 text-center" style="border: 1px solid #eaeaea">
                <h5>Tempat duduk</h5>
                <h5 id="seats">belum dipilih</h5>
            </div>
        </div>
        {{-- input hidden yang disembunyikan hanya untuk menyimpan nilai yang diperlukan JS untuk proses tambah data ticket --}}
        <input type="hidden" name="user_id" id="user_id" value="{{ Auth::user()->id }}">
        <input type="hidden" name="schedule_id" id="schedule_id" value="{{ $schedule->id }}">
        <input type="hidden" name="hours" id="hours" value="{{ $hour }}">

        <div class="text-center p-2 w-100" style="cursor: pointer" id="btnOrder"><b>RINGKASAN ORDER</b></div>
    </div>
@endsection

@push('script')
    <script>
        // menyimpan data kursi yang dipilih
        let seats = [];
        let totalPrice = 0;

        function selectedSeats(price, baris, nomorKursi, element) {
            // buat A-1
            let seat = baris + "-" + nomorKursi;
            // cek apakah kursi ini sudah dipilih sebelumnya, cek dari apakah ada di array seats diatas atau ngga index nya (indexOf)
            let indexSeat = seats.indexOf(seat);
            // jika tidak ada berarti kursi baru dipilih, kalau gaada index nya -1
            if (indexSeat == -1) {
                // kalau gaada kasi warna biru terang dan simpan data kursi ke array di atas
                element.style.background = "blue";
                seats.push(seat);
            } else {
                // jika ada, berarti ini klik kedua kali di kursi tsb.
                element.style.background = "#112646";
                seats.splice(indexSeat, 1);
            }

            let totalPriceElement = document.querySelector("#totalPrice");
            let seatsElement = document.querySelector("#seats");
            // hitung harga dari parameter dikali jumlah kursi yang dipilih
            totalPrice = price * (seats.length); // length : menghitung jumlah item array
            // simpan harga di element html
            totalPriceElement.innerText = "Rp. " + totalPrice;
            // join(', ') : mengubah array menjadi string dipisahkan dengan tanda tertentu
            seatsElement.innerText = seats.join(", ");

            let btnOrder = document.querySelector("#btnOrder");
            // seats array isinya lebih dar sama dengan satu, aktifin btn ordder
            if (seats.length >= 1) {
                btnOrder.style.background = '#112646';
                btnOrder.style.color = 'white';
                // buat agar ketika di klik mengarah ke proses createTicket
                btnOrder.onclick = createTicket;
            } else {
                btnOrder.style.background = '';
                btnOrder.style.color = '';
                btnOrder.onclick = null;
            }
        }

        function createTicket() {
            // AJAX (asynchronus javascript and XML) : proses mengambil/menambahkan data dari/ke database. hanya bisa gunakan melalui JQuery (Library yang penulisannya berupa JS modern, gaya penulisan lebih singkat $())
            $.ajax({
                url: "{{ route('tickets.store') }}", // route untuk proses data
                method: "POST", // http method sesuai url
                data: {
                    // data yang mau dikirim ke route (kalo di html, input form)
                    _token: "{{ csrf_token() }}",
                    user_id: $("#user_id").val(), // value="" dr input id="user_id"
                    schedule_id: $("#schedule_id").val(),
                    hours: $("#hours").val(),
                    quantity: seats.length, // jumlah item array seats
                    total_price: totalPrice,
                    rows_of_seats: seats,
                    // fillable : value
                },
                success: function(response) { // kalau berhasil, mau ngapain. data hasil disimpen di (response)
                    // console.log(response);
                    // redirect JS : window.location.href
                    // response : message & data
                    let ticketId = response.data.id;
                    window.location.href = `/tickets/${ticketId}/order`;
                },
                error: function(message) { // kalau diservernya ada errro mau ngapain
                    alert("Terjadi kesalahan ketika membuat data tiket!");
                }
            })
        }
    </script>
@endpush
