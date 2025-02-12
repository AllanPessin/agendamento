<?php

use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('agenda', [ScheduleController::class, 'index'])->name('schedule.index');
Route::get('eventos', [ScheduleController::class, 'listar'])->name('schedule.listar');


Route::post('schedule', [ScheduleController::class, 'store'])->name('schedule.store');
Route::delete('schedule/{schedule}', [ScheduleController::class, 'destroy'])->name('schedule.store');
