<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Parking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ParkingControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching all parkings.
     */
    public function test_index_returns_all_parkings()
    {
        $parkings = Parking::all();

        $response = $this->getJson('/api/parkings');

        $response->assertStatus(200);

        $response->assertJson($parkings->toArray());
    }

    /**
     * Test fetching all parkings when no parkings exist.
     */
    public function test_index_returns_empty_when_no_parkings_exist()
    {
        $response = $this->getJson('/api/parkings');

        $response->assertStatus(200);

        $response->assertJson([]);
    }

    /**
     * Test fetching a specific parking by ID.
     */
    public function test_show_returns_parking()
    {
        $parking = Parking::get(1);

        $response = $this->getJson("/api/parkings/{$parking->id}");

        $response->assertStatus(200);

        $response->assertJson($parking->toArray());
    }

    /**
     * Test creating a new parking record.
     */
    public function test_store_creates_parking()
    {
        $data = [
            'name' => 'Test Parking',
            'location' => 'Test Location',
            'total_places' => 100,
            'available_places' => 50,
        ];

        $response = $this->postJson('/api/parkings', $data);

        $response->assertStatus(201);

        // Assert that the response contains the correct data
        $response->assertJson($data);

        // Assert that the parking record was actually created in the database
        $this->assertDatabaseHas('parkings', $data);
    }

    /**
     * Test updating a parking record.
     */
    public function test_update_parking()
    {
        // Create a parking record
        $parking = Parking::factory()->create();

        $updatedData = [
            'name' => 'Updated Parking',
        ];

        $response = $this->putJson("/api/parkings/{$parking->id}", $updatedData);

        $response->assertStatus(200);

        $response->assertJson($updatedData);

        $this->assertDatabaseHas('parkings', $updatedData);
    }

    /**
     * Test deleting a parking record.
     */
    public function test_delete_parking()
    {
        $parking = Parking::factory()->create();

        $response = $this->deleteJson("/api/parkings/{$parking->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('parkings', ['id' => $parking->id]);
    }
}
