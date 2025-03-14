<?php

namespace App\Http\Controllers\api;

use App\Http\Services\StatisticsService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="Endpoints for admin functionalities"
 * )
 */
class AdminController extends Controller
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * Get parking statistics.
     *
     * @OA\Get(
     *     path="/api/admin/parking-statistics",
     *     tags={"Admin"},
     *     summary="Retrieve parking statistics",
     *     description="Fetch various statistics related to parking occupancy and availability.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with parking statistics",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_parkings", type="integer", example=50),
     *             @OA\Property(property="occupied", type="integer", example=30),
     *             @OA\Property(property="available", type="integer", example=20)
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal Server Error")
     * )
     */

    public function parkingStatistics()
    {
        try {
            $statistics = $this->statisticsService->getParkingStatistics();
            return response()->json($statistics, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
