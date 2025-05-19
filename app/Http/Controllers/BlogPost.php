<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\BlogRequest;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BlogPost extends Controller
{
    public function create(BlogRequest $request)
    {
        try {
            $blog = new Blog();
            $blog->title = $request->title;
            $blog->slug = Str::slug($request->title);
            $blog->content = $request->content;
            $blog->tag = $request->tag;
            unset($request->image); // remove the file from the data array
            // Upload image
            if ($request->file('image')) {
                $manager = new ImageManager(new Driver());
                // Limit title to 30 characters and sanitize it, so that it will save because if it's too long it won't
                $shortTitle = Str::slug(Str::limit($request->title, 30));
                // Generate a unique name using timestamp
                $name_gen = $shortTitle . '-' . time() . '.png'; // Always saving as JPEG

                $img = $manager->read($request->file('image'));
                $img = $img->resize(450, 450);

                $savePath = public_path('assets/blogs');

                // Create the directory if it doesn't exist
                if (!File::exists($savePath)) {
                    File::makeDirectory($savePath, 0755, true);
                }

                // Save the image as JPEG
                $img->toJpeg(80)->save($savePath . '/' . $name_gen);

                $imagePath = '/assets/blogs/' . $name_gen;
            }
            $blog->image = $imagePath; // this is your actual resized image path

            $blog->save();

            return response()->json(['message' => 'Blog created successfully', 'data' => $blog]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to create blog' . $e->getMessage(), 'error' => $e->getMessage()], 500);
        }
    }

    public function update(BlogRequest $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);

            $input = array_filter($request->all(), fn($value) => $value !== null && $value !== '');
            $onlyStatusBeingUpdated = isset($input['status']) && count($input) === 1;

            if ($onlyStatusBeingUpdated) {
                // Handle status-only update
                $validated = $request->validate([
                    'status' => 'required|in:Active,Inactive',
                ]);

                $blog->update($validated);
            } else {
                $blog->title = $request->title;
                $blog->slug = Str::slug($request->title);
                $blog->content = $request->content;
                $blog->tag = $request->tag;

                // Upload image
                if ($request->file('image')) {
                    $manager = new ImageManager(new Driver());
                    $shortTitle = Str::slug(Str::limit($request->title, 30));
                    // Generate a unique name using timestamp
                    $name_gen = $shortTitle . '-' . time() . '.png'; // Always saving as JPEG

                    $img = $manager->read($request->file('image'));
                    $img = $img->resize(450, 450);

                    $savePath = public_path('assets/blogs');

                    // Create the directory if it doesn't exist
                    if (!File::exists($savePath)) {
                        File::makeDirectory($savePath, 0755, true);
                    }

                    // Save the image as JPEG
                    $img->toJpeg(80)->save($savePath . '/' . $name_gen);

                    $imagePath = '/assets/blogs/' . $name_gen;
                    $blog->image = $imagePath; // this is your actual resized image path
                }

                $blog->save();
            }

            return response()->json(['success' => true, 'message' => 'Blog updated successfully', 'data' => $blog]);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Failed to update blog', 'error' => $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $blogs = Blog::where('status', 'Active')->get();
        return response()->json([
            'success' => true,
            'data' => $blogs,
        ]);
    }

    public function view($id)
    {
        $blog = Blog::find($id);
        return response()->json([
            'success' => true,
            'data' => $blog,
        ]);
    }

    public function delete($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Delete image from folder if it exists
            if ($blog->image) {
                $imagePath = public_path($blog->image);

                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }

            // Delete the blog entry from the database
            $blog->delete();

            return response()->json(['success' => true, 'message' => 'Blog deleted successfully']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete blog', 'error' => $e->getMessage()], 500);
        }
    }
}
