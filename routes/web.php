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

Auth::routes();

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', [
    'uses' => 'UserAuthController@signInPage',
    'as' => 'auth.signIn',
    ]);

Route::post('/', [
   'uses' => 'UserAuthController@postSignIn',
   'as' => 'auth.signIn'
]);


Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'user', 'middleware' => 'auth'], function(){
    //User
    Route::get('/index', [
        'uses' => 'MainController@getIndex',
        'as' => 'dashboard.index'
    ]);


    // User Validation
    Route::group(['prefix' => 'auth'], function(){
        // User Login Page
//        Route::get('/sign-in', [
//            'uses' => 'UserAuthController@signInPage',
//            'as' => 'auth.signIn',
//        ]);
        Route::get('/sign-out', [
            'uses' => 'UserAuthController@getSignOut',
            'as' => 'auth.signOut',
        ]);
    });
});


Route::group(['prefix' => 'course', 'middleware' => 'auth'], function(){
   Route::get('/add', [
       'uses' => 'CourseController@getAddCourse',
       'as' => 'course.addCourse'
   ]);

   Route::post('/add', [
       'uses' => 'CourseController@postAddCourse',
       'as' => 'course.addCourse'
   ]);

   //assignment
    Route::get('/allAssignments', [
        'uses' => 'AssignmentController@getAllAssignments' ,
        'as' => 'assignment.showAllAssignments'
    ]);

    Route::get('/assignment/new', [
        'uses' => 'AssignmentController@getCreateAssignment',
        'as' => 'Assignment.createAssignment'
    ]);

    Route::post('assignment/new', [
        'uses' => 'AssignmentController@postCreateAssignment',
        'as' => 'Assignment.createAssignment'
    ]);


    Route::get('{course_id}/assignments/{assignment_id}/handIn', [
        'uses' => 'AssignmentController@getHandInAssignment',
        'as' => 'assignment.handInAssignment'
    ]);



//    Route::group(['prefix' => '{course_id}', 'middleware' => 'auth'], function(){
//
//    });
});

Route::get('/assignments', [
    'uses' => 'AssignmentController@getAssignments',
    'as' => 'assignment.showAssignments'
]);

Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function(){
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
Route::group(['prefix' => 'datatables', 'middleware' => 'auth'], function(){
    Route::get('/user', [
        'uses' => 'CourseController@getUsers_dt',
        'as' => 'get.courseUsers'
    ]);

    Route::get('/allCourses', [
        'uses' => 'CourseController@getAllCourses_dt',
        'as' => 'get.allCourses'
    ]);

    Route::get('/allAssignments', [
       'uses' => 'AssignmentController@getAllAssignments_dt',
       'as' => 'get.allAssignments'
    ]);
});

//for dropZone
Route::group(['prefix' => 'dropZone', 'middleware' => 'auth'], function() {
    Route::post('/upload', [
        'uses' => 'AssignmentController@uploadAssignment',
        'as' => 'dropZone.uploadAssignment',
    ]);

    Route::post('/delete', [
        'uses' => 'AssignmentController@deleteAssignment',
        'as' => 'dropZone.deleteAssignment',
    ]);

    Route::post('/fileDetails', [
        'uses' => 'AssignmentController@getAssignmentFileDetail',
        'as' => 'dropZone.getAssignmentFileDetail'
    ]);
});

