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
Route::group(['namespace' => 'Authentication'], function () {
    Route::post('/authenticate', 'AuthenticationsController@authenticate');
    Route::post('/register', 'AuthenticationsController@register');
});

Route::group(['namespace' => 'NegaritClients'], function () {
    Route::get('/negarit_clients', 'NegaritClientsController@getClientsList');
    Route::get('/negarit_clients_paginated', 'NegaritClientsController@getClientsPaginated');
    Route::post('/negarit_client', 'NegaritClientsController@createClient');
    Route::patch('/negarit_client', 'NegaritClientsController@updateClient');
    Route::delete('/negarit_client/{id}', 'NegaritClientsController@deleteClient');
});

Route::group(['namespace' => 'Messages'], function () {
    Route::get('/received_messages', 'ReceivedMessagesController@getReceivedMessagesList');
    Route::get('/received_messages_paginated', 'ReceivedMessagesController@getReceivedMessagesPaginated');
    Route::post('/received_message', 'ReceivedMessagesController@createdReceivedMessage');
});

Route::group(['namespace' => 'Messages'], function () {
    Route::get('/send_pending_message', 'SendMessagesController@sendPendingMessage');
});

Route::group(['namespace' => 'SyncServices'], function () {
    Route::get('/sync_sent_messages', 'SyncSentMessagesController@pullSentMessage');
});
