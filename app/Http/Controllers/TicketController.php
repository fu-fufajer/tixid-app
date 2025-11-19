<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketPayment;
use App\Models\Schedule;
use App\Models\Promo;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    // show seats
    public function showSeats($scheduleId, $hourId)
    {
        // dd($scheduleId, $hourId);
        $schedule = Schedule::where('id', $scheduleId)->with('cinema')->first();
        // jika tidak ada data jam kasi default nilai kosong
        $hour = $schedule['hours'][$hourId] ?? '-';
        return view('schedule.show-seats', compact('schedule', 'hour'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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
            'date' => now()
        ]);
        // karena dia dipanggil di AJAX jadi return nya bentuk JSON
        return response()->json([
            'message' => 'Berhasil membuat data tiket',
            'data' => $createData
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
        $kodeBarcode = 'TICKET' . $request->ticket_id;

        $qrImage = QrCode::format('svg')->size(300)->margin(2)->errorCorrection('H')->generate($kodeBarcode);

        // penamaan file
        $filename = $kodeBarcode . '.svg';
        // tempat menyimpan barcode public/barcodes
        $path = 'barcodes/' . $filename;
        Storage::disk('public')->put($path, $qrImage);

        $createData = TicketPayment::create([
            'ticket_id' => $request->ticket_id,
            'barcode' => $path,
            'status' => 'process',
            'booked_date' => now()
        ]);

        $ticket = Ticket::find($request->ticket_id);
        if ($request->promo_id != NULL) {
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
            'total_price' => $totalPrice
        ]);
        }

        

        return response()->json([
            'message' => 'Berhasil membuat pesanan tiket sementara!',
            'data' => $createData
        ]);
    }

    public function ticketPaymentPage($ticketId)
    {
        $ticket = Ticket::where('id', $ticketId)->with(['promo', 'ticketPayment', 'schedule'])->first();
        return view('schedule.payment', compact('ticket'));
    }
    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
