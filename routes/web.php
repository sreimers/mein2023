<?php

use Illuminate\Foundation\Application;
use App\Http\Controllers\DrivingServiceController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Chat;
use App\Events\ChatSent;
use App\Http\Resources\Chat as ChatResource;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/pretix/login', function () {
    return Inertia::render('PretixLogin');
})->name('pretix');


Route::get('/pretix/login/{token}', function ($token) {
    if (empty($token)) {
        return abort(403);
    }
    $user = User::where('token', $token)->first();

    if ($user) {
        Auth::loginUsingId($user->id, true);

        return redirect('/');
    }

    return abort(403);
});

Route::get('/offline', function () {
    return view('offline');
});

Route::get('/site.webmanifest', function () {
    return view('manifest');
});


/* --- auth routes --- */

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    /* Chats */
    Route::get('/chats', function () {
        return ChatResource::collection(Chat::with('user')->get());
    });

    /* Project Routes */
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

    /* Fahrdienst */
    Route::get('/driving_services', [DrivingServiceController::class, 'index'])->name("drivings");
    Route::post('/driving_services', [DrivingServiceController::class, 'store']);
    Route::put('/driving_services/{driving_service}', [DrivingServiceController::class, 'update']);
    Route::delete('/driving_services/{driving_service}', [DrivingServiceController::class, 'destroy']);

    Route::post('/chats', function () {
        $message = request()->message;
        if (empty($message))
            return abort(400);
        $chat = new Chat;
        $chat->content = request()->message;
        $chat->user_id = auth()->user()->id;
        $chat->save();

        event(new ChatSent(new ChatResource($chat)));
    });

});
