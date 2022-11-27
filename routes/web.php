<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\TransactionController;
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

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/address/{address}', [AddressController::class, 'index'])->name('address');
Route::post('/address', [AddressController::class, 'search'])->name('address.search');

Route::get('/transaction/{transactionId}', [TransactionController::class, 'index'])->name('transaction');
Route::get('/block/{block}', [BlockController::class, 'index'])->name('block');

Route::get('/info', [InfoController::class, 'index'])->name('info');
