<?php
Route::get('ok', 'Api\HealthController@ok');

Route::get('testMail', 'Api\HealthController@testMail');

Route::get('version', 'Api\VersionController@version');

// include 'api/user.php';
//
// include 'api/technecian.php';

include 'api/user_routes.php';

include 'api/technecian_routes.php';

include 'api/admin.php';
