<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
    
            $imageName = $image->hashName(); 
            if ($request->filled('title')) {
                $slug = Str::slug($request->input('title'), '-');
                $extension = $image->getClientOriginalExtension();
                $imageName = $slug . '.' . $extension;
            }
    
            $imagePath = $image->storeAs('images', $imageName, 'public');
            $imageUrl = Storage::url($imagePath);
            return response()->json(['image_url' => $imageUrl]);
    }

        return response()->json(['message' => 'No image uploaded'], 400);
    }
    
    public function show($filename)
    {
        $path = storage_path('app/public/images/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
    
}