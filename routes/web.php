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

   Route::post('/add', [
       'uses' => 'CourseController@postAddCourse',
       'as' => 'course.addCourse'
   ]);
});

Route::group(['prefix' => 'admin'], function(){
    Route::get('/add', [
        'uses' => 'UserController@getCreateUser',
        'as' => 'user.createUser'
    ]);

    Route::post('/add', [
        'uses' => 'UserController@postCreateUser',
        'as' => 'user.createUser'
    ]);

    Route::get('/courses', [
       'uses' => 'CourseController@getAllCourses' ,
        'as' => 'course.showAllCourses'
    ]);
});

// for data tables
Route::group(['prefix' => 'datatables'], function(){
    Route::get('user', [
        'uses' => 'CourseController@getUsers_dt',
        'as' => 'get.courseUsers'
    ]);

    Route::get('allCourses', [
        'uses' => 'CourseController@getAllCourses_dt',
        'as' => 'get.allCourses'
    ]);
});


