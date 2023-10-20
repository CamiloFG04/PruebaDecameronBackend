<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorResponseJson;
use App\Helpers\SuccessResponseJson;
use App\Models\Accommodation;
use App\Models\Bedroom;
use App\Models\Hotel;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BedroomController extends Controller
{
    function addRooms(Request $request, string $id)
    {
        try {

            $hotel = Hotel::findOrFail($id);
            if (!$hotel) return ErrorResponseJson::errorResponse(__('validation.hotel_not_exist'),404);

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'type_id' => 'required|integer',
                'accommodation_id' => 'required|integer',
            ]);

            if ($validator->fails()){
                $messageErrors = collect($validator->errors()->all())->implode("\n");
                return ErrorResponseJson::errorResponse($messageErrors,400);
            }

            $validateAssignmentRooms = $this->validateAssignmentRooms($hotel,$request->type_id,$request->accommodation_id);
            if (!$validateAssignmentRooms) return ErrorResponseJson::errorResponse(__('validation.assignment_rooms'),400);

            $validateQuantity = $this->validateQuantity($hotel);
            if ($validateQuantity) return ErrorResponseJson::errorResponse(__('validation.limit_rooms'),400);

            $existType = $this->existType($request->type_id);

            if (!$existType) return ErrorResponseJson::errorResponse(__('validation.room_type_not_exist'),404);

            $existAccommodation = $this->existAccommodation($request->accommodation_id);
            if (!$existAccommodation) return ErrorResponseJson::errorResponse(__('validation.accommodation_not_exist'),404);

            $totalRooms = $this->totalRooms($hotel,$request->quantity);
            if ($totalRooms) return ErrorResponseJson::errorResponse(__('validation.quantity_over_capacity'),400);


            $validateRooms = $this->validateRooms($hotel,$request->type_id,$request->accommodation_id);
            if ($validateRooms) return ErrorResponseJson::errorResponse(__('validation.rooms_t_a_exist'),400);

            $room = Bedroom::create(array_merge(
                $validator->validate(),
                ['hotel_id' => $id]
            ));

            return SuccessResponseJson::successResponse($room,201);

        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse( $th->getMessage(),500);
        }
    }

    public function showRooms($id) {
        try {
            $rooms = Bedroom::with('type', 'accommodation')
            ->where('hotel_id', $id)
            ->get();
            foreach ($rooms as $room) {
                $room->typeName = $room->type->name;
                $room->accommodationName = $room->accommodation->name;

            }
            return SuccessResponseJson::successResponse($rooms,200);
        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse( $th->getMessage(),500);
        }
    }

    protected function validateAssignmentRooms(Hotel $hotel, string $type_id, string $accommodation_id) {
        $validCombinations = [
            '1' => ['1', '2'],
            '2' => ['3', '4'],
            '3' => ['1', '2', '3'],
        ];

        if (array_key_exists($type_id, $validCombinations) && in_array($accommodation_id, $validCombinations[$type_id])) {
            return true;
        }

        return false;
    }

    protected function validateQuantity(Hotel $hotel)
    {
        $totalRooms = $hotel->number_rooms;
        $existingRoom = Bedroom::where('hotel_id',$hotel->id)->sum('quantity');
        if ($existingRoom >= $totalRooms) {
            return true;
        }
        return false;
    }

    protected function existType(string $type_id)
    {
        $type = Type::find($type_id);
        if (!$type) {
            return false;
        }
        return true;
    }

    protected function existAccommodation(string $accommodation_id)
    {
        $type = Accommodation::find($accommodation_id);
        if (!$type) {
            return false;
        }
        return true;
    }

    protected function totalRooms(Hotel $hotel,int $quantity)
    {
        $existingRooms = Bedroom::where('hotel_id',$hotel->id)->sum('quantity');
        $totalRooms = $existingRooms + $quantity;
        if ($totalRooms > $hotel->number_rooms) {
            return true;
        }
        return false;
    }

    protected function validateRooms(Hotel $hotel,string $type_id,string $accommodation_id)
    {
        $existRoom = Bedroom::where([
            ['hotel_id', '=', $hotel->id],
            ['type_id', '=', $type_id],
            ['accommodation_id', '=', $accommodation_id]
        ])->get();
        if (count($existRoom) > 0) {
            return true;
        }
        return false;
    }
}
