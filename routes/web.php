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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::get('/', 'IndexController@index');
Route::get('/aboutme', function() {
    return view('aboutme');
});
Route::get('/goods', 'GoodsController@index');
Route::get('/goods/property', 'GoodsController@property');

Route::get('/my', 'UserController@index');
Route::get('/money', 'IndexController@money');
Route::get('/card', 'MemberController@card');
Route::resource('address', 'AddressController');
Route::post('/address/setdefault', 'AddressController@setdefault');

Route::post('/car/add', 'CarController@add');
Route::get('/car', 'CarController@index');
Route::post('/car/delcar', 'CarController@delcar');

Route::get('/order/create', 'OrderController@create');

Route::get('/admin/login', 'Admin\AdminController@login');
Route::post('/admin/check', 'Admin\AdminController@check');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => 'checklogin'], function() {
    Route::get('/', 'IndexController@index');

    Route::get('/manage', 'ManagerController@index');

    Route::get('/user', 'UserController@index');
    Route::get('/user/delete', 'UserController@delete');

    Route::get('/type', 'TypeController@index');
    Route::post('/type/add', 'TypeController@add');
    Route::get('/type/delete', 'TypeController@delete');
    Route::post('/type/modify', 'TypeController@modify');

    Route::get('/property', 'PropertyController@index');
    Route::post('/property/addkey', 'PropertyController@addkey');
    Route::get('/property/deletekey', 'PropertyController@deletekey');
    Route::post('/property/modifykey', 'PropertyController@modifykey');
    Route::get('/property/listvalue', 'PropertyController@listvalue');
    Route::post('/property/addvalue', 'PropertyController@addvalue');
    Route::post('/property/modifyvalue', 'PropertyController@modifyvalue');
    Route::get('/property/deletevalue', 'PropertyController@deletevalue');

    Route::get('/goods', 'GoodsController@index');
    Route::get("/goods/add", 'GoodsController@add');
    Route::get("/goods/delete", 'GoodsController@delete');
    Route::get("/goods/changehot", 'GoodsController@changehot');
    Route::get("/goods/getproperty", 'GoodsController@getproperty');
    Route::post("/goods/upload", 'GoodsController@upload');
    Route::post("/goods/create", 'GoodsController@create');

    Route::resource("card", 'CardController');
    Route::resource('/goods', 'GoodsController');
});