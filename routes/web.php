<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'user'], function(){
    //User
    Route::get('/index', [
        'uses' => 'MainController@getIndex',
        'as' => 'dashboard.index'
    ]);


    // User Validation
    Route::group(['prefix' => 'auth'], function(){
        // User Login Page
        Route::get('/sign-in', [
            'uses' => 'UserAuthController@signInPage',
            'as' => 'auth.signIn',
        ]);
    });
});


Route::group(['prefix' => 'course'], function(){
   Route::get('/add', [
       'uses' => 'CourseController@getAddCourse',
       'as' => 'course.addCourse'
   ]);
});

Route::get('user', [
    'uses' => 'CourseController@getUsers',
    'as' => 'get.courseUsers'
]);