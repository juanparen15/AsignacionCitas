<?php
// routes/api.php
use App\Http\Controllers\Api\AppointmentApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('appointments')->group(function () {
    Route::get('/services', [AppointmentApiController::class, 'services']);
    Route::get('/services/{service}/slots', [AppointmentApiController::class, 'availableSlots']);
    Route::post('/services/{service}/book', [AppointmentApiController::class, 'book']);
    Route::post('/my-appointments', [AppointmentApiController::class, 'myAppointments']);
});
