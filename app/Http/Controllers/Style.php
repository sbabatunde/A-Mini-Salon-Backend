<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Styles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class Style extends Controller
{
    public function create(Request $request)
    {
        try {
            // Validate request
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:styles,name',
                'category' => 'required|string|max:255',
                'description' => 'required|string',
                'tag' => 'required|string|max:255',
                'image' => 'required|file|mimes:jpg,jpeg,png|max:2048', // validate, but don't save this field
            ]);

            unset($validatedData['image']); // remove the file from the data array

            // Upload image
            if ($request->file('image')) {
                $manager = new ImageManager(new Driver());

                // Generate a unique name using timestamp
                $name_gen = $request->name . '-' . time() . '.png'; // Always saving as JPEG

                $img = $manager->read($request->file('image'));
                $img = $img->resize(450, 450);

                $savePath = public_path('assets/styles');

                // Create the directory if it doesn't exist
                if (!File::exists($savePath)) {
                    File::makeDirectory($savePath, 0755, true);
                }

                // Save the image as JPEG
                $img->toJpeg(80)->save($savePath . '/' . $name_gen);

                $imagePath = '/assets/styles/' . $name_gen;
            }
            // Create style
            $style = Styles::create(array_merge($validatedData, [
                'image' => $imagePath, // this is your actual resized image path
            ]));
            return response()->json([
                'message' => 'Style created successfully.',
                'data' => $style,
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
                'message' => 'Style not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the style.' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show()
    {
        $styles = Styles::all();
        return response()->json([
            'success' => true,
            'data' => $styles,
        ]);
    }


    public function update(Request $request, $id)
    {
        try {
            $style = Styles::findOrFail($id);

            $input = array_filter($request->all(), fn($value) => $value !== null && $value !== '');
            $onlyStatusBeingUpdated = isset($input['status']) && count($input) === 1;

            if ($onlyStatusBeingUpdated) {
                // Handle status-only update
                $validated = $request->validate([
                    'status' => 'required|in:Active,Inactive',
                ]);

                $style->update($validated);
            } else {
                // Handle full update
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'category' => 'required|string|max:255',
                    'description' => 'required|string',
                    'tag' => 'required|string|max:255',
                    'image' => 'nullable|mimes:jpg,jpeg,png|max:2048',
                    'status' => 'nullable|in:Active,Inactive', // Optional status update
                ]);

                // Image processing
                $imagePath = $style->image; // default to existing image
                if ($request->hasFile('image')) {
                    $manager = new ImageManager(new Driver());

                    $name_gen = str_replace(' ', '-', $request->name) . '-' . time() . '.png';

                    $img = $manager->read($request->file('image'))->resize(450, 450);
                    $savePath = public_path('assets/styles');

                    if (!File::exists($savePath)) {
                        File::makeDirectory($savePath, 0755, true);
                    }

                    $img->toJpeg(80)->save($savePath . '/' . $name_gen);
                    $imagePath = '/assets/styles/' . $name_gen;
                }

                // Remove image from validated, then merge image path
                unset($validated['image']);
                $validated['image'] = $imagePath;

                // Update style
                $style->update($validated);
            }

            return response()->json([
                'success' => true,
                'message' => 'Style updated successfully!',
                'data' => $style,
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
                'message' => 'Style not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function delete($id)
    {
        try {
            $style = Styles::findOrFail($id);

            // Step 1: Delete the image file
            if ($style->image && File::exists(public_path($style->image))) {
                File::delete(public_path($style->image));
            }

            // Step 2: Delete the database record
            $style->delete();

            return response()->json([
                'message' => 'Style and associated image deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
