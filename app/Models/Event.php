<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Customer;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'event_date',
        'note',
        'notified',
    ];

    protected $casts = [
        'event_date' => 'date',
        'notified' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
