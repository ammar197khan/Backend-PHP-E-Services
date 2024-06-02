<?php


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard/blank', function () {
//     return view('dashboard.layouts.app');
// });
//
// Route::get('dashboard/', function () {
//     return view('dashboard.views.home');
// });


include 'dashboard.v1/admin.php';
include 'dashboard.v1/provider.php';
include 'dashboard.v1/company.php';
include 'dashboard.v1/ajax.php';


//Cron Jobs
Route::get('/crone/schedule', 'Admin\CroneController@schedule');
Route::get('/crone/rotate', 'Admin\CroneController@rotate');
//End Cron Jobs

Route::get('/policy', 'Admin\HomeController@policy');

Route::get('set-local','Admin\HomeController@setLocal')->name('set.local');

// function (Request $request) {
    // \Session::put('current_locale',$_GET['current_locale']);
    // return redirect('/admin/dashboard');
// }

Route::get('report-issue', function () {
    return redirect('https://forms.gle/BuTWwCHRfV4kwEHb7');
});

Route::get('/script_database', '\App\Script\setMaterials');
