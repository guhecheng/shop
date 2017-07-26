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

    public function index(Request $request) {
        $status = empty($request->input('status')) ? $this->state['ORDER_HAS_PAY'] : $request->input('status');
        $start_date = !empty($request->input('start_date')) ? date("Y-m-d", strtotime($request->input('start_date'))) : '';
        $end_date = !empty($request->input('start_date')) ? date("Y-m-d", strtotime($request->input('end_date'))) : '';
        $where[] = ['orderinfo.status', '=', $status];
        var_dump($start_date);
        if (!empty($start_date)) {
            $where[] = ['orderinfo.pay_time', '>=', $start_date];
        }
        if (!empty($end_date)) {
            $where[] = ['orderinfo.pay_time', '<=', $end_date];
        }
        $orders = DB::table('orderinfo')->leftJoin('order', 'order.order_no', '=', 'orderinfo.order_no')
            ->select('orderinfo.*', 'order.count', 'order.price as per_price', 'order.skuid')
            ->where($where)->orderBy('order.order_no', 'asc')->get();

        if (empty($orders))
            return view('admin.order.index', ['orders' => null, 'status' => $status, 'start_date'=>$start_date, 'end_date'=>$end_date]);
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
            return view('admin.order.index', ['orders' => null, 'status' => $status, 'start_date'=>$start_date, 'end_date'=>$end_date]);

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

        return view('admin.order.index', ['orders' => $orders, 'status' => $status, 'start_date'=>$start_date, 'end_date'=>$end_date]);
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
        $status = empty($request->input('status')) ? $this->state['ORDER_HAS_PAY'] : $request->input('status');
        $orders = DB::table('orderinfo')->leftJoin('order', 'order.order_no', '=', 'orderinfo.order_no')
            ->where('orderinfo.status', $status)->orderBy('order.order_no', 'asc')->get();

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
        $data[] = ['订单号', '物品名', '物品属性', '购买数量', '支付金额', '收货人', '联系方式', '联系地址', '支付时间'];
        foreach ($orders as $order) {
            $data[] = [
                $order->order_no,
                $order->goodsname,
                $order->property,
                $order->count,
                $order->price * $order->count / 100,
                $order->recv_name,
                $order->location,
                $order->pay_time
            ];
        }
        Excel::create('订单', function ($excel) use ($data) {
            $excel->setTitle('订单');
            $excel->sheet('未发货订单', function($sheet) use ($data) {
                $sheet->rows($data);
            });
        })->download('xls');

    }
}