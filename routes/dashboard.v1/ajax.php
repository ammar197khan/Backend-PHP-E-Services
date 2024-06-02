<?php

Route::prefix('ajax')->middleware(['company', IsCompany::class])->group(function () {
    Route::post('admin/items/approve/{id}', 'Api\Admin\OrderController@approveItem');
});
