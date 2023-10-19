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

class HotelController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $hotels = Hotel::all();
            $types = Type::all();
            $accommodations = Accommodation::all();
            $data = ['hotels' => $hotels,'types' => $types,'accommodations' => $accommodations];
            return SuccessResponseJson::successResponse($data,200);
        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse($th->getMessage(),500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|unique:hotels',
                'direction' => 'required|string',
                'city' => 'required|string',
                'nit' => 'required|string|unique:hotels',
                'number_rooms' => 'required|integer'
            ]);
            if ($validator->fails()) {
                return ErrorResponseJson::errorResponse($validator->errors(),400);
            }

            $hotel = Hotel::create($validator->validate());
            return SuccessResponseJson::successResponse($hotel,201);
        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse($th->getMessage(),500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $hotel = Hotel::find($id)->with('bedrooms')->get();
            if (!$hotel) {
                return ErrorResponseJson::errorResponse(__('validation.hotel_not_exist'),404);
            }
            return SuccessResponseJson::successResponse($hotel,200);
        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse( $th->getMessage(),500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $hotel = Hotel::findOrFail($id);
            $hotel->update($request->all());
            return SuccessResponseJson::successResponse($hotel,200);
        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse( $th->getMessage(),500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Hotel::destroy($id);
            $data = ['message' => 'Hotel eliminado'];
            return SuccessResponseJson::successResponse($data,200);
        } catch (\Throwable $th) {
            return ErrorResponseJson::errorResponse($th->getMessage(),500);
        }
    }

    function addRooms(Request $request, string $id)
    {
        try {

            $hotel = Hotel::findOrFail($id);
            if (!$hotel) return ErrorResponseJson::errorResponse(__('validation.hotel_not_exist'),404);

            $validateQuantity = $this->validateQuantity($hotel);
            if ($validateQuantity) return ErrorResponseJson::errorResponse(__('validation.limit_rooms'),400);

            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'type_id' => 'required|integer',
                'accommodation_id' => 'required|integer',
            ]);
            if ($validator->fails()) {
                if ($validateQuantity) return ErrorResponseJson::errorResponse($validator->errors(),400);
            }

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
