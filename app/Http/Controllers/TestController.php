<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/7/5
 * Time: 23:25
 */
namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Payment\Order;
use EasyWeChat\Foundation\Application;

class TestController extends Controller {
    public function testorder() {
        $orders = DB::table('orderinfo')->where([
            ['status', '<=', 1],
            ['create_time', '<=', date("Y-m-d H:i:s", time()-30*60)]
        ])->select('order_no')->get();
        if (empty($orders)) return;
        foreach ($orders as $order) {
            $item = DB::table('order')->where('order_no', $order->order_no)->select('skuid', 'count')->get();
            foreach ($item as $key=>$value) {
                DB::table('goodssku')->where('sku_id', $value->skuid)->increment('num', $value->count);
                DB::table('orderinfo')->where('order_no', $order->order_no)->update(['status' => 6]);
            }
        }
    }
}