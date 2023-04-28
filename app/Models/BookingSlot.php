<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'slot_date',
        'start_time',
        'end_time',
        'slot_status',
    ];

    public static function storeData($request){

        $data = [

            'service_id'       => $request->service_id,
            'slot_date'          => $request->date,
            'start_time'            => $request->slots['start_time'],
            'end_time'     => $request->slots['end_time'],
            'slot_status'            => "RESERVED"
        ];

        return self::create($data);

    }


    public static function checkSlotInBookingSlot($request) {

        return self::with('personalDetail')
            ->where('service_id', $request->service_id)
            ->whereTime('start_time', '>=', $request->slots['start_time'])
            ->whereTime('end_time', '<=', $request->slots['end_time'])
            ->whereDate('slot_date', '=', $request->date)
            ->where('slot_status', 'RESERVED')
            ->first();

    }

    public function personalDetail() {
        return $this->hasMany(BookingSlotPersonalDetail::class, 'booking_sLot_id', 'id');
    }
}
