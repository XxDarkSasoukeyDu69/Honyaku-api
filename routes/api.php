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

Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');

Route::post('/file', 'Api\FileController@store');

Route::get('/google/language/support', 'Api\GoogleController@getLanguageSupport');

Route::post('/billing/postPaymentIntent', 'Api\StripeController@createPaymentIntent');
Route::post('/billing/success', 'Api\StripeController@successPayment');
Route::post('/billing/insertBilling', 'Api\StripeController@insertBilling');

Route::post('/mail/sender', 'Api\MailController@receiveFrom');
Route::get('/getOrder/{id}', 'Api\FileController@getOrder');
Route::delete('/deleteOrder/{id}', 'Api\FileController@destroy');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function () {
    Route::get('/getNoTranslateFiles', 'Api\FileController@index');
    Route::put('/setRunningTranslate/{id}', 'Api\FileController@setRunning');
    Route::get('/getFile/{id}', 'Api\FileController@show');
    Route::put('/user/update', 'Api\UserController@update');
    Route::get('/file/getMyRunningTranslation', 'Api\FileController@getMyRunningTranslation');
    Route::get('/file/getMyEffectedTranslation', 'Api\FileController@getMyEffectedTranslation');
    Route::post('/file/updateTranslation/{id}', 'Api\FileController@updateTranslation');
    Route::post('/file/setFinish/{id}', 'Api\FileController@setFinish');
});
