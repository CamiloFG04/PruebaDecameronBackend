<?php

namespace App\Http\Controllers;

use App\Helpers\ErrorResponseJson;
use App\Helpers\SuccessResponseJson;
use App\Models\Accommodation;
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
            $hotels = Hotel::with('bedrooms')->get();
            $types = Type::all();
            $accommodations = Accommodation::all();
            foreach ($hotels as $hotel) {
                $roomNumber = 0;
                foreach ($hotel->bedrooms as $bedroom) {
                    $roomNumber += $bedroom->quantity;
                }
                $hotel->quantity = $roomNumber;
            }
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
                $messageErrors = collect($validator->errors()->all())->implode("\n");
                return ErrorResponseJson::errorResponse($messageErrors,400);
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
}
