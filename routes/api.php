<?php
use App\Http\Controllers\Api\ArticleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Models\Category;
use App\Models\User;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SliderHeadlineController;
use App\Http\Controllers\GoogleTrendController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Middleware\ServeStorageFile;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Symfony\Component\HttpFoundation\JsonResponse;

$client = new \Google_Client();

// Route yang memerlukan otentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('articles', [ArticleController::class, 'store']);
    Route::put('articles/{id}', [ArticleController::class, 'update']);
    Route::delete('articles/{id}', [ArticleController::class, 'destroy']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::post('/articles/{id}/set-as-slider', [SliderHeadlineController::class, 'setAsSlider']);
    Route::post('/articles/{id}/unset-slider', [SliderHeadlineController::class, 'unsetSlider']);
});

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('articles/{id}', [ArticleController::class, 'show']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('categories', function() {
    return Category::all();
});
Route::get('/articleSlider', [SliderHeadlineController::class, 'getSliderArticles']);

Route::get('/categories/{slug}/articles', [CategoryController::class, 'getArticlesByCategory']);

Route::put('articles/{id}/views', [ArticleController::class, 'updateViews']);

Route::get('articles/related/{category_id}/{current_article_id}', [ArticleController::class, 'getRelatedArticles']);

Route::post('upload-image', [ImageController::class, 'uploadImage']);
Route::get('/trends', [GoogleTrendController::class, 'getTrend']);
Route::get('/search', [ArticleController::class, 'search']);
Route::get('articlespop', [ArticleController::class, 'getPopularArticles']);
Route::post('/comments', [CommentController::class, 'store']);
Route::get('/articles/{id}/comments', [CommentController::class, 'index']);
Route::get('/articlestoptags', [ArticleController::class, 'getTopTagArticles']);
Route::get('/check-session', [AuthController::class, 'checkSession']);
Route::post('/articles/{articleId}/like', [LikeController::class, 'like']);
Route::get('/articles/{articleId}/likes', [LikeController::class, 'getLikeCount']);
// Route::get('storage/{filename}', [ServeStorageFile::class, 'handle']);

Route::get('/storage/images/{filename}', [ImageController::class, 'show']);

Route::post('/logingoogle', function (Request $request) {
    try {
        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];

            $user = User::updateOrCreate([
                'email' => $email,
            ], [
                'name' => $name,
                'google_id' => $googleId,
                'password' => null, 
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['token' => $token, 'user' => $user]);
    } catch (\Exception $e) {
        // return response()->json(data: ['error' => $e->getMessage()], 500);
        return $e;
    }
});


