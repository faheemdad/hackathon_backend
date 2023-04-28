<?php

namespace Database\Seeders;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Service::insert([
            ['title' => 'Men Haircut', 'cleaning_minutes' => 5, 'allowed_bookings_serve' => 5, 'duration' => 10, 'created_at' => Carbon::now(),'updated_at' => Carbon::now()],
            ['title' => 'Women Haircut', 'cleaning_minutes' => 10, 'allowed_bookings_serve' => 3, 'duration' => 60, 'created_at' => Carbon::now(),'updated_at' => Carbon::now()],
        ]);
    }
}
