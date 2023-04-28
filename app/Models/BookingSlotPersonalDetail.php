<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSlotPersonalDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_slot_id',
        'first_name',
        'last_name',
        'email',
    ];

    public static function storeData($request, $booking){

        foreach ($request->personal_details as $detail) {
            $data = [

                'booking_slot_id'       => $booking->id,
                'first_name'          => $detail['first_name'],
                'last_name'          => $detail['last_name'],
                'email'          => $detail['email'],
            ];

            self::create($data);
        }

        return ;

    }
}
