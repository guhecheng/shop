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
Route::get('/test', 'IndexController@test');
Route::get('/addmenu', 'IndexController@addmenu');
Route::any('/wx', 'IndexController@wx');
Route::any('/card/notify', 'MemberController@notify');
Route::any('/order/wxnotify', 'OrderController@wxnotify');

Route::group(['middleware' => 'userlogin'], function (){
    Route::get('/my', 'UserController@index');
    Route::get('/money', 'UserController@money');
    Route::get('/score', 'UserController@score');
    Route::get('/card', 'MemberController@card');
    Route::post('/card/pay', 'MemberController@pay');
    Route::get('/card/forward', 'MemberController@forward');


    Route::resource('address', 'AddressController');
    Route::post('/address/setdefault', 'AddressController@setdefault');

    Route::post('/car/add', 'CarController@add');
    Route::get('/car', 'CarController@index');
    Route::post('/car/delcar', 'CarController@delcar');

    Route::get('/order/create', 'OrderController@create');
    Route::get('/order', 'OrderController@index');
    Route::post('/order/add', 'OrderController@add');
    Route::get('/ordershow', 'OrderController@show');
    Route::get('/order/list', 'OrderController@list');
    Route::any('/orderpay', 'OrderController@orderpay');
    Route::any('/order/pay', 'OrderController@pay');
    Route::any('/order/freepay', 'OrderController@freepay');
    Route::any('/order/cardpay', 'OrderController@cardpay');
    Route::any('/order/repay', 'OrderController@repay');
    Route::any('/order/ajaxGetGoods', 'OrderController@ajaxGetGoods');
    Route::any('/order/changeorder', 'OrderController@changeorder');

    Route::get('/info', 'UserController@info');
    Route::post('/relate', 'UserController@relate');
    Route::post('/modinfo', 'UserController@modinfo');

    Route::post('/modcar', 'CarController@modcar');
});
Route::get('/goods', 'GoodsController@index');
Route::get('/goods/property', 'GoodsController@property');
Route::get('/goods/getgoods', 'GoodsController@getgoods');
Route::post('/goods/getgoodssku', 'GoodsController@getgoodssku');
Route::get('/test/testorder', 'TestController@testorder');



Route::get('/admin/login', 'Admin\AdminController@login');
Route::post('/admin/check', 'Admin\AdminController@check');

Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['checklogin', 'menu']], function() {
    Route::get('/logout', 'AdminController@logout');
    Route::get('/', 'AdminController@index');

    Route::get('/manage', 'ManagerController@index');

    Route::get('/user', 'UserController@index');
    Route::get('/user/delete', 'UserController@delete');
    Route::get('/user/usercard', 'UserController@usercard');

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
    Route::get("/goods/changesale", 'GoodsController@changesale');
    Route::get("/goods/getproperty", 'GoodsController@getproperty');
    Route::post("/goods/upload", 'GoodsController@upload');
    Route::post("/goods/create", 'GoodsController@create');

    Route::get("/card/recharge", 'CardController@recharge');
    Route::resource("card", 'CardController');
    Route::resource('/goods', 'GoodsController');

    Route::get('/auth', 'AuthController@index');
    Route::post('/auth/add', 'AuthController@add');
    Route::get('/adminauth', 'AuthController@adminauth');
    Route::post('/auth/addAdmin', 'AuthController@addAdmin');
    Route::get('/auth/disable', 'AuthController@disable');
    Route::post('/auth/updateAdmin', 'AuthController@updateAdmin');

    Route::any('/userexport', 'UserController@userExport');
    Route::any('/user/upload', 'UserController@upload');

    Route::get('/message', 'MessageController@index');
    Route::get('/message/delete', 'MessageController@delete');
    Route::post('/message/add', 'MessageController@add');
    Route::post('/message/update', 'MessageController@update');
    Route::get('/message/send', 'MessageController@send');

    Route::get('/order', 'OrderController@index');
    Route::post('/order/send', 'OrderController@send');
    Route::get('/order/export', 'OrderController@export');

    Route::post("/modify", 'AdminController@modify');

    Route::get('/capital', 'CapitalController@index');
});