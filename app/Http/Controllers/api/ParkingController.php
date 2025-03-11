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
            return response()->json($parkings);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function show($id)
    {
        try {
            $parking = $this->parkingService->getParkingById($id);
            return response()->json($parking);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $parking = $this->parkingService->createParking($request->all());
            return response()->json($parking, 201);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $parking = $this->parkingService->updateParking($id, $request->all());
            return response()->json($parking);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $this->parkingService->deleteParking($id);
            return response()->noContent();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }
}
