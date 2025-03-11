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

    public function test_get_all_parkings(): void
    {
        $parkings = Parking::factory()->count(3)->create();

        $response = $this->getJson('/api/parkings');

        $response->assertStatus(200)
            ->assertJson($parkings->toArray());
    }

    public function test_get_all_parkings_not_found(): void
    {
        $response = $this->getJson('/api/parkings');

        $response->assertStatus(404)
            ->assertJson(['error' => 'No parking records available']);
    }

    public function test_get_parking_by_id(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->getJson("/api/parkings/{$parking->id}");

        $response->assertStatus(200)
            ->assertJson($parking->toArray());
    }


    public function test_get_parking_by_id_not_found(): void
    {
        $response = $this->getJson('/api/parkings/999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Parking record not found.']);
    }

    public function test_create_parking(): void
    {
        $data = [
            'name' => 'Test Parking',
            'location' => 'Test Location',
            'total_places' => 100,
            'available_places' => 50,
        ];

        $response = $this->postJson('/api/parkings', $data);

        $response->assertStatus(201)
            ->assertJson($data);

        $this->assertDatabaseHas('parking', $data);
    }

    public function test_create_parking_validation_errors(): void
    {
        $data = [
            'name' => '', // Name is required
            'location' => '', // Location is required
            'total_places' => 'not an integer', // Should be an integer
            'available_places' => 'not an integer', // Should be an integer
        ];

        $response = $this->postJson('/api/parkings', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'location', 'total_places', 'available_places']);
    }

    public function test_update_parking(): void
    {
        $parking = Parking::factory()->create();

        $data = [
            'name' => 'Updated Parking',
            'available_places' => 30,
        ];

        $response = $this->putJson("/api/parkings/{$parking->id}", $data);

        $response->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('parking', $data);
    }

    public function test_update_parking_not_found(): void
    {
        $data = [
            'name' => 'Updated Parking',
            'available_places' => 30,
        ];

        $response = $this->putJson('/api/parkings/999', $data);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Parking record not found.']);
    }

    public function test_delete_parking(): void
    {
        $parking = Parking::factory()->create();

        $response = $this->deleteJson("/api/parkings/{$parking->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('parking', ['id' => $parking->id]);
    }

    public function test_delete_parking_not_found(): void
    {
        $response = $this->deleteJson('/api/parkings/999');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Parking record not found.']);
    }
}
