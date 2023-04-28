<?php

namespace Tests\Unit;

use App\Models\DaySlot;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class GetAvailableSlotsTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $this->assertTrue(true);
    }

    public function it_returns_available_slots()
    {
        // Arrange
        $serviceId = 1;
        factory(DaySlot::class, 5)->create(['status' => 1, 'service_id' => $serviceId]);

        // Act
        $response = $this->json('GET', '/available-slots', ['service_id' => $serviceId]);

        // Assert
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'slots' => [
                    '*' => [
                        'start_time',
                        'end_time'
                    ]
                ]
            ]
        ]);
    }

    public function it_returns_no_data_found_when_no_slots_are_available()
    {
        // Arrange
        $serviceId = 1;

        // Act
        $response = $this->json('GET', '/available-slots', ['service_id' => $serviceId]);

        // Assert
        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'error',
            'message' => 'No data found.'
        ]);
    }

    public function testGetAvailableSlots()
    {
        // Arrange
        $serviceId = 1;
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays(7);

        // Create some test data in the DaySlot table
        $daySlots = factory(\App\Models\DaySlot::class, 10)->create([
            'service_id' => $serviceId,
            'status' => 1,
        ]);

        // Act
        $response = $this->post('/api/get-available-slots', [
            'service_id' => $serviceId,
        ]);

        // Assert
        $response->assertStatus(200);
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertCount($endDate->diffInDays($startDate), $responseData);

        foreach ($responseData as $date => $data) {
            $this->assertArrayHasKey('slots', $data);

            $dayNumber = date('N', strtotime($date));
            $filteredSlots = $daySlots->filter(function ($daySlot) use ($dayNumber) {
                return $daySlot->day_number === $dayNumber;
            });

            $this->assertCount($filteredSlots->count(), $data['slots']);
            foreach ($data['slots'] as $slot) {
                $this->assertArrayHasKey('start_time', $slot);
                $this->assertArrayHasKey('end_time', $slot);

                $matchingSlot = $filteredSlots->first(function ($daySlot) use ($slot) {
                    return $daySlot->start_time === $slot['start_time']
                        && $daySlot->end_time === $slot['end_time'];
                });

                $this->assertNotNull($matchingSlot);
            }
        }
    }
}
