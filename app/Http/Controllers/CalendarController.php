<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookSlotRequest;
use App\Models\BookingSlot;
use App\Models\BookingSlotPersonalDetail;
use App\Models\DateBreak;
use App\Models\DateOff;
use App\Models\DayBreak;
use App\Models\DayOff;
use App\Models\DaySlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CalendarController extends Controller
{

    public function getAvailableSlots(Request $request) {

        $slots = DaySlot::where('status', '1')->where('service_id', $request->service_id)->get();
        if (!empty($slots) && count($slots) > 0) {
            $dates = [];
            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDays(7);
            while ($startDate->lt($endDate)) {
                $day_date = $startDate->format('Y-m-d');

                $day_number = date('N', strtotime($day_date));
                $filtered_data = $this->filterSlots($slots, $day_number);
                if (!empty($filtered_data)) {
                    foreach ($filtered_data as $filtered_slot) {
                        $dates[$day_date]['slots'][] = [
                            'start_time' => $filtered_slot->start_time,
                            'end_time' => $filtered_slot->end_time
                        ];
                    }

                }

                $startDate->addDay();

            }
            return response()->success('success', $dates);
        }
        return response()->failed('No data found.');
    }


    public function bookSlot(BookSlotRequest $request) {

        /*
         * Payload sample


        {
    "service_id": "1",
    "date": "2023-04-27",
    "slots": {
        "start_time": "10:00:00",
        "end_time": "10:15:00"
    },
    "personal_details":[
        {
            "email": "a@g",
            "first_name": "a",
            "last_name": "b"
        },
        {
            "email": "c@g",
            "first_name": "c",
            "last_name": "d"
        },
        {
            "email": "c@g",
            "first_name": "c",
            "last_name": "d"
        }
    ]
}



         */

        $day_number = date('N', strtotime($request->date));
        $slot = DaySlot::where('service_id', $request->service_id)
            ->whereTime('start_time', '>=', $request->slots['start_time'])
            ->whereTime('end_time', '<=', $request->slots['end_time'])
            ->where('day_number', $day_number)
            ->where('status', '1')
            ->first();
        if (!empty($slot)) {


            // Check in date_breaks
            $date_breaks = DateBreak::checkSlotInDateBreak($request);
            if (!empty($date_breaks) && count($date_breaks) > 0) {
                return response()->failed('No slot available');
            }


            // Check in date_off
            $date_off = DateOff::checkSlotInDateOff($request);
            if (!empty($date_off) && count($date_off) > 0) {
                return response()->failed('No slot available');
            }

            // Check in day_breaks
            $day_breaks = DayBreak::checkSlotInDayBreak($request, $day_number);
            if (!empty($day_breaks) && count($day_breaks) > 0) {
                return response()->failed('No slot available');
            }

            // Check in day_off
            $day_off = DayOff::checkSlotInDayOff($request, $day_number);
            if (!empty($day_off) && count($day_off) > 0) {
                return response()->failed('No slot available');
            }

            // Check in booking_slots
            $booking_slot = BookingSlot::checkSlotInBookingSlot($request);

            if (empty($booking_slot)) {
                $booking = BookingSlot::storeData($request);
                if ($booking) {
                    // Add personal details
                    BookingSlotPersonalDetail::storeData($request, $booking);
                    return response()->success('Reserved successfully');
                } else {
                    return response()->failed('Something went wrong. Try again later');
                }
            } else {

                return response()->failed('No slot available, reserved');

            }

        }


        return response()->internalServerError('Something went wrong.');

    }

    private function filterSlots($slots, $day_number) {
        return $slots
            ->where('day_number', $day_number)
            ->where('status', '1')
            ->all();
    }
}
