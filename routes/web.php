<?php

use App\Http\Controllers\Web\DefaultPageController;
use App\Http\Controllers\Web\LeadController;
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

/*
|--------------------------------------------------------------------------
| PUBLIC WEBSITE ROUTES
|--------------------------------------------------------------------------
|
*/

Route::get('/', [DefaultPageController::class, 'index'])->name('web.pgs.index');
Route::get('sobre', [DefaultPageController::class, 'about'])->name('web.pgs.about');
Route::get('contato', [DefaultPageController::class, 'contactUs'])->name('web.pgs.contact-us');
// ...
Route::get('regras/{slug}', [DefaultPageController::class, 'rules'])->name('web.pgs.rules');

// ...
