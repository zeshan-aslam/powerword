<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->get('/usernew', function (Request $request) {
    return $request->user();
});


Route::get('/user', 'PartnersMerchantController@getPartners');
Route::get('/usernew', 'PartnersMerchantController@getPartnersNew');
Route::get('/tokens', 'PartnersMerchantController::class@getTokens');
Route::get('/saveDetail','PartnersMerchantController@saveDetail');
Route::post('/saveMatched','PartnersMerchantController@trackMatchedWords');
Route::post('/saveErrors','PartnersMerchantController@trackErrorWords');