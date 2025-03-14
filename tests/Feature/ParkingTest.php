<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\api\ParkingController;
use Illuminate\Validation\ValidationException;
use App\Http\Services\ParkingService;
use Illuminate\Http\Request;
use App\Models\Parking;
use Tests\TestCase;
use Exception;

use Illuminate\Http\JsonResponse;


class ParkingTest extends TestCase
{
    // use RefreshDatabase;

    // public function test_get_all_parkings(): void
    // {
    //     $parkings = Parking::factory()->count(3)->create();

    //     $response = $this->getJson('/api/parkings');

    //     $response->assertStatus(200)
    //         ->assertJson($parkings->toArray());
    // }

    // public function test_get_all_parkings_not_found(): void
    // {
    //     $response = $this->getJson('/api/parkings');

    //     $response->assertStatus(404)
    //         ->assertJson(['error' => 'No parking records available']);
    // }

    // public function test_get_parking_by_id(): void
    // {
    //     $parking = Parking::factory()->create();

    //     $response = $this->getJson("/api/parkings/{$parking->id}");

    //     $response->assertStatus(200)
    //         ->assertJson($parking->toArray());
    // }


    // public function test_get_parking_by_id_not_found(): void
    // {
    //     $response = $this->getJson('/api/parkings/999');

    //     $response->assertStatus(404)
    //         ->assertJson(['error' => 'Parking record not found.']);
    // }

    public function test_create_parking(): void
    {
        $data = [
            'name' => 'Test Parking',
            'location' => 'Test Location',
            'total_places' => 100,
            'available_places' => 50,
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ];

        $response = $this->postJson('/api/parkings', $data);

        $response->assertStatus(201)
            ->assertJson($data);

        $this->assertDatabaseHas('parking', $data);
    }

    // public function test_create_parking_validation_errors(): void
    // {
    //     $data = [
    //         'name' => '',
    //         'location' => '',
    //         'total_places' => 'dqwdq',
    //         'available_places' => 'dflekd',
    //         'latitude' => '',
    //         'longitude' => '',
    //     ];

    //     $response = $this->postJson('/api/parkings', $data);

    //     $response->assertStatus(422)
    //         ->assertJsonValidationErrors(['name', 'location', 'total_places', 'available_places']);
    // }

    // public function test_update_parking(): void
    // {
    //     $parking = Parking::factory()->create();

    //     $data = [
    //         'name' => 'Updated Parking',
    //         'available_places' => 30,
    //     ];

    //     $response = $this->putJson("/api/parkings/{$parking->id}", $data);

    //     $response->assertStatus(200)
    //         ->assertJson($data);

    //     $this->assertDatabaseHas('parking', $data);
    // }

    // public function test_update_parking_not_found(): void
    // {
    //     $data = [
    //         'name' => 'Updated Parking',
    //         'available_places' => 30,
    //     ];

    //     $response = $this->putJson('/api/parkings/999', $data);

    //     $response->assertStatus(404)
    //         ->assertJson(['error' => 'Parking record not found.']);
    // }

    // public function test_delete_parking(): void
    // {
    //     $parking = Parking::factory()->create();

    //     $response = $this->deleteJson("/api/parkings/{$parking->id}");

    //     $response->assertStatus(204);

    //     $this->assertDatabaseMissing('parking', ['id' => $parking->id]);
    // }

    // public function test_delete_parking_not_found(): void
    // {
    //     $response = $this->deleteJson('/api/parkings/999');

    //     $response->assertStatus(404)
    //         ->assertJson(['error' => 'Parking record not found.']);
    // }

    public function test_search_available_parkings_within_radius(): void
    {
        $parking1 = Parking::create([
            'name' => 'Parking A',
            'location' => '123 Main St',
            'total_places' => 100,
            'available_places' => 50,
            'latitude' => 48.8566,
            'longitude' => 2.3522,
        ]);

        $parking2 = Parking::create([
            'name' => 'Parking B',
            'location' => '456 Elm St',
            'total_places' => 80,
            'available_places' => 30,
            'latitude' => 48.8570,
            'longitude' => 2.3525,
        ]);

        $parking3 = Parking::create([
            'name' => 'Parking C',
            'location' => '789 Oak St',
            'total_places' => 60,
            'available_places' => 10,
            'latitude' => 49.0000,
            'longitude' => 2.5000,
        ]);

        $searchData = [
            'latitude' => 49.0000,
            'longitude' => 2.5000,
            'radius' => 5
        ];

        $response = $this->getJson('/api/parkings/search', $searchData);
// dd($response);
        $response->assertStatus(200);

        $response->assertJsonCount(2);

        $response->assertJsonFragment([
            'name' => 'Parking A',
            'available_places' => 50,
        ]);
        $response->assertJsonFragment([
            'name' => 'Parking B',
            'available_places' => 30,
        ]);

        $response->assertJsonMissing([
            'name' => 'Parking C',
        ]);
    }

    // public function test_search_missing_required_parameters()
    // {
    //     $response = $this->getJson('/api/parkings/search');

    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors(['latitude', 'longitude']);
    // }

    // public function test_search_parkings_with_no_available_places()
    // {
    //     Parking::create([
    //         'name' => 'Parking E',
    //         'location' => '202 Maple St',
    //         'latitude' => 48.8566,
    //         'longitude' => 2.3522,
    //         'total_places' => 100,
    //         'available_places' => 0,
    //     ]);

    //     $response = $this->getJson('/api/parkings/search', [
    //         'latitude' => 48.8566,
    //         'longitude' => 2.3522,
    //         'radius' => 5,
    //     ]);

    //     $response->assertStatus(404);
    //     $response->assertJson([
    //         'error' => 'No available parkings found within the specified area.',
    //     ]);
    // }
}
