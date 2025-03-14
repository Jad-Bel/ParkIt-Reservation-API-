<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'parking_id',
        'arrival_time',
        'departure_time',
    ];

    protected $dates = [
        'arrival_time',
        'departure_time',
    ];

    public function parking()
    {
        return $this->belongsTo(Parking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Utiliser Carbon pour formater les dates
    protected function arrivalTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value),
            set: fn ($value) => Carbon::parse($value),
        );
    }

    protected function departureTime(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value),
            set: fn ($value) => Carbon::parse($value),
        );
    }
}
