<?php

use App\Http\Controllers\Web\BlogPostController;
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
Route::get('fotos', [DefaultPageController::class, 'photos'])->name('web.pgs.photos');
Route::get('videos', [DefaultPageController::class, 'videos'])->name('web.pgs.videos');
Route::get('fale-conosco', [DefaultPageController::class, 'contactUs'])->name('web.pgs.contact-us');
// ...
Route::get('regras/{slug}', [DefaultPageController::class, 'rules'])->name('web.pgs.rules');

Route::name('web.leads.')->group(function () {
    Route::post('contact-us', [LeadController::class, 'sendContactUsForm'])
        ->name('contact-us');

    Route::post('work-with-us', [LeadController::class, 'sendWorkWithUsForm'])
        ->name('work-with-us');

    Route::post('newsletter', [LeadController::class, 'sendNewsletterSubscriberForm'])
        ->name('newsletter');

    Route::post('business-lead', [LeadController::class, 'sendBusinessLeadForm'])
        ->name('business');
});

Route::group(['prefix' => 'blog'], function () {
    Route::get('/', [BlogPostController::class, 'index'])
        ->name('web.blog.index');

    Route::get('busca', [BlogPostController::class, 'search'])
        ->name('web.blog.search');

    Route::get('categoria/{category}', [BlogPostController::class, 'indexByCategory'])
        ->name('web.blog.category');

    Route::get('tipo/{role}', [BlogPostController::class, 'indexByRole'])
        ->name('web.blog.role');

    Route::get('{slug}', [BlogPostController::class, 'show'])
        ->name('web.blog.show');
});

/*
|--------------------------------------------------------------------------
| CLEAR
|--------------------------------------------------------------------------
|
*/

Route::get('/app-clear', function () {
    $optimizeClear = Artisan::call('optimize:clear');
    echo "Optimize cache cleared! <br/>";
    $cacheClear = Artisan::call('cache:clear');
    echo "Application cache cleared! <br/>";
    $clearCompiled = Artisan::call('clear-compiled');
    echo "Compiled services and packages files removed! <br/>";
    $routeClear = Artisan::call('route:clear');
    echo "Route cache cleared! <br/>";
    $viewClear = Artisan::call('view:clear');
    echo "Compiled views cleared! <br/>";
    $configClear = Artisan::call('config:clear');
    echo "Configuration cache cleared! <br/>";
    // $configCache = Artisan::call('config:cache');
    // echo "Configuration cache cleared! <br/>";
    // echo "Configuration cached successfully! <br/><br/>";
    echo 'App cleared!';
});
