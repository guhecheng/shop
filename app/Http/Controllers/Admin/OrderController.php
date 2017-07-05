<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/7/5
 * Time: 23:40
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    private $state = [
        'ORDER_CREATE' => 0 ,
        'ORDER_WAIT_PATY' => 1,
        'ORDER_HAS_PAY' => 2,
        'ORDER_HAS_SEND' => 3,
        'ORDER_HAS_RECV' => 4,
        'ORDER_HAS_LOST' => 5,
    ];
    private $pay_type = [
        'WX_PAY' => 0,
        'CARD_PAY' => 1
    ];

    public function index() {
        $orders = DB::table('orderinfo')->leftJoin('order', 'order.order_no', '=', 'orderinfo.order_no')
            ->where('orderinfo.status', ORDER_HAS_PAY)->get();

        $skuids = [];
        foreach ($orders as $item) {
            $skuids[] = $item->skuid;
        }
        $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                left join goods on goods.goodsid=gp.goods_id
                where gp.sku_id in (".implode(',', $skuids).")";
        $skus = DB::select($sql);
        foreach ($orders as &$item) {
            $item->property = '';
            foreach ($skus as $value) {
                if ($value->sku_id == $item->skuid) {
                    $item->goodsicon = $value->goodsicon;
                    $item->goodsname = $value->goodsname;
                    $item->property .= $value->key_name . ':' . $value->value_name . " ";
                }
            }
        }
        return view('admin.order.index', ['orders' => $orders]);
    }

    public function modOrder(Request $request) {
        $order_no = $request->input('order_no');
        $express_company = $request->input('express_company');
        $express_no = $request->input('express_no');
        if (empty($order_no) || empty($express_company) || empty(($express_no)))
            return response()->json(['rs'=>0, 'errmsg' => '信息不全']);
        $rs = DB::table('orderinfo')->where('order_no', $order_no)->update([
            'express_company' => $express_company,
            'express_no' => $express_no,
            'status' => self::ORDER_HAS_SEND,
            'send_time' => date("Y-m-d H:i:s")
        ]);
        return resoponse()->json(['rs' => $rs]);
    }
}