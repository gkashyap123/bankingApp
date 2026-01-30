<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    /**
     * Mass assignable fields
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'dob',
        'anniversary',
        'notes',
        'investments',
    ];

    /**
     * Cast fields
     */
    protected $casts = [
        'dob' => 'date',
        'anniversary' => 'date',
        'investments' => 'array',
    ];
}
