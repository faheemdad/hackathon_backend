<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateOff extends Model
{
    use HasFactory;

    protected $table = "dates_off";

    public static function checkSlotInDateOff($request) {

        return self::where('date', '=', $request->date)
            ->where('service_id', $request->service_id)
            ->where('day_status', 'OFF')
            ->get();

    }
}
