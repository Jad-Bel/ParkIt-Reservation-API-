<?php

namespace App\Http\Controllers\api;

use Illuminate\Validation\ValidationException;
use App\Http\Services\ReservationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Reservations",
 *     description="Endpoints for managing parking reservations"
 * )
 */
class ReservationController extends Controller
{
    protected $reservationService;

    public function __construct(ReservationService $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * Reserve a parking spot.
     *
     * @OA\Post(
     *     path="/api/reservations/{parkingId}",
     *     tags={"Reservations"},
     *     summary="Reserve a parking spot",
     *     description="Allows a user to reserve a parking spot by providing their user ID and reservation timings.",
     *     @OA\Parameter(
     *         name="parkingId",
     *         in="path",
     *         required=true,
     *         description="ID of the parking spot to reserve",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id", "arrival_time", "departure_time"},
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="arrival_time", type="string", format="date-time", example="2024-03-14T08:00:00"),
     *             @OA\Property(property="departure_time", type="string", format="date-time", example="2024-03-14T10:00:00")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reservation successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="parking_id", type="integer", example=5),
     *             @OA\Property(property="arrival_time", type="string", format="date-time"),
     *             @OA\Property(property="departure_time", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=400, description="Bad request")
     * )
     */

    public function reserve(Request $request, $parkingId)
    {
        try {
            $data = $request->validate([
                'user_id' => 'required|exists:users,id', // Vérifier que l'utilisateur existe
                'arrival_time' => 'required|date',
                'departure_time' => 'required|date|after:arrival_time',
            ]);

            $reservation = $this->reservationService->reserveParking(
                $data['user_id'],
                $parkingId,
                $data['arrival_time'],
                $data['departure_time']
            );

            return response()->json($reservation, 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

      /**
     * Cancel a reservation.
     *
     * @OA\Delete(
     *     path="/api/reservations/{reservationId}",
     *     tags={"Reservations"},
     *     summary="Cancel a reservation",
     *     description="Allows a user to cancel their reservation by ID.",
     *     @OA\Parameter(
     *         name="reservationId",
     *         in="path",
     *         required=true,
     *         description="ID of the reservation to cancel",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Reservation cancelled successfully"),
     *     @OA\Response(response=400, description="Bad request")
     * )
     */

    public function cancel($reservationId)
    {
        try {
            $this->reservationService->cancelReservation($reservationId);
            return response()->json(['message' => 'Reservation cancelled successfully.'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

       /**
     * Get reservations of a specific user.
     *
     * @OA\Get(
     *     path="/api/reservations/user/{userId}",
     *     tags={"Reservations"},
     *     summary="Get user reservations",
     *     description="Fetch all reservations of a specific user.",
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="List of reservations"),
     *     @OA\Response(response=404, description="No reservations found")
     * )
     */

    public function getUserReservations($userId)
    {
        $reservations = Reservation::where('user_id', $userId)
            ->with('parking')
            ->orderBy('arrival_time', 'desc')
            ->get();

        if ($reservations->isEmpty()) {
            throw new Exception('No reservations found for this user.');
        }

        $now = Carbon::now();
        $reservations->each(function ($reservation) use ($now) {
            $reservation->status = $reservation->departure_time > $now ? 'active' : 'past';
        });

        return $reservations;
    }

     /**
     * Get reservations of the authenticated user.
     *
     * @OA\Get(
     *     path="/api/reservations/my",
     *     tags={"Reservations"},
     *     summary="Get my reservations",
     *     description="Fetch all reservations of the currently authenticated user.",
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(response=200, description="List of reservations"),
     *     @OA\Response(response=400, description="Bad request")
     * )
     */

    public function myReservations(Request $request)
    {
        try {
            $userId = $request->user()->id; // Récupérer l'ID de l'utilisateur connecté
            $reservations = $this->reservationService->getUserReservations($userId);
            return response()->json($reservations, 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
