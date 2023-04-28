<?php

namespace Tests\Unit;

use App\Models\BookingSlot;
use App\Models\DateBreak;
use App\Models\DayOff;
use App\Models\DaySlot;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;

class BookingSlotTest extends TestCase
{
    use RefreshDatabase;


    /**
     * Test booking a valid slot
     *
     * @return void
     */
    public function testBookValidSlot()
    {
        // Arrange
        $service_id = 1;
        $date = Carbon::now()->addDays(1)->format('Y-m-d');
        $start_time = '10:00:00';
        $end_time = '11:00:00';

        $day_slot = DaySlot::factory()->create([
            'service_id' => $service_id,
            'day_number' => Carbon::parse($date)->dayOfWeekIso,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => '1'
        ]);

        $request = [
            'service_id' => $service_id,
            'date' => $date,
            'slots' => [
                'start_time' => $start_time,
                'end_time' => $end_time
            ]
        ];

        // Act
        $response = $this->postJson('api/book-slot', $request);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseHas('booking_slots', [
            'service_id' => $service_id,
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);
        $this->assertDatabaseHas('booking_slot_personal_details', [
            'booking_slot_id' => BookingSlot::first()->id
        ]);
    }

    /**
     * Test booking an invalid slot due to a date break
     *
     * @return void
     */
    public function testBookInvalidSlotDueToDateBreak()
    {
        // Arrange
        $request = [
            'service_id' => 1,
            'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
            'slots' => [
                'start_time' => '10:00:00',
                'end_time' => '11:00:00'
            ]
        ];

        DateBreak::factory()->create([
            'service_id' => $request['service_id'],
            'start_time' => $request['slots']['start_time'],
            'end_time' => $request['slots']['end_time'],
            'date' => $request['date']
        ]);

        // Act
        $response = $this->postJson('api/book-slot', $request);

        // Assert
        $response->assertStatus(422);
        $response->assertJson(['status' => 'failed']);
        $this->assertDatabaseMissing('booking_slots', [
            'service_id' => $request['service_id'],
            'date' => $request['date'],
            'start_time' => $request['slots']['start_time'],
            'end_time' => $request['slots']['end_time']
        ]);
    }

    public function testBookInvalidSlotDueToDayOff()
    {
        // Create a test day off record for the requested date and service ID
        $serviceId = 1;
        $date = '2023-05-01';
        DayOff::create([
            'service_id' => $serviceId,
            'date' => $date,
        ]);

        // Create a test request with a slot on the same date as the day off record
        $request = new BookSlotRequest([
            'service_id' => $serviceId,
            'date' => $date,
            'slots' => [
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
            ],
        ]);

        // Call the bookSlot method with the test request
        $response = $this->post('api/book-slot', $request->toArray());

        // Assert that the response indicates that the slot is not available due to day off
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'failed',
            'message' => 'No slot available',
        ]);
    }
}
