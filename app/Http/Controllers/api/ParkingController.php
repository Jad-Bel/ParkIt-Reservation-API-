<?php

namespace App\Http\Controllers\api;

use Illuminate\Validation\ValidationException;
use App\Http\Resources\ParkingResource;
use App\Http\Services\ParkingService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parking;
use Exception;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Parking",
 *     description="API Endpoints for managing parkings"
 * )
 */

class ParkingController extends Controller
{
    protected $parkingService;

    public function __construct(ParkingService $parkingService)
    {
        $this->parkingService = $parkingService;
    }


    /**
     * @OA\Get(
     *     path="/api/parkings",
     *     summary="Get all parkings",
     *     tags={"Parking"},
     *     @OA\Response(
     *         response=200,
     *         description="List of all parkings"
     *     )
     * )
     */

    public function index()
    {
        try {
            $parkings = $this->parkingService->getAllParkings();
            return response()->json($parkings);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/parkings/{id}",
     *     summary="Get a parking by ID",
     *     tags={"Parking"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parking details"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking not found"
     *     )
     * )
     */

    public function show($id)
    {
        try {
            $parking = $this->parkingService->getParkingById($id);
            return response()->json($parking);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

     /**
     * @OA\Post(
     *     path="/api/parkings",
     *     summary="Create a new parking",
     *     tags={"Parking"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","location","capacity"},
     *             @OA\Property(property="name", type="string", example="Central Parking"),
     *             @OA\Property(property="location", type="string", example="Downtown, City"),
     *             @OA\Property(property="capacity", type="integer", example=100)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Parking created successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

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

       /**
     * @OA\Put(
     *     path="/api/parkings/{id}",
     *     summary="Update an existing parking",
     *     tags={"Parking"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","location","capacity"},
     *             @OA\Property(property="name", type="string", example="Updated Parking Name"),
     *             @OA\Property(property="location", type="string", example="New Location"),
     *             @OA\Property(property="capacity", type="integer", example=150)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Parking updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */

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

    /**
     * @OA\Delete(
     *     path="/api/parkings/{id}",
     *     summary="Delete a parking",
     *     tags={"Parking"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Parking deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Parking not found"
     *     )
     * )
     */

    public function destroy($id)
    {
        try {
            $this->parkingService->deleteParking($id);
            return response()->json([
                'message' => 'Parking deleted successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/parkings/search",
     *     summary="Search parkings by location and radius",
     *     tags={"Parking"},
     *     @OA\Parameter(
     *         name="latitude",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float"),
     *         example=34.0522
     *     ),
     *     @OA\Parameter(
     *         name="longitude",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="number", format="float"),
     *         example=-118.2437
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         example=5
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of parkings found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */

    public function search(Request $request)
    {
        try {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 5);

            $parkings = $this->parkingService->searchParkings($latitude, $longitude, $radius);

            return response()->json($parkings);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
