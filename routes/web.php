<?php

use App\Models\PartnerMerchant;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/', function () {
    Artisan::call('cache:clear');

    // $merchants = PartnerMerchant::all();

    // foreach($merchants as $merchant){

    //     $merchant->update([
    //         'brands' => $merchant->merchant_company
    //     ]);
    // }
    return view('welcome');
});
