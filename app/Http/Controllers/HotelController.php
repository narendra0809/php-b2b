<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;

class HotelController extends Controller
{
    // Store a new hotel with destination_id
    public function hotel(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'hotel_type' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'tariff' => 'required|numeric',
            'destination_id' => 'required|exists:destinations,id', // Ensure destination_id exists in destinations table
            'room_types' => 'sometimes|array', // Validate as array
            'room_types.*.type' => 'required|string|max:100', // Each type must be a string
            'room_types.*.rate' => 'required|numeric', // Each rate must be numeric
            'meals' => 'sometimes|array', // Validate as array
            'meals.*.name' => 'required|string|max:255', // Each meal must have a name
            'meals.*.rate' => 'required|numeric', // Each meal must have a rate
        ]);

        // Create the hotel
        $hotel = Hotel::create([
            'name' => $request->name,
            'hotel_type' => $request->hotel_type,
            'address' => $request->address,
            'contact_no' => $request->contact_no,
            'tariff' => $request->tariff,
            'destination_id' => $request->destination_id,
            'room_types' => $request->room_types, // Add room_types JSON
            'meals' => $request->meals, // Add meals JSON
        ]);

        // Return the created hotel in response
        return response()->json($hotel, 201);
    }

    // Show a specific hotel with its destination
    public function show($id)
    {
        $hotel = Hotel::with('destination')->findOrFail($id);
        return response()->json($hotel);
    }

    // Update an existing hotel
    public function updatehotel(Request $request, $id)
    {
        // Find hotel by ID, or fail
        $hotel = Hotel::findOrFail($id);

        // Validate the incoming data
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'hotel_type' => 'sometimes|string|max:100',
            'address' => 'sometimes|string|max:255',
            'contact_no' => 'sometimes|string|max:20',
            'tariff' => 'sometimes|numeric',
            'destination_id' => 'sometimes|exists:destinations,id',
            'room_types' => 'sometimes|array', // Validate as array
            'room_types.*.type' => 'required|string|max:100', // Each type must be a string
            'room_types.*.rate' => 'required|numeric', // Each rate must be numeric
            'meals' => 'sometimes|array', // Validate as array
            'meals.*.name' => 'required|string|max:255', // Each meal must have a name
            'meals.*.rate' => 'required|numeric', // Each meal must have a rate
        ]);

        // Update only the provided fields
        $hotel->update($request->only([
            'name',
            'hotel_type',
            'address',
            'contact_no',
            'tariff',
            'destination_id',
            'room_types',
            'meals',
        ]));

        // Return the updated hotel
        return response()->json($hotel);
    }

    // Delete a hotel
    public function destroy($id)
    {
        // Find hotel by ID, or fail
        $hotel = Hotel::findOrFail($id);

        // Delete the hotel
        $hotel->delete();

        // Return a response indicating successful deletion
        return response()->json(['message' => 'Hotel deleted successfully'], 200);
    }
}
