<?php
// TODO: User Logout
// TODO: Device Token for all logged apps

Route::namespace('Api\User')->middleware('jsonify')->prefix('user')->group(function () {
    Route::post('register', 'Auth\RegisterUser');
    Route::post('code_send', 'Auth\SendUserConfirmationCode');
    Route::post('code_check', 'Auth\VerifyUserConfirmationCode');
    Route::post('password_change', 'Auth\ChangeUserPassword');
    Route::post('login', 'Auth\UserLogin');
    Route::post('logout', 'Auth\UserLogout');

    Route::post('profile', 'Profile\GetUserProfile');
    Route::post('splash', 'Profile\SetUserDeviceToken');
    Route::post('location/set', 'Profile\SetUserLocation');
    Route::post('profile/update', 'Profile\UpdateUserProfile');

    Route::post('home', 'Category\GetMainCategories');
    Route::post('home/sub_cats', 'Category\GetSubCategories');

    Route::post('get_techs', 'Technician\GetTechnicians');
    Route::post('get_supervoisors', 'Technician\GetSupervisors');
    
    Route::post('search_techs', 'Technician\SearchTechnicians');
    Route::post('search_supervisors', 'Technician\SearchSupervisors');
    Route::post('search_teamleads', 'Technician\SearchTeamLeads');
    Route::post('view_tech', 'Technician\GetTechnicianDetails');
    Route::post('order/view_tech_to_rate', 'Technician\GetTechnicianForRating');

    Route::post('order', 'Order\MakeOrder');
    Route::post('orders', 'Order\GetUserOrders');
    Route::post('order/details', 'Order\GetUserOrderDetails');
    Route::post('order/re_schedule', 'Order\RescheduleUserOrder');
    Route::post('order/cancel', 'Order\CancelUserOrder');
    Route::post('order/rate', 'Order\RateOrder');
    // Route::post('order/payment','Order\PaymentOrder');

    Route::post('order/item_change_status', 'Order\Item\TempApproveSingleItem');
    Route::post('order/items/submit', 'Order\Item\ConfirmAllItemsApproval');
    Route::post('v.0.2/order/items/submit', 'Order\Item\ApproveOrderItems');
    Route::post('order/transaction/initiate', 'Order\Transaction\InitiateTransaction');
    Route::post('order/transaction/execute', 'Order\Transaction\ExecuteTransaction');
    Route::post('order/transaction/status', 'Order\Transaction\GetStatus');
    Route::post('order/transaction/direct', 'Order\Transaction\DirectPayment');
    Route::post('order/transaction/confirm', 'Order\Transaction\ConfirmTransaction');
    Route::get('order/transaction/callback/success', 'Order\Transaction\CallBackUrl');
    Route::get('order/transaction/callback/failure', 'Order\Transaction\ErrorUrl');
    Route::post('order/payments/set_type', 'Order\SetPaymentType');
    Route::post('order/transaction/online/confirm', 'Order\Transaction\ConfirmOnlineTransaction');
    Route::post('order/transaction/cash/inform', 'Order\Transaction\InformCashTransaction');

    Route::post('notifications', 'Notification\GetUserNotifications');
    Route::post('notification/seen', 'Notification\SetUserNotificationAsSeen');

    Route::get('about_us/{lang}', 'Info\UserAppAboutUs');
    Route::get('terms/{lang}', 'Info\UserAppTerms');
    Route::get('privacy/{lang}', 'Info\UserAppPrivacy');

    Route::get('complain_titles/{lang}', 'Complaint\GetComplaintTypes');
    Route::post('complain', 'Complaint\SendComplaint');


    Route::post('location', 'Location\GetUserLocation');
    Route::post('location_set', 'Location\SetUserLocation');
    Route::post('location_update', 'Location\UpdateUserLocation');
    Route::post('location_delete', 'Location\DeleteUserLocation');
});
