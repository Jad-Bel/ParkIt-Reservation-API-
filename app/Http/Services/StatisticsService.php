<?php

namespace App\Http\Services;

use App\Models\Parking;
use App\Models\Reservation;
use Carbon\Carbon;

class StatisticsService
{
    public function getParkingStatistics()
    {
        // nombre total de parkings
        $totalParkings = Parking::count();

        // nombre total de reservations
        $totalReservations = Reservation::count();

        // taux d'occupation moyen des parking
        $averageOccupancyRate = Parking::withCount('reservations')
            ->get()
            ->avg(function ($parking) {
                return ($parking->reservations_count / $parking->total_places) * 100;
            });

        // parkings les plus populaires
        $mostPopularParkings = Parking::withCount('reservations')
            ->orderBy('reservations_count', 'desc')
            ->take(5)
            ->get();

        // reservations par heure
        $peakHours = Reservation::selectRaw('EXTRACT(HOUR FROM arrival_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
            
        return [
            'total_parkings' => $totalParkings,
            'total_reservations' => $totalReservations,
            'average_occupancy_rate' => round($averageOccupancyRate, 2),
            'most_popular_parkings' => $mostPopularParkings,
            'peak_hours' => $peakHours,
        ];
    }
}
