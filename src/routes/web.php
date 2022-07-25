<?php

use Neurohub\Apilinkedin\Http\Controllers\LinkedinShareController;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/post/request/share', [LinkedinShareController::class, 'index'])->name('post.linkedin');
    Route::post('/post/request/share', [LinkedinShareController::class, 'store'])->name('post.linkedin.store');

    Route::get('/post/get-profile-id/{id}', [LinkedinShareController::class, 'getProfileId'])->name('post.linkedin.getprofile');
});
