<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/15
 * Time: 21:08
 */
namespace App\Http\Controllers;

use Illuminate\Pagination\Paginator;
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
        $uid = $request->session()->get('uid');
        $status = $request->input('status');   // 订单状态
        $pagesize = 5;          // 获取数据条数
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
                ['uid', '=', $uid]
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
                        ->select('goodsname', 'goodsicon', 'goodsid')->get();
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
                }
            }
            $item->property = '';
            foreach ($property as $v) {
                if ($v->sku_id == $item->skuid) {
                    $item->property .= $v->key_name . ':' . $v->value_name . ' ';
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
        if ($request->ajax()) {
            return response()->json(['orders' => $orders]);
        } else
            return view('order.list', ['orders' => $orders, 'status'=>$status]);
    }

    public function create(Request $request) {
        $car_ids = $request->input('car_ids');
        $uid = $request->session()->get("uid");
        $count = DB::table('orderinfo')->where('create_time', '>=', date("Y-m-d"))
            ->count();
        $orderno = date("YmdHis") . mt_rand(100, 200) . ($count + 1);
        $user = DB::table('user')->where('userid', $uid)->first();
        $discount =  $user->level == 0 ? 0 : ($user->level == 1 ? 90 : ($user->level == 2 ? 85 : 80));  // 对应折扣
        if ($request->input('skuid')) {
            $sql = "select goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price, goods.is_discount,
                    goodssku.num, goodssku.price as sku_price, goods.goodsid,goodssku.sku_id from goods left join goodssku on goods.goodsid=goodssku.goods_id
                    where goods.goodsid=".$request->input('goodsid') . " and goodssku.sku_id=".$request->input('skuid');
            $goods = DB::select($sql);
        } else {
            $sql = "select cart.*, goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price, goods.is_discount,
                goodssku.num, goodssku.price as sku_price,goodssku.sku_id from cart left join goods on goods.goodsid=cart.goodsid 
                left join goodssku on goodssku.sku_id=cart.skuid where cart.uid=$uid and cart.is_delete=0 and 
                cart.cartid in (".rtrim($car_ids,',').")";
            $goods = DB::select($sql);
        }
        if ($goods) {
            $price = $discount_price = 0;
            foreach ($goods as &$item) {
                $true_price = empty($item->sku_price) ? $item->sku_price : $item->price;
                if ($item->is_discount) {
                    $true_discount = $discount / 100;
                    $discount_price += $true_price * (1 - $true_discount) / 100;
                    $price += $true_price * $true_discount  / 100;
                } else {
                    $price += $true_price;
                }
                DB::table('order')->insert([
                    'order_no' => $orderno,
                    'skuid' => $item->sku_id,
                    'goodsid' => $item->goodsid,
                    'count' => empty($request->input('num')) ? $item->num : $request->input('num'),
                    'price' => $true_price
                ]);
            }
            DB::table("orderinfo")->insert([
                'order_no' => $orderno,
                'uid' => $uid,
                'price' => $price,
                'discount' => $discount,
                'discount_price' => $discount_price
            ]);
        }
        return redirect('/order/orderpay?orderno=' . $orderno);

    }
    public function orderpay(Request $request) {
        $address_id = $request->input('address_id');
        $orderno = $request->input('orderno');
        $uid = $request->session()->get("uid");
        if (empty($orderno)) {
            exit('订单已失效');
        }
        if (!empty($address_id)) {
            $address = DB::table('useraddress')->where('address_id', $address_id)->first();
        } else {
            $address = DB::table('useraddress')->where([
                ['uid', '=', $uid],
                ['is_default', '=', 1]
            ])->first();
        }
        $order = DB::table('orderinfo')->where('order_no', $orderno)->first();

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
                    $item->property .= $value->key_name . ':' . $value->value_name . " ";
                }
            }
        }

        $user = DB::table('user')->where('userid', $request->session()->get('uid'))->first();
        return view('order.create', ['goods' => $goods, 'user'=>$user, 'address'=>$address, 'orderno' => $orderno]);
    }
    public function add(Request $request) {
        $orderno = $request->input('orderno');
        $address_id = $request->input('address_id');
        $address = DB::table('useraddress')->where('address_id', $address_id)->first();
        DB::table('orderinfo')->where(['order_no', $orderno])->update([
            'recv_name' => $address->name ,
            'phone' => $address->phone ,
            'location' => $address->address . $address->location,
            'status' => $this->state['ORDER_WAIT_PATY']
        ]);
        /*$attributes = [
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
        }*/
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

    public function show(Request $request) {
        $orderno = $request->input('orderno');
        if (empty($orderno)) {
            exit('订单已失效');
        }
        $order = DB::table('orderinfo')->where('order_no', $orderno)->first();

        $goods = DB::table('order')->where('order.order_no', $orderno)->get();
        $skuids = '';
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
                    $item->property .= $value->key_name . ':' . $value->value_name . " ";
                }
            }
        }

        $user = DB::table('user')->where('userid', $request->session()->get('uid'))->first();
        return view('order.show', ['order'=>$order, 'goods'=>$goods, 'user'=>$user]);
    }


    /**
     * 订单列表
     */
    public function listorder(Request $request) {

    }

}