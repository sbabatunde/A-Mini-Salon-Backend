<?php

namespace App\Http\Controllers;

use App\Models\Inventory as ModelsInventory;
use Illuminate\Http\Request;

class Inventory extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validate the input
            $validatedData = $request->validate([
                'product' => 'required|string|max:255|unique:inventories,product',
                'remark' => 'required|string|max:255',
                'unit' => 'required|string|max:20',
                'stock' => 'required|numeric',
                'acquiredOn' => 'required|date',
                'price' => 'required|numeric',
            ]);
            if ($validatedData['stock'] >= 5) {
                $validatedData['status'] = true;
            }

            // Update the appointment in the database
            $inventory = ModelsInventory::create($validatedData);
            return response()->json([
                'success' => true,
                'message' => 'New Inventory has been added successfully!',
                'data' => $inventory,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => $e->getMessage(), // You can remove this in production
            ], 500);
        }
    }

    public function show()
    {
        $inventory = ModelsInventory::all();
        return response()->json([
            'success' => true,
            'data' => $inventory,
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $inventory = ModelsInventory::findOrFail($id);

            // Validate the input
            $validatedData = $request->validate([
                'product' => 'required|string|max:255',
                'remark' => 'required|string|max:255',
                'unit' => 'required|string|max:20',
                'stock' => 'required|numeric',
                'acquiredOn' => 'required|date',
                'price' => 'required|numeric',
            ]);
            if ($validatedData['stock'] >= 5) {
                $validatedData['status'] = true;
            } else {
                $validatedData['status'] = false;
            }
            // Update the service
            $inventory->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Inventory updated successfully!',
                'data' => $inventory,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory not found.',
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
        $inventory = ModelsInventory::findOrFail($id);
        $inventory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Inventory deleted successfully.'
        ]);
    }
}
