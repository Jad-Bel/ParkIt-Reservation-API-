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
            'name' => 'required|string',
            'location' => 'required|string',
            'total_places' => 'required|integer',
            'available_places' => 'required|integer'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Parking::create($data);
    }

    public function getParkingById($id)
    {
        $parking = Parking::find($id);

        if (!$parking) {
            throw new Exception('Parking record not found.');
        }

        return $parking;
    }

    public function updateParking($id, array $data)
    {
        $parking = Parking::find($id);

        if (!$parking) {
            throw new \Exception('Parking record not found.');
        }

        $validator = Validator::make($data, [
            'name' => 'sometimes|string',
            'location' => 'sometimes|string',
            'total_places' => 'sometimes|integer',
            'available_places' => 'sometimes|integer',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $parking->update($data);
        return $parking;
    }

    public function deleteParking($id)
    {
        $parking = Parking::find($id);

        if (!$parking) {
            throw new \Exception('Parking record not found.');
        }

        $parking->delete();
        return response()->noContent();
    }

    public function searchParkings($latitude, $Longitude, $radius)
    {
        $earthRadius = 6371;

        $maxLat = $latitude + rad2deg($radius / $earthRadius);
        $minLat = $latitude - rad2deg($radius / $earthRadius);
        $maxLon = $Longitude + rad2deg(asin($radius / $earthRadius) / cos(deg2rad($latitude)));
        $minLon = $Longitude - rad2deg(asin($radius / $earthRadius) / cos(deg2rad($latitude)));

        $parkings = Parking::whereBetween('latitude', [$minLat, $maxLat])
                           ->whereBetween('longitude', [$minLon, $maxLon])
                           ->where('available_places', '>', 0)
                           ->get();

        if ($parkings->isEmpty()) {
            throw new Exception('No available parkings found within the specified area');
        }

        return $parkings;
    }
}
