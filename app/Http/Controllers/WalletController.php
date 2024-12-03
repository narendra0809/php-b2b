<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\User;

use Illuminate\support\Facades\Auth;


use Illuminate\Http\Request;

class WalletController extends Controller
{
public function showBalance($userId)
{
    // Find the wallet for the given user ID
    $wallet = Wallet::where('user_id', $userId)->with('user')->first();

    // Check if a wallet exists
    if (!$wallet) {
        return response()->json(['error' => 'Wallet not found'], 404);
    }

    return response()->json([
        'id' => $wallet->id,
        'user_id' => $wallet->user->id,
        'username' => $wallet->user->username,
        'balance' => $wallet->balance,
    ]);
}

public function updateWalletBalance(Request $request, $agentId)
{
    // Validate the input
    $request->validate([
        'amount' => 'required|numeric|min:0.01',
        'action' => 'required|in:add,subtract',
    ]);

    // Check if the authenticated user is an admin
    $admin = Auth::user();
    if (!$admin || $admin->role !== 'admin') {
        return response()->json(['error' => 'Unauthorized: Only admins can update wallet balances'], 403);
    }

    // Retrieve the target user and ensure they are an agent
    $agent = User::where('id', $agentId)->where('role', 'agent')->first();

    if (!$agent) {
        return response()->json(['error' => 'Agent not found or user is not an agent'], 404);
    }

    // Retrieve the agent's wallet
    $wallet = Wallet::where('user_id', $agentId)->first();

    if (!$wallet) {
        return response()->json(['error' => 'Wallet not found for the specified agent'], 404);
    }

    // Perform the balance update
    if ($request->action === 'add') {
        $wallet->balance += $request->amount;
    } elseif ($request->action === 'subtract') {
        if ($wallet->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance in wallet'], 400);
        }
        $wallet->balance -= $request->amount;
    }

    // Save the updated wallet
    $wallet->save();

    return response()->json([
        'message' => 'Agent wallet balance updated successfully',
        'wallet' => $wallet,
    ]);
}


}
