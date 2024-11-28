<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoogleTrendsController;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    public function index()
            {
                $articles = Article::orderBy('created_at', 'desc')->get();
                return response()->json($articles);
            }

    public function updateViews($id)
            {
                $article = Article::find($id);

                if ($article) {
                    $article->increment('views');
                    return response()->json($article);
                }

                return response()->json(['error' => 'Article not found'], 404);
            }

        public function show($id)
            {
                $article = Article::find($id);

                if (!$article) {
                    return response()->json(['message' => 'Article not found'], 404);
                }

                $article->views += 1;
                $article->save();

                return response()->json($article);
            }

            public function store(Request $request)
            {
                $validatedData = $request->validate([
                    'title' => 'required|string|max:255',
                    'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                    'body' => 'required|string',
                    'category_id' => 'required|integer|exists:categories,id',
                    'author_id' => 'required|integer|exists:users,id',
                    'views' => 'nullable|integer',
                    'slug' => 'nullable|string|unique:articles,slug',
                    'tags' => 'nullable|string',
                    'image_url' => 'string',
                    'linkVideo' => 'nullable|string',
                ]);

                $user = User::findOrFail($request->author_id);
                $validatedData['author_name'] = $user->name;

                if (!$request->filled('slug')) {
                    $validatedData['slug'] = Str::slug($request->title, '-');
                }

                if ($request->filled('image_url')) {
                   
                    $originalPath = $request->input('image_url');
                    $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
            
                    $slugTitle = Str::slug($request->title, '-');
                    $newFileName = $slugTitle . '.' . $extension;
            
                    $validatedData['image_url'] = '/storage/images/' . $newFileName;
                    
                    // error_log('Image URL modified to: ' . $validatedData['image_url']);
                }            

                $article = Article::create($validatedData);

                return response()->json($article, 201);
            }

    public function getPopularArticles()
            {
                $articles = Article::orderBy('views', 'desc')->limit(10)->get();
                if ($articles->isEmpty()) {
                    return response()->json(['message' => 'Article not found'], 404);
                }

                return response()->json($articles);
            }

    public function getRelatedArticles($category_id, $current_article_id)
            {
                $articles = Article::where('category_id', $category_id)
                                    ->where('id', '!=', $current_article_id)
                                    ->orderBy('created_at', 'desc')
                                    ->take(8)
                                    ->get();

                return response()->json($articles);
            }

    public function destroy($id)
            {
                Article::destroy($id);
                return response()->json(null, 204);
            }

            public function update(Request $request, $id)
            {
                $article = Article::findOrFail($id);
            
                $validatedData = $request->validate([
                    'title' => 'required|string|max:255',
                    'body' => 'required|string',
                    'category_id' => 'required|integer|exists:categories,id',
                    'author_id' => 'required|integer|exists:users,id',
                    'slug' => 'nullable|string|unique:articles,slug,'.$article->id,
                    'views' => 'nullable|integer',
                    'tags' => 'nullable|string',
                ]);

                $validatedData['image_url'] = $request->image_url ?? $article->image_url;
                $article->update($validatedData);
                return response()->json($article, 200);
            }
            
        public function search(Request $request)
            {
                $query = $request->input('query');
                $results = Article::where('title', 'like', "%{$query}%")->get();
                return response()->json($results);
            }

        public function getTopTagArticles()
            {
                $articles = Article::select('id', 'title', 'body', 'tags', 'image_url', 'slug', 'views', 'category_id', 'created_at')
                    ->whereNotNull('tags')
                    ->orderBy(Article::raw('LENGTH(tags) - LENGTH(REPLACE(tags, ",", ""))'), 'DESC')
                    ->limit(5)
                    ->get();
        
                return response()->json($articles);
            }

            public function getArticlesByTag($tagName)
                {
                   
                    $tags = explode('-', $tagName);

                    $articles = Article::where(function($query) use ($tags) {
                        foreach ($tags as $tag) {
                            $query->orWhere('tags', 'like', "%{$tag}%");
                        }
                    })->get();

                    return response()->json($articles);
                }
                

                            
}
