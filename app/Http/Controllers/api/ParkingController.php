<?php

namespace App\Http\Controllers\api;

use App\Models\Parking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\ParkingService;
use App\Http\Resources\ParkingResource;
use Exception;
use Illuminate\Validation\ValidationException;

class ParkingController extends Controller
{
    protected $parkingService;

    public function __construct(ParkingService $parkingService)
    {
        $this->parkingService = $parkingService;
    }
    public function index()
    {
        try {
            $parkings = $this->parkingService->getAllParkings();
            return ParkingResource::collection($parkings);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $parking = $this->parkingService->createParking($request->all());
            return new ParkingResource($parking);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }
}
