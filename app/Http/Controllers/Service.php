<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Services;
use Illuminate\Http\Request;

class Service extends Controller
{
    public function newService(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric',
                'duration' => 'required|string',
                'icon' => 'required',
            ]);

            // Update the appointment in the database
            $services = Services::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Your service has been added successfully!',
                'data' => $services,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(), // You can remove this in production
            ], 500);
        }
    }

    public function listServices()
    {
        $services = Services::all();
        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    public function updateService(Request $request, $id)
    {
        try {
            $services = Services::findOrFail($id);

            // Check if only status is being updated
            if ($request->has('status') && count($request->all()) === 1) {
                $request->validate([
                    'status' => 'required|in:Active,Inactive',
                ]);
            } else {
                // Full update
                $request->validate([
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'price' => 'required|numeric',
                    'duration' => 'required|string',
                    'icon' => 'required',
                ]);
            }

            // Update the service
            $services->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully!',
                'data' => $services,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(), // Optional: remove in production
            ], 500);
        }
    }

    public function delete($id)
    {
        $service = Services::findOrFail($id);
        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.'
        ]);
    }
}
