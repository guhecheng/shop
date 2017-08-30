<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/15
 * Time: 21:08
 */
namespace App\Http\Controllers;

use model\parter\emp\emp;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Payment\Order;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller {
    private $state = [
        'ORDER_CREATE' => 0 ,
        'ORDER_WAIT_PAY' => 1,
        'ORDER_WAIT_SEND' => 2,
        'ORDER_HAS_SEND' => 3,
        'ORDER_HAS_RECV' => 4,
        'ORDER_HAS_LOST' => 5
    ];
    private $pay_type = [
        'WX_PAY' => 0,
        'CARD_PAY' => 1
    ];
    private $app;
    private $payment;

    public function __construct() {
        $this->app = new Application(config('wx'));
    }

    public function index(Request $request) {
        $uid = $request->session()->get('uid');
        $status = $request->input('status');   // 订单状态

        $orders = $this->getGoods($status, $uid, $pagesize = 10);

        if ($request->ajax()) {
            return response()->json(['orders' => $orders]);
        } else
            return view('order.list', ['orders' => $orders, 'status'=>$status]);
    }

    public function ajaxGetGoods(Request $request) {
        $uid = $request->session()->get('uid');
        $status = $request->input('status');   // 订单状态

        $orders = $this->getGoods($status, $uid, $pagesize = 10);
        if ($request->ajax()) {
            return response()->json(['orders' => $orders]);
        }
    }

    private function getGoods($status, $uid, $pagesize) {
        if ($status == 1) {
            $where = [
                ['uid', '=', $uid],
                ['status', '<=', 1]
            ];
        } else if ($status == 2) {
            $where = [
                ['uid', '=', $uid],
                ['status', '=', 2]
            ];
        } else if ($status == 3) {
            $where = [
                ['uid', '=', $uid],
                ['status', '>=', 4]
            ];
        } else if (empty($status) || $status == -1) {

            $where = [
                ['uid', '=', $uid],
                ['status', '>', 0]
            ];
        } else {

        }
        $orders = DB::table('orderinfo')->where($where)
            ->orderBy('create_time', 'desc')->limit($pagesize)->get();
        if ($orders->isEmpty()) {
            return view('order.list', ['orders' => $orders, 'status'=>$status]);
        }
        $order_no = [];
        foreach ($orders as $order) {
            $order_no[] = $order->order_no;
        }
        $data = DB::table('order')->whereIn('order_no', $order_no)->get();
        $goodsid = [];
        $skuids = [];
        foreach ($data as $value) {
            $goodsid[] = $value->goodsid;
            $skuids[] = $value->skuid;
        }
        // 获取商品姓名
        $goods = DB::table('goods')->whereIn('goodsid', $goodsid)
            ->select('goodsname', 'goodsicon', 'goodsid', 'is_delete')->get();
        // 获取属性
        $property = DB::table('goodsproperty')
            ->leftJoin('propertykey', 'propertykey.key_id', '=', 'goodsproperty.key_id')
            ->leftJoin('propertyvalue', 'propertyvalue.value_id', '=', 'goodsproperty.value_id')
            ->whereIn('goodsproperty.sku_id', $skuids)
            ->select('goodsproperty.goods_id', 'goodsproperty.sku_id', 'propertykey.key_name', 'propertyvalue.value_name')
            ->get();
        foreach ($data as &$item) {
            foreach ($goods as $value) {
                if ($item->goodsid = $value->goodsid) {
                    $item->goodsname = $value->goodsname;
                    $item->goodsicon = $value->goodsicon;
                    $item->goodsdelete = $value->is_delete;
                }
            }
            $item->property = '';
            foreach ($property as $v) {
                if ($v->sku_id == $item->skuid) {
                    $item->property .= $v->value_name . ' ';
                }
            }
        }
        foreach ($orders as &$item) {
            foreach ($data as $value) {
                if ($item->order_no == $value->order_no) {
                    $item->data[] = $value;
                }
            }
        }
        
        return $orders;
    }

    public function create(Request $request) {
        $car_ids = $request->input('car_ids');
        $uid = $request->session()->get("uid");
        $count = DB::table('orderinfo')->where('create_time', '>=', date("Y-m-d"))
            ->count();
        $orderno = date("Ymd") . mt_rand(1000, 9999) . ($count + 1);

        if ($request->input('skuid')) {
            $sql = "select goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price, goods.is_discount,goods.brand_id,
                    goods.common_discount, goods.ordinary_discount, goods.golden_discount, goods.platinum_discount, goods.diamond_discount,
                    goodssku.num, goodssku.price as sku_price, goods.goodsid,goodssku.sku_id from goods left join goodssku on goods.goodsid=goodssku.goods_id
                    where goods.goodsid=".$request->input('goodsid') . " and goodssku.sku_id=".$request->input('skuid');
            $goods = DB::select($sql);
        } else {
            $sql = "select cart.*, goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price, goods.is_discount,goods.brand_id,
                goods.common_discount, goods.ordinary_discount, goods.golden_discount, goods.platinum_discount, goods.diamond_discount,
                goodssku.num, goodssku.price as sku_price,goodssku.sku_id from cart left join goods on goods.goodsid=cart.goodsid 
                left join goodssku on goodssku.sku_id=cart.skuid where cart.uid=$uid and cart.is_delete=0 and 
                cart.cartid in (".rtrim($car_ids,',').")";
            $goods = DB::select($sql);
        }
        $user = DB::table("user")->where('userid', $uid)->first();
        if ($goods) {
            foreach ($goods as &$item) {
                $true_price = !empty($item->sku_price) ? $item->sku_price : $item->price;
                if ($user->level == 0) {
                    $real_price = empty($item->common_discount) ? $true_price : $true_price * $item->common_discount / 100;
                } else if ($user->level == 1) {
                    $real_price = empty($item->ordinary_discount) ? $true_price : $true_price * $item->ordinary_discount / 100;
                } else if ($user->level == 2) {
                    $real_price = empty($item->ordinary_discount) ? $true_price : $true_price * $item->golden_discount / 100;
                } else if ($user->level == 3) {
                    $real_price = empty($item->platinum_discount) ? $true_price : $true_price * $item->platinum_discount / 100;
                } else if ($user->level == 4) {
                    $real_price = empty($item->diamond_discount) ? $true_price : $true_price * $item->diamond_discount / 100;
                }
                $count = empty($request->input('num')) ? $item->goodscount : $request->input('num');
                DB::table('order')->insert([
                    'order_no' => $orderno,
                    'skuid' => $item->sku_id,
                    'goodsid' => $item->goodsid,
                    'count' => $count,
                    'price' => $true_price,
                    'real_price' => $real_price
                ]);
                if (($sku = DB::table('goodssku')->where('sku_id', $item->sku_id)->first()) && $sku->num >= $count) {
                    DB::table('goodssku')->where('sku_id', $item->sku_id)->update(['num' => $sku->num - $count]);
                } else {
                    return redirect('/orderpay?errmsg=1');
                }
            }
            DB::table("orderinfo")->insert([
                'order_no' => $orderno,
                'uid' => $uid,
                'car_ids' => empty($car_ids) ? '' : $car_ids
            ]);
        }
        //DB::table("cart")->whereIn('cartid', $car_ids)->update(['is_delete', 0]);
        return redirect('/orderpay?orderno=' . $orderno);

    }
    public function orderpay(Request $request) {
        if ($request->input('errmsg') == 1) {
            exit('商品已经抢购一空,下次再来');
        }
        $address_id = $request->input('address_id');
        $orderno = $request->input('orderno');
        $uid = $request->session()->get("uid");
        if (empty($orderno)) {
            exit('订单已失效');
        }
        // 判断订单是否支付
        $order = DB::table('orderinfo')->where('order_no', $orderno)->first();
        if ($order->status != $this->state['ORDER_CREATE'])
            return redirect('/ordershow?orderno=' . $order->order_no);
        if (!empty($address_id)) {
            $address = DB::table('useraddress')->where('address_id', $address_id)->first();
        } else {
            $address = DB::table('useraddress')->where([
                ['uid', '=', $uid],
                ['is_default', '=', 1]
            ])->first();
        }

        $goods = DB::table('order')->where('order.order_no', $orderno)->get();
        $skuids = [];
        foreach ($goods as $item) {
            $skuids[] = $item->skuid;
        }
        $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                left join goods on goods.goodsid=gp.goods_id
                where gp.sku_id in (".implode(',', $skuids).")";
        $skus = DB::select($sql);
        $brand_ids = [];
        foreach ($goods as &$item) {
            $item->property = '';
            foreach ($skus as $value) {
                if ($value->sku_id == $item->skuid) {
                    $item->goodsicon = $value->goodsicon;
                    $item->goodsname = $value->goodsname;
                    $item->is_discount = $value->is_discount;
                    $item->brand_id = $value->brand_id;
/*                    $item->common_discount = $value->common_discount;
                    $item->ordinary_discount = $value->ordinary_discount;
                    $item->golden_discount = $value->golden_discount;
                    $item->platinum_discount = $value->platinum_discount;
                    $item->diamond_discount = $value->diamond_discount;*/
                    $item->property .= $value->value_name . " ";
                    $item->brands = DB::table('brands')->where('id', $value->brand_id)->first();
                }
            }
        }
        $user = DB::table('user')->where('userid', $request->session()->get('uid'))->first();
        return view('order.create', ['goods' => $goods, 'user'=>$user, 'address'=>$address, 'orderno' => $orderno, 'order'=>$order]);
    }
    public function add(Request $request) {
        $orderno = $request->input('orderno');
        $address_id = $request->input('address_id');
        if (empty($orderno) || empty($address_id))
            return response()->json(['rs' => 0]);
        $address = DB::table('useraddress')->where('address_id', $address_id)->first();
        $rs = DB::table('orderinfo')->where(['order_no', $orderno])->update([
            'recv_name' => $address->name,
            'phone' => $address->phone ,
            'location' => $address->address . $address->location,
            'status' => $this->state['ORDER_WAIT_PAY']
        ]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function pay(Request $request) {
        $address_id = $request->input('address_id');
        $order_no = $request->input('order_no');
        $express_price = $request->input('express_price');
        $score = $request->input('score');
        $price = $request->input('price');
        $coupon_id = $request->input('coupon_id');
        $coupon_discount = $request->input('coupon_discount');
        if (empty($address_id)) {
            return response()->json(['rs' => 0, 'errmsg' => "请先选择地址"]);
        }
        if( empty($order_no) || empty($price)) {
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }
        try {
            DB::beginTransaction();
            $order = DB::table('orderinfo')->where('order_no', $order_no)->first();
            if (!$order || $order->status != $this->state['ORDER_CREATE']) {
                throw new \Exception("订单不存在");
            }

            $address = DB::table('useraddress')->where('address_id', $address_id)->first();
            if (!$address) {
                throw new \Exception("地址信息错误");
            }
            $user = DB::table("user")->where("userid", $order->uid)->first();
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            $rs = DB::table('orderinfo')->where('info_id', $order->info_id)->update([
                'pay_type' => 0,
                'status' => $this->state['ORDER_WAIT_PAY'],
                'pay_time' => date("Y-m-d H:i:s"),
                'price' => $price * 100,
                'score' => empty($score) ? 0 : $score,
                'express_price' => empty($express_price) ? 0 : $express_price,
                'recv_name' => $address->name,
                'phone' => $address->phone,
                'location' => $address->location,
                'coupon_id' => empty($coupon_id) ? 0 : $coupon_id
            ]);
            if (!$rs)
                throw new \Exception('修改订单状态失败');

            if (!empty($coupon_id)) {
                DB::table('user_coupon')->where(['user_id'=>$request->session()->get("uid"), "coupon_id" => $coupon_id])->update(['status'=>1]);
            }
            if (!empty($score)) {
                if ($user->score < $score)
                    throw new \Exception('积分不够');

                DB::table('user')->where('userid', $user->userid)->decrement('score', $score);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }

        $app = new Application(config('wx'));
        $payment = $app->payment;
        $openid = $request->session()->get('openid');
        $attributes = [
            'trade_type'    =>  'JSAPI',
            'body'          =>  '商品购买',
            'detail'        =>  '购买商品',
            'out_trade_no'  =>  $order_no,
            'total_fee'     => $price * 100,
            /*'total_fee'     => $money * 100,*/
            'notify_url'    => 'http://www.jingyuxuexiao.com/order/wxnotify',
            'openid'    => $openid
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            exit($payment->configForPayment($prepayId));
        }
        return response()->json(['rs' => 0, 'errmsg' => "网络出现异常"]);
    }

    public function wxnotify(Request $request) {
        $response = $this->app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = DB::table('orderinfo')->where('order_no', $notify->out_trade_no)->first();
            if (!$order) { // 如果订单不存在
                return 'Order not exist.';
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->status == $this->state['ORDER_WAIT_SEND']) {
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                DB::beginTransaction();
                try {
                    $info = DB::table('orderinfo')->where('info_id', $order->info_id)->update([
                        'status' => $this->state['ORDER_WAIT_SEND'],
                        'pay_time' => date("Y-m-d H:i:s"),
                    ]);
                    if (!$info) {
                        throw new \Exception("修改订单状态失败");
                    }
                    if (!empty($info->car_ids)) {
                        $rs = DB::table('cart')->whereIn('cartid', explode(",", rtrim($order->car_ids, ',')))->update([
                            'is_delete' => 1
                        ]);
                        if (!$rs)
                            throw new \Exception("删除购物车失败");
                    }
                    $trans['insert'] = DB::table('usertransmoney')->insert([
                        'uid'   => $order->uid,
                        'trans_type'    => 0,
                        'trans_money' => $order->price,
                        'order_no' => $notify->out_trade_no
                    ]);
                    if (!$trans['insert'])
                        throw new \Exception('操作失败');

                    $user = DB::table('user')->where("userid", $order->uid)->first();
                    if (empty($user)) {
                        throw new \Exception('用户不存在');
                    } /*else
                        DB::table('user')->where('userid', $order->uid)->decrement('score', $order->score);*/
                    if (!empty($score)) DB::table('scorechange')->insert([ 'uid'=>$order->uid, 'score'=>$info->score, 'type'=>2, 'paytype'=>1 ]);

                    DB::commit();
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    DB::rollback();
                }
            } else { // 用户支付失败
                return false;
            }
            return true; // 返回处理完成
        });
        return $response;
    }

    public function cardpay(Request $request) {
        $address_id = $request->input('address_id');
        $order_no = $request->input('order_no');
        $express_price = $request->input('express_price');
        $score = $request->input('score');
        $price = $request->input('price');              // 金额
        $coupon_id = $request->input('coupon_id');      // 优惠券id

        if (empty($address_id) || empty($order_no) || empty($price)) {
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }

        DB::beginTransaction();
        try {
            $order = DB::table('orderinfo')->where('order_no', $order_no)->first();
            if (!$order || $order->status >= $this->state['ORDER_WAIT_SEND']) {
                throw new \Exception("订单不存在");
            }
            if (!empty($order->car_ids)) {
                $rs = DB::table('cart')->whereIn('cartid', explode(",", rtrim($order->car_ids, ',')))->update([
                    'is_delete' => 1
                ]);
                if (!$rs)
                    throw new \Exception("删除购物车失败");
            }
            if (! $address = DB::table('useraddress')->where('address_id', $address_id)->first())
                throw new \Exception("地址信息错误");

            if (!($user = DB::table("user")->where("userid", $order->uid)->first())) {
                throw new \Exception('用户不存在');
            }
            $rs = DB::table('orderinfo')->where('info_id', $order->info_id)->update([
                'pay_type' => 1,
                'status' => $this->state['ORDER_WAIT_SEND'],
                'pay_time' => date("Y-m-d H:i:s"),
                'price' => $price * 100,
                'score' => empty($score) ? 0 : $score,
                'express_price' => empty($express_price) ? 0 : $express_price,
                'recv_name' => $address->name,
                'phone' => $address->phone,
                'location' => $address->location,
                'coupon_id' => empty($coupon_id) ? 0 : $coupon_id
            ]);

            if (!$rs)
                throw new \Exception('修改订单状态失败');

            $trans['insert'] = DB::table('usertransmoney')->insert([
                'uid'   => $order->uid,
                'trans_type'    => 0,
                'trans_money' => $order->price,
                'order_no' => $order_no
            ]);
            if (!$trans['insert'])
                throw new \Exception('操作失败');

            if (!empty($price)) {
                if (!DB::table('user')->where('userid', $order->uid)->update([
                        'money' => $user->money - $price * 100,
                        'score' => $user->score - $score
                    ]))
                    throw new \Exception('修改订单状态失败');
            }

            if (!empty($coupon_id)) {
                DB::table('user_coupon')->where(['user_id' => $order->uid, 'coupon_id' => $coupon_id])->update(['status' => 1]);
            }
            if (!empty($score)) {
                if ($user->score < $score)
                    throw new \Exception('积分不够');

                DB::table('scorechange')->insert([ 'uid'=>$order->uid, 'score'=>$score, 'type'=>2, 'paytype'=>2 ]);
            }
            DB::commit();
            return response()->json(['rs' => 1]);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }
    }

    public function show(Request $request) {
        $orderno = $request->input('orderno');
        if (empty($orderno)) {
            exit('订单已失效');
        }
        $order = DB::table('orderinfo')->leftJoin('coupon', 'coupon.id', '=', 'orderinfo.coupon_id')
            ->select('orderinfo.*', 'coupon.discount_price')->where('order_no', $orderno)->first();
        $goods = DB::table('order')->where('order.order_no', $orderno)->get();
        $skuids = [];
        foreach ($goods as $item) {
            $skuids[] = $item->skuid;
        }
        $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                left join goods on goods.goodsid=gp.goods_id
                where gp.sku_id in (".implode(',', $skuids).")";
        $skus = DB::select($sql);
        foreach ($goods as &$item) {
            $item->property = '';
            foreach ($skus as $value) {
                if ($value->sku_id == $item->skuid) {
                    $item->goodsicon = $value->goodsicon;
                    $item->goodsname = $value->goodsname;
                    $item->property .= $value->value_name . " ";
                    $item->common_discount = $value->common_discount;
                    $item->ordinary_discount = $value->ordinary_discount;
                    $item->golden_discount = $value->golden_discount;
                    $item->platinum_discount = $value->platinum_discount;
                    $item->diamond_discount = $value->diamond_discount;
                }
            }
        }
        $coupon = DB::table('coupon')->where('id', $order->coupon_id)->first();
        $user = DB::table('user')->where('userid', $request->session()->get('uid'))->first();
        return view('order.show', ['order'=>$order, 'goods'=>$goods, 'user'=>$user, 'coupon' => $coupon]);
    }

    /**
     * \'address_id':address_id, 'score':score, 'express_price': express_price,
    'discount_price':discount_price, 'order_no':{{ $orderno }}
     * @param Request $request
     */
    public function freepay(Request $request) {
        $address_id = $request->input('address_id');
        $order_no = $request->input('order_no');
        $express_price = $request->input('express_price');
        $brand_id = $request->input('brand_id');
        $score = $request->input('score');
        $price = $request->input('price');
        $coupon_id = $request->input('coupon_id');
        if (empty($address_id) || empty($order_no) || empty($score)) {
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }
        try {
            DB::beginTransaction();
            $order = DB::table('orderinfo')->where('order_no', $order_no)->first();
            if (!$order || $order->status != $this->state['ORDER_CREATE']) {
                throw new \Exception("订单不存在");
            }
            if (!empty($order->car_ids)) {
                //$rs = DB::table('cart')->whereIn('cartid', rtrim($order->car_ids, ','))->update(['is_delete'=>1]);
                //$rs = DB::update("update cart set is_delete=1 where cartid in (?)" , [rtrim($order->car_ids, ',')]);
                $rs = DB::table('cart')->whereIn('cartid', explode(",", rtrim($order->car_ids, ',')))->update([
                    'is_delete' => 1
                ]);
                if (!$rs)
                    throw new \Exception("删除购物车失败");
            }
            $address = DB::table('useraddress')->where('address_id', $address_id)->first();
            if (!$address) {
                throw new \Exception("地址信息错误");
            }
            $user = DB::table("user")->where("userid", $order->uid)->first();
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            if ($user->score < $score)
                throw new \Exception('用户积分不够');

            $rs = DB::table('orderinfo')->where('info_id', $order->info_id)->update([
                'pay_type' => 2,
                'status' => $this->state['ORDER_WAIT_SEND'],
                'pay_time' => date("Y-m-d H:i:s"),
                'price' => $score,
                'express_price' => empty($express_price) ? 0 : $express_price,
                'recv_name' => $address->name,
                'score' => $score,
                'phone' => $address->phone,
                'location' => $address->location,
                'coupon_id' => $coupon_id
            ]);
            if (!$rs)
                throw new \Exception('修改订单状态失败');
            if (!empty($score)) {
                $rs = DB::table("scorechange")->insert([
                    'score' => $score,
                    'type' => 2
                ]);
            }
            if (!$rs)
                throw new \Exception("插入记录失败");

            if (!empty($score)) {
                if ($user && $user->score >= $score) {
                    $rs = DB::table('user')->where('userid', $order->uid)->update([
                        'score' => $user->score - $score
                    ]);
                    if (!$rs)
                        throw new \Exception('修改订单状态失败');
                }
            }
            DB::commit();
            return response()->json(['rs' => 1]);

        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }
    }

    public function repay(Request $request) {
        $order_no = $request->input('orderno');
        if (empty($order_no))
            return response()->json(['rs' => 0, 'errmsg' => '信息错误']);

        $order = DB::table('orderinfo')->where('order_no', $order_no)->first();
        if (!$order || $order->status != $this->state['ORDER_WAIT_PAY']) {
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }

        $app = new Application(config('wx'));
        $payment = $app->payment;
        $openid = $request->session()->get('openid');
        $attributes = [
            'trade_type'    =>  'JSAPI',
            'body'          =>  '商品购买',
            'detail'        =>  '购买商品',
            'out_trade_no'  =>  $order_no,
            'total_fee'     => $order->price,
            /*'total_fee'     => $money * 100,*/
            'notify_url'    => 'http://www.jingyuxuexiao.com/order/wxnotify',
            'openid'    => $openid
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            exit($payment->configForPayment($prepayId));
        }
        return response()->json(['rs' => 0, 'errmsg' => "网络出现异常"]);
    }
    /**
     * 订单列表
     */
    public function changeorder(Request $request) {
        $order_no = $request->input('orderno');
        $status = $request->input('status');
        if (empty($order_no) || empty($status)) {
            return response()->json(['rs'=>0, 'errmsg'=>'信息错误']);
        }
        $order = DB::table('orderinfo')->where('order_no', $order_no)->first();
        if (empty($order) || $order->status != $this->state['ORDER_HAS_SEND']) {
            return response()->json(['rs'=>0, 'errmsg'=>'订单状态错误']);
        }
        $rs = DB::table('orderinfo')->where('info_id', $order->info_id)->update(['status' => $this->state['ORDER_HAS_RECV']]);
        if (empty($rs)) {
            return response()->json(['rs'=>0, 'errmsg'=>'操作失败']);
        }
        return response()->json(['rs'=>1]);
    }

    /**
     * 获取对应用户的优惠券
     */
    public function getCoupons(Request $request) {
        $brand_ids = $request->input('brand_ids');
        $uid = $request->session()->get('uid');
        $price = $request->input('price');
        if (empty($brand_ids) || empty($uid) || empty($price)) exit;
        $brand_ids = explode(',', $brand_ids);
        $prices = explode(',', $price);
        $today = date("Y-m-d");
        $coupons = [];
        foreach ($brand_ids as $key=>$brand_id) {
            $where = "user_coupon.user_id={$uid} and coupon_brand.brand_id = ({$brand_id}) and status=0 and end_date>='{$today}' and start_date <='{$today}' and goods_price<={$prices[$key]}";
            $brands = DB::table('brands')->where('is_del', 0)->get();
            $sql = "select `user_coupon`.*, `coupon`.*, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where}) group by `coupon_brand`.`coupon_id` order by `coupon`.`start_date` desc";
            $rs = DB::select($sql);
            if (!empty($rs)) {
                $coupon = $rs[0];
                $ids = explode(',', $coupon->brand_ids);
                $brand_names = [];
                foreach ($brands as $brand) {
                    if (in_array($brand->id, $ids))
                        $brand_names[] = $brand->brand_name;
                }
                $coupon->brand_names = !empty($brand_names) ? implode(',', $brand_names) : '';
                $coupon->start_date = date("Y年m月d日", strtotime($coupon->start_date));
                $coupon->end_date = date("Y年m月d日", strtotime($coupon->end_date));
                $coupons[] = $coupon;
            }
        }
        return response()->json(['coupons'=>!empty($coupons) ? $coupons : '']);
    }

}
