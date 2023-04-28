<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DaySlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->menHariCutService();
        $this->womenHariCutService();
    }


    private function menHariCutService() {
        $service = Service::where('id', '1')->where('status', '1')->first();
        if (!empty($service)) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $dayNumber = 1; // Monday
            for ($i = 1; $i <= 7; $i++) {
                if ($dayNumber == 7) {
                    $dayNumber = 1; // Sunday
                    continue;
                }

                // Define start and end time
                $startTime = ($days[$i] == 'Saturday') ? '10:00:00' : '08:00:00';
                $endTime = ($days[$i] == 'Saturday') ? '22:00:00' : '20:00:00';
                for ($time = strtotime($startTime); $time < strtotime($endTime); $time += ($service->duration) + $service->cleaning_minutes) { // every 10 minutes

                    $start = date('H:i:s', $time);
                    $end = date('H:i:s', $time + ($service->duration) + $service->cleaning_minutes); // 10 minutes duration

                    // Create slot
                    DB::table('day_slots')->insert([
                        'service_id' => $service->id,
                        'day_number' => $dayNumber,
                        'start_time' => $start,
                        'end_time' => $end,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }


                DB::table('day_breaks')->insert([
                    'service_id' => $service->id,
                    'title' => 'Launch Break',
                    'day_number' => $dayNumber,
                    'start_time' => '12:00:00',
                    'end_time' => '13:00:00',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('day_breaks')->insert([
                    'service_id' => $service->id,
                    'title' => 'Cleaning Break',
                    'day_number' => $dayNumber,
                    'start_time' => '15:00:00',
                    'end_time' => '16:00:00',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $dayNumber++;
            }

            // Add day off for the third day starting from now
            $holidayDate = date('Y-m-d', strtotime('+3 days'));
            $dayOfWeek = date('l', strtotime($holidayDate));
            if ($dayOfWeek != 'Sunday') { // Don't add day off if it's Sunday
                DB::table('days_off')->insert([
                    'service_id' => $service->id,
                    'title' => 'Public Holiday',
                    'day_number' => date('N', strtotime($holidayDate)),
                    'day_status' => 'OFF',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


//            Log::info('dd', ['today' => $today, 'num' => '$endOfWeek']);

        }
    }

    private function womenHariCutService() {
        $service = Service::where('id', '2')->where('status', '1')->first();
        if (!empty($service)) {
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $dayNumber = 1; // Monday
            for ($i = 1; $i <= 7; $i++) {
                if ($dayNumber == 7) {
                    $dayNumber = 1; // Sunday
                    continue;
                }

                // Define start and end time
                $startTime = ($days[$i] == 'Saturday') ? '10:00:00' : '08:00:00';
                $endTime = ($days[$i] == 'Saturday') ? '22:00:00' : '20:00:00';
                for ($time = strtotime($startTime); $time < strtotime($endTime); $time += ($service->duration) + $service->cleaning_minutes) { // every 10 minutes

                    $start = date('H:i:s', $time);
                    $end = date('H:i:s', $time + ($service->duration) + $service->cleaning_minutes); // 10 minutes duration

                    // Create slot
                    DB::table('day_slots')->insert([
                        'service_id' => $service->id,
                        'day_number' => $dayNumber,
                        'start_time' => $start,
                        'end_time' => $end,
                        'status' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('day_breaks')->insert([
                    'service_id' => $service->id,
                    'title' => 'Launch Break',
                    'day_number' => $dayNumber,
                    'start_time' => '12:00:00',
                    'end_time' => '13:00:00',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                DB::table('day_breaks')->insert([
                    'service_id' => $service->id,
                    'title' => 'Cleaning Break',
                    'day_number' => $dayNumber,
                    'start_time' => '15:00:00',
                    'end_time' => '16:00:00',
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $dayNumber++;
            }

            // Add day off for the third day starting from now
            $holidayDate = date('Y-m-d', strtotime('+3 days'));
            $dayOfWeek = date('l', strtotime($holidayDate));
            if ($dayOfWeek != 'Sunday') { // Don't add day off if it's Sunday
                DB::table('days_off')->insert([
                    'service_id' => $service->id,
                    'title' => 'Public Holiday',
                    'day_number' => date('N', strtotime($holidayDate)),
                    'day_status' => 'OFF',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


//            Log::info('dd', ['today' => $today, 'num' => '$endOfWeek']);

        }
    }
}
