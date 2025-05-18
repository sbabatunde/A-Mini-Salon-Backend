<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class Settings extends Controller
{
    public function businessDetails(Request $req)
    { {

            // Validate the input
            $req->validate([
                'businessName' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|min:11',
                'address' => 'required|string|max:255',
                'googleMapAddress' => 'required|string',
                'facebook' => 'required|string|max:255',
                'instagram' => 'required|string|max:255',
                'x' => 'required',
                'linkedIn' => 'nullable',
            ]);

            // Create a new supplier in the database
            $setting = Setting::create($req->all());
            return response()->json([
                'success' => true,
                'message' => 'Your business details has been updated successfully.',
                'data' => $setting,
            ], 201);
        }
    }

    public function fetchBusinessDetails()
    {
        $bussinessInfo = Setting::orderBy('id', 'desc')->first();
        return response()->json([
            'success' => true,
            'data' => $bussinessInfo,
        ]);
    }
}
