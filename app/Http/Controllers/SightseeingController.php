<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\Sightseeing;

class SightseeingController extends Controller
{
    public function postSightseeing(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'destination_id' => 'required|exists:destinations,id', // Ensure destination_id exists in destinations table
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'scompany_document' =>  'required|file|mimes:jpeg,jpg,png,doc,docx,pdf',
            'contact_no' => 'required|string|max:15',
            'email' => 'required|email|unique:sightseeings,email',
            'description' => 'nullable|string',
            's_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'rate_adult' => 'required|numeric|min:0',
            'rate_child' => 'required|numeric|min:0',
        ]);

        if ($request->file('scompany_document')) {
            try {
                $sDocumentsPath = 'scompany_document/' . time() . '_' . $request->file('scompany_document')->getClientOriginalName();
                $request->file('scompany_document')->move(public_path('scompany_document'), $sDocumentsPath);
            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
            }
        }
    
        // Handle company logo upload
        if ($request->file('s_pic')) {
            try {
                $scompanypic= 's_pic/' . time() . '_' . $request->file('s_pic')->getClientOriginalName();
                $request->file('s_pic')->move(public_path('s_pic'), $scompanypic);
            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
            }
        }
    


        // Create the sightseeing option
        $sightseeing = Sightseeing::create([
            'destination_id' => $request->destination_id,
            'company_name' => $request->company_name,
            'scompany_document' => $sDocumentsPath,
            'contact_no' => $request->contact_no,
            'address'=> $request->address,
            'email' => $request->email,
            'description' => $request->description,
            's_pic' => $scompanypic,
            'rate_adult' => $request->rate_adult,
            'rate_child' => $request->rate_child,
        ]);

        // Return the created sightseeing option in response
        return response()->json($sightseeing, 201);
    }

    public function showsightseeing($id)
    {
        $sightseeing = Sightseeing::with('destination')->findOrFail($id);
        return response()->json($sightseeing);
    }
    public function updatesightseeing(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'destination_id' => 'required|exists:destinations,id',
            'company_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'scompany_document' => 'nullable|file|mimes:jpeg,jpg,png,doc,docx,pdf',
            'contact_no' => 'required|string|max:15',
            'email' => 'required|email|unique:sightseeings,email,' . $id,
            'description' => 'nullable|string',
            's_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'rate_adult' => 'required|numeric|min:0',
            'rate_child' => 'required|numeric|min:0',
        ]);
    
        // Find the sightseeing record
        $sightseeing = Sightseeing::findOrFail($id);
    
        // Handle document upload if provided
        if ($request->hasFile('scompany_document')) {
            // Delete the old document file if exists
            if ($sightseeing->scompany_document && file_exists(public_path($sightseeing->scompany_document))) {
                unlink(public_path($sightseeing->scompany_document));
            }
    
            $sDocumentsPath = 'scompany_document/' . time() . '_' . $request->file('scompany_document')->getClientOriginalName();
            $request->file('scompany_document')->move(public_path('scompany_document'), $sDocumentsPath);
            $sightseeing->scompany_document = $sDocumentsPath;
        }
    
        // Handle company logo upload if provided
        if ($request->hasFile('s_pic')) {
            // Delete the old picture file if exists
            if ($sightseeing->s_pic && file_exists(public_path($sightseeing->s_pic))) {
                unlink(public_path($sightseeing->s_pic));
            }
    
            $scompanypic = 's_pic/' . time() . '_' . $request->file('s_pic')->getClientOriginalName();
            $request->file('s_pic')->move(public_path('s_pic'), $scompanypic);
            $sightseeing->s_pic = $scompanypic;
        }
    
        // Update the remaining attributes
        $sightseeing->destination_id = $request->destination_id;
        $sightseeing->company_name = $request->company_name;
        $sightseeing->contact_no = $request->contact_no;
        $sightseeing->address = $request->address;
        $sightseeing->email = $request->email;
        $sightseeing->description = $request->description;
        $sightseeing->rate_adult = $request->rate_adult;
        $sightseeing->rate_child = $request->rate_child;
    
        // Save the changes
        $sightseeing->save();
    
        return response()->json(['success' => true, 'message' => 'Sightseeing updated successfully'], 200);
    }
    
    
    public function destroy($id)
{
    $sightseeing = Sightseeing::findOrFail($id);

    // Optionally delete associated files if needed
    if (file_exists(public_path($sightseeing->scompany_document))) {
        unlink(public_path($sightseeing->scompany_document));
    }

    if (file_exists(public_path($sightseeing->s_pic))) {
        unlink(public_path($sightseeing->s_pic));
    }

    // Delete the record
    $sightseeing->delete();

    return response()->json(['success' => true, 'message' => 'Sightseeing deleted successfully'], 200);
}



}
