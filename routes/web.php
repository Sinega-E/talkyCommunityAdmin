<?php
use App\Http\Controllers\TrainerController;
use Illuminate\Support\Facades\Route;

// Homepage route
Route::get('/', function () {
    return view('welcome');
});

// // Trainer route for fetching data based on username (via the controller)
// Route::get('/trainer/{username}', [TrainerController::class, 'getTrainerInfo']);

