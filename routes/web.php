<?php

use Illuminate\Support\Facades\Route;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/home/create', [App\Http\Controllers\HomeController::class, 'create'])->name('home.create');
Route::post('/home', [App\Http\Controllers\HomeController::class, 'store']);
Route::get('/home/{id}/edit', [App\Http\Controllers\HomeController::class, 'edit'])->name('home.edit');
Route::put('/home/{id}', [App\Http\Controllers\HomeController::class, 'update']);
Route::delete('/home/{id}', [App\Http\Controllers\HomeController::class, 'destroy']);

Route::get('/campaigns', [App\Http\Controllers\CampaignsController::class, 'get_campaign_level_summary']);
Route::get('/campaigns/{id}/site_level', [App\Http\Controllers\CampaignsController::class, 'get_site_level_summary']);
Route::get('/campaigns/account_level', [App\Http\Controllers\CampaignsController::class, 'get_account_level_summary']);
Route::post('/block_publishers', [App\Http\Controllers\CampaignsController::class, 'block_publishers']);
Route::post('/campaigns/{id}', [App\Http\Controllers\CampaignsController::class, 'update_publisher_targeting']);
Route::put('/campaigns/{id}', [App\Http\Controllers\CampaignsController::class, 'update_campaign_level']);
Route::patch('/campaigns/{id}', [App\Http\Controllers\CampaignsController::class, 'update_bid_modifiers']);

Route::get('/legendary-thanks-for-purchase', [App\Http\Controllers\CampaignsController::class, 'config_data']);