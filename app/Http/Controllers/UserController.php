<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Wallet as ModelsWallet;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function register(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'username' => 'required|string|max:25',
            'company_name' => 'required|string|max:50',
            'phoneno' => 'required|string|max:15',
            'address' => 'required|string|max:250',
            'company_documents' => 'required|file|mimes:jpeg,jpg,png,doc,docx,pdf',
            'company_logo' => 'required|file|mimes:jpeg,jpg,png',
            'reffered_by' => 'nullable|string|max:25',
            'role' => 'required|string|max:25',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);
    
        // Handle file uploads
        if ($request->file('company_documents')) {
            try {
                $companyDocumentsPath = 'company_documents/' . time() . '_' . $request->file('company_documents')->getClientOriginalName();
                $request->file('company_documents')->move(public_path('company_documents'), $companyDocumentsPath);
            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
            }
        }
    
        if ($request->file('company_logo')) {
            try {
                $companyLogoPath = 'company_logos/' . time() . '_' . $request->file('company_logo')->getClientOriginalName();
                $request->file('company_logo')->move(public_path('company_logos'), $companyLogoPath);
            } catch (\Exception $e) {
                return response()->json(['error' => 'File upload error: ' . $e->getMessage()], 500);
            }
        }
    
        // Create a new user
        $user = User::create([
            'username' => $request->username,
            'company_name' => $request->company_name,
            'phoneno' => $request->phoneno,
            'address' => $request->address,
            'company_documents' => $companyDocumentsPath,
            'company_logo' => $companyLogoPath,
            'reffered_by' => $request->reffered_by,
            'role' => $request->role,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // Check if the user type is "admin" or "agent" and create a wallet with zero balance
        if (in_array($request->role, ['agent'])) {
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00, // initial balance set to zero
            ]);
        }
    
        // Create a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Return a success response with user data and token
        return response()->json([
            'message' => 'Registration successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'company_name' => $user->company_name,
                'email' => $user->email,
                // Add any other user fields you want to return
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Check if user exists and password is correct
        $user = User::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }
    
        // Delete any existing tokens for this user
        $user->tokens()->delete();
    
        // Generate a new token for the user
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Return the token in the response
        return response()->json([
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                // Include other fields if needed
            ],
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 200);
    }
    
    public function showdetailsuser ($id){
        $show = User::with('wallet')->findOrFail($id);
        return response()->json($show);
    }


    public function logout(Request $request)
{
    // Ensure the user is authenticated
    $user = $request->user();

    if ($user) {
        // Revoke all tokens issued to the user
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ], 200);
    }

    return response()->json([
        'message' => 'No authenticated user found'
    ], 400);
}

public function changePassword(Request $request)
{  
    $request->validate([
        'current_password' => 'required|string|min:8',
        'new_password' => 'required|string|min:8|confirmed',
    ]);

    $user = $request->user(); // Get the authenticated user

    // Check if the current password is correct
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => 'Current password is incorrect'], 400);
    }

    // Update the password
    $user->update([
        'password' => Hash::make($request->new_password),
    ]);

    return response()->json(['message' => 'Password updated successfully'], 200);
}



}
 