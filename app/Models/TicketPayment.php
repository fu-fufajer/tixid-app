<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketPayment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'ticket_id',
        'barcode',
        'status',
        'booked_date',
        'paid_date'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
