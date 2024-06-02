<?php

Route::namespace('Api\Tech')->middleware('jsonify')->prefix('tech')->group(function () {
    Route::post('register', 'Auth\TechnicianRegister');
    Route::post('code_send', 'Auth\SendTechnicianConfirmationCode');
    Route::post('code_check', 'Auth\VerifyTechnicianConfirmationCode');
    Route::post('password_change', 'Auth\ChangeTechnicianPassword');
    Route::post('login', 'Auth\TechnicianLogin');
    Route::post('logout', 'Auth\TechnicianLogout');

    Route::post('profile', 'Profile\GetTechnicianProfile');
    Route::post('splash', 'Profile\SetTechnicianDeviceToken');
    Route::post('location/set', 'Profile\SetTechnicianLocation');
    Route::post('profile/update', 'Profile\UpdateTechnicianProfile');
    Route::post('status/switch', 'Profile\SetTechnicianOnlineStatus');
    Route::post('rates', 'Profile\GetTechnicianRate');

    Route::post('home', 'Order\GetTechnicianPendingOrders');
    Route::post('orders', 'Order\GetTechnicianCompletedOrders');
    Route::post('order/details', 'Order\GetTechnicianOrderDetails');
    Route::post('view-order/teamlead-remark-report', 'Order\GetTechnicianOrderTeamleadRemarkReport');
    Route::post('send-back/tl-remark-report', 'Order\SendBackTeamLeadRemarkReport');
    Route::post('order/track/update', 'Order\SetTechnicianOrderStatus');
    Route::post('order/change_status', 'Order\FinishTechnicianOrder');
    Route::post('order/cancel', 'Order\CancelTechnicianOrder');
    Route::post('order/assignment', 'Order\AssignmentTechnician');
    Route::post('order/team-lead-submit-report', 'Order\TeamLeadSubmitReport');
    Route::post('order/supervisor-update-report', 'Order\SupervisorUpdateReport');
    Route::post('order/get-assessment-report-log', 'Order\GetAssessmentReportLog');

    Route::post('order/get_third_levels', 'Category\GetCategoryTechnicianJobs');

    Route::post('order/transaction/cash/confirm', 'Order\ConfirmCashRecieval');

    Route::post('warehouse/cats', 'Item\GetItemsCategories');
    Route::post('warehouse/items', 'Item\GetItemsOfCategory');
    Route::post('warehouse/search', 'Item\SearchItems');
    Route::post('warehouse/item/show', 'Item\GetItemDetails');
    Route::post('warehouse/item/request', 'Item\RequestNotAvailableItem');
    Route::post('warehouse/item/add', 'Item\RequestOrderItemApprovalForUser');

    Route::post('notifications', 'Notification\GetTechnicianNotifications');
    Route::post('notification/seen', 'Notification\SetTechnicianNotificationAsSeen');

    Route::get('about_us/{lang}', 'Info\TechnicianAppAboutUs');
    Route::get('terms/{lang}', 'Info\TechnicianAppTerms');
    Route::get('privacy/{lang}', 'Info\TechnicianAppPrivacy');
});
