<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/15
 * Time: 21:08
 */
namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Payment\Order;
use EasyWeChat\Foundation\Application;

class OrderController extends Controller {
    private $state = [
        'ORDER_CREATE' => 0 ,
        'ORDER_WAIT_PATY' => 1,
        'ORDER_HAS_PAY' => 2,
        'ORDER_WAIT_SEND' => 3,
        'ORDER_HAS_SEND' => 4,
        'ORDER_HAS_RECV' => 5
    ];
    private $pay_type = [
        'WX_PAY' => 0,
    ];
    private $app;
    private $payment;

    public function __construct() {
        $options = [
            // 前面的appid什么的也得保留哦
            'app_id' => 'xxxx',
            // ...
            // payment
            'payment' => [
                'merchant_id'        => 'your-mch-id',
                'key'                => 'key-for-signature',
                'cert_path'          => 'path/to/your/cert.pem', // XXX: 绝对路径！！！！
                'key_path'           => 'path/to/your/key',      // XXX: 绝对路径！！！！
                'notify_url'         => '默认的订单回调地址',       // 你也可以在下单时单独设置来想覆盖它
                // 'device_info'     => '013467007045764',
                // 'sub_app_id'      => '',
                // 'sub_merchant_id' => '',
                // ...
            ],
        ];
        $this->app = new Application($options);
        $payment = $this->app->payment;
    }

    public function index(Request $request) {
        $uid = $request->session()->get("uid");
        $state = $request->input('state');

    }

    public function create(Request $request) {
        $uid = $request->session()->get("uid");
        $car_ids = $request->input('car_ids');
        $address_id = $request->input('address_id');
        if (empty($uid)) {
            exit;
        }
        if (!empty($address_id)) {
            $address = DB::table('useraddress')->where('address_id', $address_id)->first();
        } else {
            $address = DB::table('useraddress')->where([
                ['uid', '=', $uid],
                ['is_default', '=', 1]
            ])->first();
        }
        if ($request->input('skuid')) {
            $sql = "select goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price,
                    goodssku.num, goodssku.price as sku_price, goods.goodsid from goods left join goodssku on goods.goodsid=goodssku.goods_id
                    where goods.goodsid=".$request->input('goodsid') . " and goodssku.sku_id=".$request->input('skuid');
            $goods = DB::select($sql);
        } else {
            $cars = DB::table('cart')
                ->leftJoin("goods", "goods.goodsid", '=', 'cart.goodsid')
                ->leftJoin("goodssku", 'goodssku.sku_id', '=', 'cart.skuid')
                ->select('cart.*', 'goods.goodsname', 'goods.goodsicon', 'goods.is_delete as goods_delete', 'goods.price',
                    'goodssku.num', 'goodssku.price as sku_price')
                ->where([
                    ['uid', '=', $uid],
                    ['cart.is_delete', '=', 0]
                ])
                ->get();
            $sql = "select cart.*, goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price,
                goodssku.num, goodssku.price as sku_price from cart left join goods on goods.goodsid=cart.goodsid 
                left join goodssku on goodssku.sku_id=cart.skuid where cart.uid=$uid and cart.is_delete=0 and 
                cart.cartid in (".rtrim($car_ids,',').")";
            $goods = DB::select($sql);
        }
        if ($goods) {
            if ($car_ids) {
                $skuids = [];
                foreach ($cars as $item) {
                    $skuids[] = $item->skuid;
                }

                $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                where gp.sku_id in (".implode(',', $skuids).")";
                $skus = DB::select($sql);
                foreach ($goods as &$item) {
                    $item->property = '';
                    foreach ($skus as $value) {
                        if ($value->sku_id == $item->skuid) {
                            $item->property .= $value->key_name . ':' . $value->value_name . " ";
                        }
                    }
                }
            } else {
                $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                where gp.sku_id =" .$request->input('skuid');
                $skus = DB::select($sql);

                foreach ($goods as &$item) {
                    $item->property = '';
                    $item->cartid = '';
                    $item->goodscount = intval($request->input('num'));
                    foreach ($skus as $value) {
                        if ($value->goods_id == $item->goodsid) {
                            $item->property .= $value->key_name . ':' . $value->value_name . " ";
                            $item->skuid = $value->sku_id;
                        }
                    }
                }
            }
        }
        $user = DB::table('user')->where('userid', $uid)->first();
        return view('order.create', ['goods' => $goods, 'address' => $address, 'user' => $user]);
    }

    public function add(Request $request) {
        $uid = $request->session()->get('uid');
        $goodsid = $request->input('goodsid');
        $num = $request->input('num');
        $skuid = $request->input('skuid');
        $cartid =$request->input('cartid');
        $addressid = $request->input('addressid');
        if ($cartid) {

        }
        $count = DB::table('orderinfo')->where('create_time', '>=', date("Y-m-d"))
                     ->count();
        $orderno = date("YmdHis") . mt_rand(100, 200) . ($count + 1);
        $level = DB::table('user')->where('userid', $uid)->select('level')->first();
        $discount =  $level == 0 ? 0 : ($level == 1 ? 90 : ($level == 2 ? 85 : 80));
        $sql = "select price,goods_id, sku_id from goodssku where skuid in (".implode(',', $skuid).")";
        $skus = DB::select($sql);
        $price = 0;
        foreach ($skus as $key=>$value) {
            $price += $value->price * $num[$key];
        }
        DB::table("orderinfo")->insert([
            'order_no' => $orderno,
            'price' => $price,
            'discount' => $discount,
            'discount_price' => $price * $discount / 100
        ]);

        $address = DB::table('useraddress')->where('address_id', $addressid)->first();
        foreach ($skus as $key=>$value) {
            DB::table('order')->insert([
                'order_no' => $orderno,
                'recv_name' => $address->name,
                'phone' => $address->phone,
                'location' => $address->address . $addressid->location,
                'skuid' => $value->sku_id,
                'count' => $num[$key],
                'price' => $value->price
            ]);
        }

        $attributes = [
            'trade_type'       => 'JSAPI', // JSAPI，NATIVE，APP...
            'body'             => 'iPad mini 16G 白色',
            'detail'           => 'iPad mini 16G 白色',
            'out_trade_no'     => '1217752501201407033233368018',
            'total_fee'        => $price, // 单位：分
            'notify_url'       => 'http://xxx.com/order-notify', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            'openid'           => '当前用户的 openid', // trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，
            // ...
        ];
        $order = new Order($attributes);

        $result = $this->payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        }
    }


    public function wxpaynotify(Request $request) {
        $response = $this->app->payment->handleNotify(function($notify, $successful){
            // 使用通知里的 "微信支付订单号" 或者 "商户订单号" 去自己的数据库找到订单
            $order = 查询订单($notify->out_trade_no);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->paid_at) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                // 不是已经支付状态则修改为已经支付状态
                $order->paid_at = time(); // 更新支付时间为当前时间
                $order->status = 'paid';
            } else { // 用户支付失败
                $order->status = 'paid_fail';
            }
            $order->save(); // 保存订单
            return true; // 返回处理完成
        });
        return $response;
    }


}