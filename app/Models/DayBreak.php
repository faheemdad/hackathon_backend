<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayBreak extends Model
{
    use HasFactory;


    public static function checkSlotInDayBreak($request, $day_number) {

        return self::where('day_number', $day_number)
            ->where('service_id', $request->service_id)
            ->whereTime('start_time', '>=', $request->slots['start_time'])
            ->whereTime('end_time', '<=', $request->slots['end_time'])
            ->get();

    }
}
