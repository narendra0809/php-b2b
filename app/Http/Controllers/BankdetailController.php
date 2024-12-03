<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\BankDetail;
use Illuminate\Support\Facades\Auth;

class BankdetailController extends Controller
{
    public function bankdetail(Request $request)
    {
     
        $request->validate([
            'account_details' => 'required|string|max:255',
            'upi_id' => 'nullable|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'ifsc_code' => 'required|string|max:11',
            'account_type' => 'required|in:savings,current,salary',
            'branch' => 'required|string|max:255',
            'status' => 'required|in:online,offline',
        ]);
        // $user = Auth::user();
        $bankDetail = BankDetail::create([
            // 'user_id' => $user->id,
            'user_id' => $request->user_id,
            'account_details' => $request->account_details,
            'upi_id' => $request->upi_id,
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'ifsc_code' => $request->ifsc_code,
            'account_type' => $request->account_type,
            'branch' => $request->branch,
            'status' => $request->status,
        ]);
    
             return response()->json(['meesage'=>'saved','data'=>$bankDetail], 201);
}

public function showbankdetail($id)
{
    $bankDetail = BankDetail::with('user')->findOrFail($id);
    
    return response()->json($bankDetail);
}

public function updatebankdetail(Request $request, $id)
{
    // Validate incoming data
    $request->validate([
        'account_details' => 'nullable|string|max:255',
        'upi_id' => 'nullable|string|max:255',
        'bank_name' => 'nullable|string|max:255',
        'account_holder_name' => 'nullable|string|max:255',
        'ifsc_code' => 'nullable|string|max:11',
        'account_type' => 'nullable|in:savings,current,salary',
        'branch' => 'nullable|string|max:255',
        'status' => 'nullable|in:online,offline',
    ]);

    // Find the bank detail entry by id
    $bankDetail = BankDetail::find($id);

    // If not found, return an error
    if (!$bankDetail) {
        return response()->json(['message' => 'Bank detail not found'], 404);
    }

    // Update fields if provided in the request
    $bankDetail->update($request->only([
        'account_details',
        'upi_id',
        'bank_name',
        'account_holder_name',
        'ifsc_code',
        'account_type',
        'branch',
        'status',
    ]));

    return response()->json(['message' => 'Bank detail updated successfully', 'data' => $bankDetail], 200);
}

public function delete($id){
    $bankdetail = BankDetail::findorfail($id);
    $bankdetail->delete();

    return response ()->json(['message' => 'Bankdetails deleted successfully'], 200);
}

}
   
