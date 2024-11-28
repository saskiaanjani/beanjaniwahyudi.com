<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CategoryController extends Controller
{
    public function getArticlesByCategory($slug) {
        $category = Category::where('slug', $slug)->firstOrFail();
        $articles = $category->articles()->get();
        return response()->json($articles);
    }

    public function store(Request $request)
            {
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                ]);
                $category = Category::create($validatedData);
                return response()->json($category, 201);
            }

}