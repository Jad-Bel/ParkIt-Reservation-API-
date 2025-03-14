<?php

namespace App\Http\Services;

use App\Models\Parking;
use App\Models\Reservation;
use Carbon\Carbon;
use Exception;

class ReservationService
{
    public function reserveParking($userId, $parkingId, $arrivalTime, $departureTime)
    {
        $parking = Parking::find($parkingId);

        if (!$parking) {
            throw new Exception('Parking record not found.');
        }

        if ($parking->available_places <= 0) {
            throw new Exception('No available places in this parking.');
        }

        $arrivalTime = Carbon::parse($arrivalTime);
        $departureTime = Carbon::parse($departureTime);

        $existingReservations = Reservation::where('parking_id', $parkingId)
            ->where(function ($query) use ($arrivalTime, $departureTime) {
                $query->whereBetween('arrival_time', [$arrivalTime, $departureTime])
                    ->orWhereBetween('departure_time', [$arrivalTime, $departureTime])
                    ->orWhere(function ($query) use ($arrivalTime, $departureTime) {
                        $query->where('arrival_time', '<=', $arrivalTime)
                            ->where('departure_time', '>=', $departureTime);
                    });
            })
            ->count();

        if ($existingReservations > 0) {
            throw new Exception('The parking spot is already reserved for the specified period.');
        }

        $reservation = Reservation::create([
            'user_id' => $userId,
            'parking_id' => $parkingId,
            'arrival_time' => $arrivalTime,
            'departure_time' => $departureTime,
        ]);

        $parking->available_places -= 1;
        $parking->save();

        return $reservation;
    }

    public function cancelReservation($reservationId)
    {
        $reservation = Reservation::find($reservationId);

        if (!$reservation) {
            throw new Exception('Reservation not found.');
        }

        $parking = Parking::find($reservation->parking_id);

        if (!$parking) {
            throw new Exception('Parking not found.');
        }

        $reservation->delete();

        $parking->available_places += 1;
        $parking->save();
    }

    public function getUserReservations($userId, $status = null)
    {
        $now = Carbon::now();
        $reservations = Reservation::where('user_id', $userId)
            ->with('parking')
            ->orderBy('arrival_time', 'desc')
            ->get();

        if ($reservations->isEmpty()) {
            throw new Exception('No reservations found for this user.');
        }

        // Filtrer par statut si spécifié
        if ($status) {
            $reservations = $reservations->filter(function ($reservation) use ($now, $status) {
                $reservationStatus = $reservation->departure_time > $now ? 'active' : 'past';
                return $reservationStatus === $status;
            });
        }

        return $reservations;
    }
}
