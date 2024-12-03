<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transportation;
use Illuminate\Support\Facades\Log;
use Exception;

class TransportationController extends Controller
{
    public function transportation(Request $request)
    {
        try {
            // Validate the incoming data
            $request->validate([
                'destination_id' => 'required|exists:destinations,id',
                'company_name' => 'required|string|max:255',
                'company_document' => 'nullable|file|mimes:jpeg,jpg,png,doc,docx,pdf', // Valid file types for the document
                'email' => 'required|email|unique:transportations,email',
                'contact_no' => 'required|string|max:15',
                'address' => 'required|string|max:255',
                'mode_of_transportation' => 'required|string|max:100',
                'vehicle_type' => 'required|string|max:100',
                'options' => 'required|array', // Ensure options is an array
                'options.*.type' => 'required|string|max:255', // Validate each option type
                'options.*.rate' => 'required|numeric|min:0', // Validate rate for each option
            ]);
    
            // Handle document upload if provided
            $documentPath = null;
            if ($request->hasFile('company_document')) {
                try {
                    // Generate a unique name for the uploaded document
                    $documentPath = 'tcompany_document/' . time() . '_' . $request->file('company_document')->getClientOriginalName();
                    $request->file('company_document')->move(public_path('tcompany_document'), $documentPath);
                } catch (\Exception $e) {
                    // Log any file upload errors
                    Log::error('File upload failed: ' . $e->getMessage(), [
                        'file_name' => $request->file('company_document')->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                    return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
                }
            }
    
            // Process 'options' field to match the desired format
            $processedOptions = [];
            foreach ($request->input('options') as $option) {
                $processedOptions[] = [
                    'option' => $option['type'],  // Map 'type' to 'option'
                    'rate' => $option['rate'],    // Keep the 'rate'
                ];
            }
            Log::info('Processed Options:', [
                'processed_options' => $processedOptions
            ]);
            // Create the transportation entry
            $transportation = Transportation::create([
                'destination_id' => $request->destination_id,
                'company_name' => $request->company_name,
                'company_document' => $documentPath, // Store the file path
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'mode_of_transportation' => $request->mode_of_transportation,
                'vehicle_type' => $request->vehicle_type,
                'options' => json_encode($processedOptions), // Store options as a JSON string
            ]);
    
            return response()->json($transportation, 201);
        } catch (\Exception $e) {
            // Log the error and the request data for debugging
            Log::error('Transportation creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Failed to create transportation: ' . $e->getMessage()], 500);
        }
    }
    

    public function showtransportation($id)
    {
        $transportation = Transportation::with('destination')->findOrFail($id);
        return response()->json($transportation);
    }
    

    public function destroy($id)
    {
        $transportation = Transportation::findOrFail($id);

        // Delete associated files if needed
        if ($transportation->company_document && file_exists(public_path($transportation->company_document))) {
            unlink(public_path($transportation->company_document));
        }
        
        // Delete the record
        $transportation->delete();

        return response()->json(['success' => true, 'message' => 'Transportation deleted successfully'], 200);
    }
    public function updatetransportation(Request $request, $id)
    {
        try {
            // Validate the incoming data
            $request->validate([
                'destination_id' => 'required|exists:destinations,id',
                'company_name' => 'required|string|max:255',
                'company_document' => 'nullable|file|mimes:jpeg,jpg,png,doc,docx,pdf', // Valid file types for the document
                'email' => 'required|email|unique:transportations,email,' . $id, // Ensure email is unique except for this record
                'contact_no' => 'required|string|max:15',
                'address' => 'required|string|max:255',
                'mode_of_transportation' => 'required|string|max:100',
                'vehicle_type' => 'required|string|max:100',
                'options' => 'required|array', // Ensure options is an array
                'options.*.type' => 'required|string|max:255', // Validate each option type
                'options.*.rate' => 'required|numeric|min:0', // Validate rate for each option
            ]);
        
            // Find the transportation entry to update
            $transportation = Transportation::findOrFail($id);
        
            // Handle document upload if provided
            $documentPath = $transportation->company_document; // Keep the existing document if no new one is uploaded
            if ($request->hasFile('company_document')) {
                try {
                    // Generate a unique name for the uploaded document
                    $documentPath = 'tcompany_document/' . time() . '_' . $request->file('company_document')->getClientOriginalName();
                    $request->file('company_document')->move(public_path('tcompany_document'), $documentPath);
                } catch (\Exception $e) {
                    // Log any file upload errors
                    Log::error('File upload failed: ' . $e->getMessage(), [
                        'file_name' => $request->file('company_document')->getClientOriginalName(),
                        'error' => $e->getMessage(),
                    ]);
                    return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
                }
            }
        
            // Process 'options' field to match the desired format
            $processedOptions = [];
            foreach ($request->input('options') as $option) {
                $processedOptions[] = [
                    'option' => $option['type'],  // Map 'type' to 'option'
                    'rate' => $option['rate'],    // Keep the 'rate'
                ];
            }
            Log::info('Processed Options:', [
                'processed_options' => $processedOptions
            ]);
        
            // Update the transportation entry
            $transportation->update([
                'destination_id' => $request->destination_id,
                'company_name' => $request->company_name,
                'company_document' => $documentPath, // Store the file path (updated if a new document was uploaded)
                'email' => $request->email,
                'contact_no' => $request->contact_no,
                'address' => $request->address,
                'mode_of_transportation' => $request->mode_of_transportation,
                'vehicle_type' => $request->vehicle_type,
                'options' => json_encode($processedOptions), // Store options as a JSON string
            ]);
        
            return response()->json($transportation, 200); // Return the updated transportation
        } catch (\Exception $e) {
            // Log the error and the request data for debugging
            Log::error('Transportation update failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Failed to update transportation: ' . $e->getMessage()], 500);
        }
    }
    
}
