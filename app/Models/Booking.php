<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $primaryKey = 'id';

    public $incrementing = false;  // ULID is not auto-increment
    protected $keyType = 'string'; // ULID stored as string

    protected $fillable = [
        'phone',
        'booking_date',
        'booking_location',
        'table_no',
        'price',
        'amount_paid',
        'employee_id',
    ];
}
