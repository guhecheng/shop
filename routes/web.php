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
Route::get('/customer', function () {
    return view('customer');
});

Route::get('/test', 'IndexController@test');
Route::get('/addmenu', 'IndexController@addmenu');
Route::any('/wx', 'IndexController@wx');
Route::any('/card/notify', 'MemberController@notify');
Route::any('/order/wxnotify', 'OrderController@wxnotify');
Route::post('/index/search', 'IndexController@search');
Route::get('/customer/qrcode', 'CustomerController@qrcode');
Route::get('/card/reback', 'MemberController@rebackPay');
Route::get('/card/cronCheckReback', 'MemberController@cronCheckReback');
Route::get('/purchase/select', 'PurchaseController@select');
Route::any('/purchase/notify', 'PurchaseController@notify');
Route::any('/purchase/wxnotify', 'PurchaseController@wxnotify');
Route::any('/user/levelcoupon', 'UserController@levelCoupon');
Route::any('/user/recvcoupon', 'UserController@recvCoupon');

Route::group(['middleware' => 'userlogin'], function (){
    Route::get('/type', 'IndexController@type');
    Route::get('/my', 'UserController@index');
    Route::get('/money', 'UserController@money');
    Route::get('/score', 'UserController@score');
    Route::get('/card', 'MemberController@card');
    Route::post('/card/pay', 'MemberController@pay');
    Route::get('/card/forward', 'MemberController@forward');
    Route::post('/card/getcoupons', 'MemberController@getCoupons');

    Route::get('/purchase/create', 'PurchaseController@index');
    Route::get('/purchase/add', 'PurchaseController@add');
    Route::post('/purchase/upload', 'PurchaseController@upload');
    Route::post('/purchase/goods', 'PurchaseController@goods');

    Route::post('/address/setdefault', 'AddressController@setdefault');
    Route::get('/address/del', 'AddressController@delete');
    Route::resource('address', 'AddressController');

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
    Route::any('/order/getcoupons', 'OrderController@getCoupons');

    Route::get('/info', 'UserController@info');
    Route::post('/relate', 'UserController@relate');
    Route::post('/modinfo', 'UserController@modinfo');

    Route::post('/modcar', 'CarController@modcar');
    Route::get('/coupon', 'CouponController@index');
    Route::any('/coupon/getcoupon', 'CouponController@getCoupon');

    Route::get('/purchase/goods', 'PurchaseController@goods');
    Route::get('/purchase/detail', 'PurchaseController@detail');
    Route::get('/purchase/pay', 'PurchaseController@pay');
    Route::any('/purchase/wxpay', 'PurchaseController@wxpay');
    Route::any('/purchase/cardpay', 'PurchaseController@cardpay');
    Route::any('/purchase/add', 'PurchaseController@add');
    Route::get('/purchase/returnpay', 'PurchaseController@returnpay');

    Route::any('/user/fit', 'UserController@fit');
    Route::any('/user/overstudy', 'UserController@overStudy');
    Route::any('/user/luxurysale', 'UserController@luxurySale');
    Route::any('/user/uploadImage', 'UserController@uploadImage');
    Route::any('/user/sharecoupon', 'UserController@shareCoupon');

    Route::any('/share', 'UserController@share');
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
    Route::post('/user/addremark', 'UserController@addremark');
    Route::post('/user/addmoney', 'UserController@addmoney');
    Route::post('/user/addscore', 'UserController@addscore');
    Route::get('/user/fit', 'UserController@fit');
    Route::get('/user/consulation', 'UserController@consulation');

    Route::get('/type', 'TypeController@index');
    Route::post('/type/add', 'TypeController@add');
    Route::get('/type/delete', 'TypeController@delete');
    Route::post('/type/modify', 'TypeController@modify');
    Route::post('/type/changeorder', 'TypeController@changeorder');

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
    Route::get("/goods/changead", 'GoodsController@changead');
    Route::get("/goods/changesale", 'GoodsController@changesale');
    Route::get("/goods/getproperty", 'GoodsController@getproperty');
    Route::post("/goods/upload", 'GoodsController@upload');
    Route::post("/goods/create", 'GoodsController@create');
    Route::any('/goods/edit', 'GoodsController@edit');
    Route::any('/goods/update', 'GoodsController@update');
    Route::any('/goods/batchAct', 'GoodsController@batchAct');
    Route::any('/user/test', 'UserController@test');

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
    Route::get('/user/info', 'UserController@info');
    Route::post('/user/addmoney', 'UserController@addmoney');

    Route::get('/message', 'MessageController@index');
    Route::get('/message/delete', 'MessageController@delete');
    Route::any('/message/add', 'MessageController@add');
    Route::post('/message/update', 'MessageController@update');
    Route::get('/message/send', 'MessageController@send');

    Route::any('/order', 'OrderController@index');
    Route::post('/order/send', 'OrderController@send');
    Route::get('/order/export', 'OrderController@export');
    Route::post('/order/getcoupons', 'OrderController@getCoupons');

    Route::post("/modify", 'AdminController@modify');

    Route::get('/capital', 'CapitalController@index');
    Route::get('/capital/backup', 'CapitalController@backup');

    Route::get('/brand', 'BrandController@index');
    Route::any('/brand/add', 'BrandController@add');
    Route::any('/brand/mod', 'BrandController@mod');
    Route::get('/brand/getbrand', 'BrandController@getBrand');
    Route::get('/brand/del', 'BrandController@del');
    Route::post('/brand/changeorder', 'BrandController@changeorder');
    Route::get('/coupon', 'CouponController@index');
    Route::any('/coupon/add', 'CouponController@add');
    Route::any('/coupon/finduser', 'CouponController@findUser');

    Route::post('/user/lookcoupons', 'UserController@lookCoupons');
    Route::post('/user/delcoupon', 'UserController@delCoupon');
    Route::post('/user/addsinglemoney', 'UserController@addsinglemoney');
    Route::get('/user/secondsale', 'UserController@secondSale');
    Route::get('/user/addcoupon', 'UserController@addCoupon');

    Route::get('/purchase', 'PurchaseController@index');
    Route::get('/purchase/add', 'PurchaseController@add');
    Route::post('/purchase/create', 'PurchaseController@create');
    Route::get('/purchase/sureback', 'PurchaseController@sureback');
    Route::get('/purchase/modify', 'PurchaseController@modify');
    Route::post('/purchase/mod', 'PurchaseController@mod');

    Route::post('/goods/gettypesbybrand', 'GoodsController@getTypesByBrand');
});