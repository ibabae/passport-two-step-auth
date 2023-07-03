<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PublicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('/register',[PublicController::class, 'Register']);
Route::post('/login',[PublicController::class, 'Login']);
Route::post('/verify',[PublicController::class, 'Verify']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
