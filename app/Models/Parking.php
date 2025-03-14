<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{
    use HasFactory;

    protected $table = 'parking';

    protected $fillable = [
        'name',
        'location',
        'total_places',
        'available_places',
        'latitude',
        'longitude',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
