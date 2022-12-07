<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

            /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticketID',
        'orderBy',
        'name',
        'email',
        'phone',
        'quantity',
        'total',
        'bookingCode',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}