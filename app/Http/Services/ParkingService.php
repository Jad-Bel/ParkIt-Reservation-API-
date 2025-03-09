<?php

namespace App\Http\Services;

use App\Models\Parking;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ParkingService
{
    public function getAllParkings()
    {
        $parkings = Parking::all();

        if ($parkings->isEmpty()) {
            throw new Exception('No parking records available');
        }

        return $parkings;
    }

    public function createParking(array $data)
    {

        $validator = Validator::make($data, [
            'name' => 'required|string|min:6',
            'location' => 'required|string',
            'total_places' => 'required|integer',
            'available_places' => 'required|integer'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Parking::create($data);
    }
}
