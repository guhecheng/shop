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
use Excel;

class OrderController extends Controller
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
            ->where('orderinfo.status', $this->state['ORDER_HAS_PAY'])->orderBy('order.order_no', 'asc')->get();

        if (empty($orders))
            return view('admin.order.index', ['orders' => null]);
        // 没有获取到数据
        $skuids = $order_no_items = [];
        foreach ($orders as $item) {
            $skuids[] = $item->skuid;
            if (key_exists($item->order_no, $order_no_items)) {
                $order_no_items[$item->order_no] += 1;
            } else
                $order_no_items[$item->order_no] = 1;
        }

        if (empty($skuids))
            return view('admin.order.index', ['orders' => null]);

        $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                left join goods on goods.goodsid=gp.goods_id
                where gp.sku_id in (".implode(',', $skuids).")";
        $skus = DB::select($sql);
        foreach ($orders as &$item) {
            foreach ($order_no_items as $k=>$v) {
                if ($k == $item->order_no) {
                    $item->times = $v;
                }
            }
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

    public function send(Request $request) {
        $order_no = $request->input('order_no');
        $express_company = $request->input('express_company');
        $express_no = $request->input('express_no');
        if (empty($order_no) || empty($express_company) || empty(($express_no)))
            return response()->json(['rs'=>0, 'errmsg' => '信息不全']);

        $rs = DB::table('orderinfo')->where('order_no', $order_no)->update([
            'express_company' => $express_company,
            'express_no' => $express_no,
            'status' => $this->state['ORDER_HAS_SEND'],
            'send_time' => date("Y-m-d H:i:s")
        ]);
        return response()->json(['rs' => $rs]);
    }

    public function export(Request $request) {
        $orders = DB::table('orderinfo')->leftJoin('order', 'order.order_no', '=', 'orderinfo.order_no')
            ->where('orderinfo.status', $this->state['ORDER_HAS_PAY'])->orderBy('order.order_no', 'asc')->get();

        if (empty($orders))
            return view('admin.order.index', ['orders' => null]);
        // 没有获取到数据
        $skuids = $order_no_items = [];
        foreach ($orders as $item) {
            $skuids[] = $item->skuid;
            if (key_exists($item->order_no, $order_no_items)) {
                $order_no_items[$item->order_no] += 1;
            } else
                $order_no_items[$item->order_no] = 1;
        }

        if (empty($skuids))
            return view('admin.order.index', ['orders' => null]);

        $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                left join goods on goods.goodsid=gp.goods_id
                where gp.sku_id in (".implode(',', $skuids).")";
        $skus = DB::select($sql);
        foreach ($orders as &$item) {
            foreach ($order_no_items as $k=>$v) {
                if ($k == $item->order_no) {
                    $item->times = $v;
                }
            }
            $item->property = '';
            foreach ($skus as $value) {
                if ($value->sku_id == $item->skuid) {
                    $item->goodsicon = $value->goodsicon;
                    $item->goodsname = $value->goodsname;
                    $item->property .= $value->key_name . ':' . $value->value_name . " ";
                }
            }
        }
        Excel::create('订单', function ($excel) use ($orders) {
            $excel->setTitle('订单');
            $excel->sheet('未发货订单', function($sheet) use ($orders) {
                $sheet->fromArray($orders);
            });
        })->downlad('xls');

    }
}