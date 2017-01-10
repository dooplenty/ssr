<?php
Route::group(['middleware' => ['web']], function () {
    Route::get('email/setup', 'Dooplenty\SyncSendRepeat\Controllers\SetupController@getSetup');
});

Route::get('ssr/sync', 'Dooplenty\SyncSendRepeat\Controllers\MailboxController@syncMessages');