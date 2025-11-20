<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Schedule;
use App\Models\Ticket;
use App\Models\TicketPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf; 

class TicketController extends Controller
{
    // show seats
    public function showSeats($scheduleId, $hourId)
    {
        // dd($scheduleId, $hourId);
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        // jika tidak ada data jam kasi default nilai kosong
        $hour = $schedule['hours'][$hourId] ?? '-';
        // ambil data kursi di tiket yg sesuai dengan jam, tanggal, dan sudah dibayar
        $seats = Ticket::whereHas('ticketPayment', function ($q) {
            // whereDate mencari berdasarkan tanggal
            $q->whereDate('paid_date', now()->format('Y-m-d'));
        })->whereTime('hours', $hour)->pluck('rows_of_seats');
        // pluck mengambil hanya dari 1 field, bedanya dengan value kalau value ambil 1 data pertama dari field tersebut, kalau pluck ambil semua data dari field tersebut
        $seatsFormat = array_merge(...$seats);

        // dd($seats);
        return view('schedule.show-seats', compact('schedule', 'hour', 'seatsFormat'));
    }

    public function chartData()
    {
        // ambil data dibulan ini
        $month = now()->format('m'); // bulan saat ini
        // ambil data tiket yang sudah dibayar dan dibayar di bulan ini, kemudian kelompokan datanya berdasarkan tanggal pembayaran groupBy
        $tickets = Ticket::whereHas('ticketPayment', function ($q) use ($month) {
            // whereMonth mencari berdasarkan bulan
            $q->whereMonth('paid_date', $month);
        })->get()->groupBy(function ($ticket) {
            return \Carbon\Carbon::parse($ticket->ticketPayment->paid_date)->format('Y-m-d');
        })->toArray();
        // $tickets berisi ['tanggal' => data digital tersebut]
        // pisahkan tanggal untuk labels di chartjs
        $labels = array_keys($tickets);
        // hitung isi data di key tanggal tersebut, utk data di chart js
        $data = [];
        foreach ($tickets as $item) {
            // simpan hasil count ke array data
            array_push($data, count($item));
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function index()
    {
        $ticketActive = Ticket::whereHas('ticketPayment', function($q) {
            $q->whereDate('booked_date', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        $ticketNonActive = Ticket::whereHas('ticketPayment', function($q) {
            $q->whereDate('booked_date', '<', now()->format('Y-m-d'))->where('paid_date', '<>', NULL);
        })->get();
        return view('ticket.index', compact('ticketActive', 'ticketNonActive'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'schedule_id' => 'required',
            'hours' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'rows_of_seats' => 'required',
        ]);

        $createData = Ticket::create([
            'user_id' => $request->user_id,
            'schedule_id' => $request->schedule_id,
            'hours' => $request->hours,
            'total_price' => $request->total_price,
            'quantity' => $request->quantity,
            'rows_of_seats' => $request->rows_of_seats,
            'activated' => 0, // kalau udah dibayar baru diubah ke 1 (aktif)
            'date' => now(),
        ]);

        // karena dia dipanggil di AJAX jadi return nya bentuk JSON
        return response()->json([
            'message' => 'Berhasil membuat data tiket',
            'data' => $createData,
        ]);
    }

    public function ticketOrder($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule.movie', 'schedule.cinema'])->first();
        $promos = Promo::where('activated', 1)->get();

        return view('schedule.order', compact('ticket', 'promos'));
    }

    public function ticketPayment(Request $request)
    {
        $kodeBarcode = 'TICKET'.$request->ticket_id;

        $qrImage = QrCode::format('svg')->size(300)->margin(2)->errorCorrection('H')->generate($kodeBarcode);

        // penamaan file
        $filename = $kodeBarcode.'.svg';
        // tempat menyimpan barcode public/barcodes
        $path = 'barcodes/'.$filename;
        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $request->ticket_id,
            'barcode' => $path,
            'status' => 'process',
            'booked_date' => now(),
        ]);

        $ticket = Ticket::find($request->ticket_id);
        if ($request->promo_id != null) {
            $promo = Promo::find($request->promo_id);
            if ($promo['type'] == 'percent') {
                $discount = $ticket['total_price'] * ($promo['discount'] / 100);
            } else {
                $discount = $promo['discount'];
            }
            $totalPrice = $ticket['total_price'] - $discount;

            // update total harga setelah menggunakan diskon
            $updateTicket = Ticket::where('id', $request->ticket_id)->update([
                'promo' => $request->promo_id,
                'total_price' => $totalPrice,
            ]);
        }

        return response()->json([
            'message' => 'Berhasil membuat pesanan tiket sementara!',
            'data' => $createData,
        ]);
    }

    public function ticketPaymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['promo', 'ticketPayment', 'schedule'])->first();

        return view('schedule.payment', compact('ticket'));
    }

    public function paymentProof($ticketId)
    {
        $updateData = Ticket::where('id', $ticketId)->update([
            'activated' => 1,
        ]);

        // karena data hanya ada ticket_id jadi update payment berdasarkan ticket_id nya
        $updatePayment = TicketPayment::where('ticket_id', $ticketId)->update([
            'paid_date' => now(),
        ]);

        // karena route receipt perlu ticket_id maka perlu dikirim
        return redirect()->route('tickets.receipt', $ticketId);
    }

    public function ticketReceipt($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'ticketPayment'])->first();
        return view('schedule.receipt', compact('ticket'));
    }

    public function exportPdf($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['schedule', 'schedule.cinema', 'schedule.movie', 'promo', 'ticketPayment'])->first()->toArray();
        // buat inisial nama data yang nanti akan digunakan pada blade pdf
        view()->share('ticket', $ticket);
        // generate file blade yg akan dicetak pdf
        $pdf = Pdf::loadView('schedule.export-pdf', $ticket);
        //utk pdf dgn nama file spesial
        $fileName = 'TICKET' . $ticket['id'] . '.pdf';
        return $pdf->download($fileName);
    }

    public function show(Ticket $ticket)
    {
        //
    }

    public function edit(Ticket $ticket)
    {
        //
    }

    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    public function destroy(Ticket $ticket)
    {
        //
    }
}
