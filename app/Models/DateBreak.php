<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DateBreak extends Model
{
    use HasFactory;


    public static function checkSlotInDateBreak($request) {

        return self::
            whereBetween('date', [$request->date, $request->date])
            ->where('service_id', $request->service_id)
            ->whereTime('start_time', '>=', $request->slots['start_time'])
            ->whereTime('end_time', '<=', $request->slots['end_time'])
            ->get();

    }
}
