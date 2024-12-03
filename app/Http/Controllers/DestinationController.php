<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Destination;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DestinationController extends Controller
{
    public function setDestination(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'status' => 'required|in:online,offline', // Only 'online' or 'offline' allowed
        ]);

        $destination = Destination::updateOrCreate(
    // Assuming you have user authentication and want one destination per user
            [
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_id' => $request->city_id,
                'status' => $request->status,
            ]
        );

        return response()->json([
            'message' => 'Destination set successfully',
            'destination' => $destination,
        ], 200);
    }

    // Get the current destination
    public function getDestination()
{
    $destinations = Destination::with(['country', 'state', 'city'])->get();

    if ($destinations->isEmpty()) {
        return response()->json([
            'message' => 'No destination set',
        ], 404);
    }

    // Transform the response to include only specific data
    $data = $destinations->map(function ($destination) {
        return [
            'id' => $destination->id,
            'country' => $destination->country->name ?? null,
            'state' => $destination->state->name ?? null,
            'city' => $destination->city->name ?? null,
            'status' => $destination->status,
            'created_at' => $destination->created_at,
        ];
    });

    return response()->json([
        'destinations' => $data,
    ], 200);
}


    // Update the online/offline status of the destination
    public function updateStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|in:online,offline',
        ]);

        $destination = Destination::where('user_id', auth()->id)->first();

        if (!$destination) {
            return response()->json([
                'error' => 'Destination not found',
            ], 404);
        }

        $destination->status = $request->status;
        $destination->save();

        return response()->json([
            'message' => 'Status updated successfully',
            'destination' => $destination,
        ], 200);
    }

    // Fetch all countries
    public function getCountries()
    {
        $countries = Country::all();

        return response()->json([
            'countries' => $countries,
        ], 200);
    }

    // Fetch states based on country_id
    public function getStates($country_id)
    {
        $states = State::where('country_id', $country_id)->get();

        return response()->json([
            'states' => $states,
        ], 200);
    }

    // Fetch cities based on state_id
    public function getCities($state_id)
    {
        $cities = City::where('state_id', $state_id)->get();

        return response()->json([
            'cities' => $cities,
        ], 200);
    }
}
