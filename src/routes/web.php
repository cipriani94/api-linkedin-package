<?php

use Neurohub\Apilinkedin\Http\Controllers\LinkedinShareController;

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/post/request/share/{id}', [LinkedinShareController::class, 'index'])->name('post.linkedin');
    Route::post('/post/request/share', [LinkedinShareController::class, 'store'])->name('post.linkedin.store');
});
