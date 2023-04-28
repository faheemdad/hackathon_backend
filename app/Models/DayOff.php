<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayOff extends Model
{
    use HasFactory;

    protected $table = "days_off";
    public static function checkSlotInDayOff($request, $day_number) {

        return self::where('day_number',$day_number)
            ->where('service_id', $request->service_id)
            ->where('day_status', 'OFF')
            ->get();

    }
}
