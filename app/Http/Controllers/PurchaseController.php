<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/9/16
 * Time: 21:08
 */
namespace App\Http\Controllers;

use MongoDB\Driver\ReadConcern;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Payment\Order;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PurchaseController extends Controller {
    /**
     * 预定支付金额
     */
    const RESERVE_MONEY= 1;

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

    public function index(Request $request) {
        $order_no = base_convert(uniqid(),16,10).sprintf('%02d',rand(0,99));
        $app = new Application(config('wx'));
        $payment = $app->payment;
        $openid = $request->session()->get('openid');
        $attributes = [
            'trade_type'    =>  'JSAPI',
            'body'          =>  '代购商品',
            'detail'        =>  '购买商品',
            'out_trade_no'  =>  $order_no,
            'total_fee'     => 1,
            'notify_url'    => 'http://www.jingyuxuexiao.com/purchase/notify',
            'openid'    => $openid
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
        }
        return view('purchase.add', ['json' => empty($prepayId) ? '' : $payment->configForPayment($prepayId), 'order_no' => $order_no]);
    }

    /**
     * 增加信息
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request) {
        $goods_name = $request->input('goods_name');        // 代购商品名
        $goods_desc = $request->input('goods_desc');        // 代购商品简介
        $uid = $request->session()->get('uid');
        $concat_phone = $request->input('concat_phone');    // 代购人联系电话

        if (empty($goods_name))
            return response()->json(['rs'=>0, 'errmsg' => '商品名称不能为空']);
        if (empty($goods_desc))
            return response()->json(['rs'=>0, 'errmsg' => '缺少商品描述']);
        if (empty($concat_phone) || !preg_match("/^1[34578]{1}\d{9}$/", $concat_phone))
            return response()->json(['rs'=>0, 'errmsg' => '手机号码不对']);

        $file = $request->file('file');
        if (!empty($file) && $file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'goods/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool)
                $imgurl = '/uploads/' . $fileName;
        }
        $insert_rs = DB::table('user_purchase')->insertGetId([
            'uid' => $request->session()->get("uid"),
            'phone' => $concat_phone,
            'goods_name' => $goods_name,
            'goods_pic' => empty($imgurl) ? '' : $imgurl,
            'goods_desc' => $goods_desc,
            'prepay_order_no' => $request->input('order_no')
        ]);
        if ($insert_rs)
            return response()->json(['rs' => 1]);
        return response()->json(['rs' => 0, 'errmsg' => "网络出现异常"]);
    }

    /**
     * 预付款支付完成回填
     */
    public function notify() {
        $app = new Application(config('wx'));
        $response = $app->payment->handleNotify(function($notify, $successful){
            $order = DB::table('user_purchase')->where('prepay_order_no', $notify->out_trade_no)->first();
            //Log::info($notify->out_trade_no);
            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->is_pay == 1) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                DB::beginTransaction();

                // 不是已经支付状态则修改为已经支付状态
                try {
                    $order_rs = DB::table('user_purchase')->where('id', $order->id)
                        ->update(['is_pay' => 1, 'pay_time' => date("Y-m-d H:i:s"), 'transaction_id'=>$notify->transaction_id, 'pay_money'=>$notify->total_fee]);
                    if (!$order_rs) {
                        throw new \Exception('订单状态修改失败');
                    }
                    $trans['insert'] = DB::table('usertransmoney')->insert([
                        'uid'   => $order->uid,
                        'trans_type'    => 4,
                        'trans_money' => $order->pay_money,
                        'order_no' => $notify->out_trade_no
                    ]);
                    $user = DB::table('user')->where("userid", $order->uid)->first();
                    if (empty($user)) {
                        throw new \Exception('用户不存在');
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    DB::rollback();
                    return false;
                }
            } else { // 用户支付失败
                return false;
            }
            return true; // 返回处理完成
        });
        return $response;
    }

    /**
     * 展示商城选择页
     *
     * @param Request $request
     * @return mixed
     */
    public function select(Request $request) {
        $brand_id = $request->input('brand_id');
        $level = DB::table('user')->where('userid', $request->session()->get("uid"))->select('level')->first();
        return view('purchase.select', ['brand_id' => $brand_id, 'level' => $level]);
    }

    /**
     * 获取vip代购商品
     * @param Request $request
     * @return mixed
     */
    public function goods(Request $request) {
        $purchase_type = $request->input('purchase_type') ?: 1;   // 类型
        if ($purchase_type == 1)
            $goods = DB::table('user_purchase')->leftJoin('goods', 'goods.goodsid', '=', 'user_purchase.goods_id')
                ->where([
                ['goods_id', '!=', 0],
                ['is_pay', '=', 1],
                ['is_back', '=', 0],
                ['uid', '=', $request->session()->get("uid")]
            ])->get();
        else {
            $goods = DB::table('user_purchase')->leftJoin('orderinfo', 'orderinfo.order_no', '=', 'user_purchase.order_no')
                ->where([
                    ['is_create', '!=', 1],
                    ['is_pay', '>', 0]
                ])
                ->orderBy('user_purchase.create_time', 'desc')
                ->select('user_purchase.*')->get();

        }
        if ($request->ajax())
            return response()->json(['goods' => $goods]);
        return view('purchase.goods', ['goods' => $goods]);
    }

    public function detail(Request $request) {
        $purchase_id = $request->input('purchase_id');
        if (empty($purchase_id))
            exit('没有商品');
        $purchase = DB::table("user_purchase")->leftJoin('goods', 'goods.goodsid', '=', 'user_purchase.goods_id')
            ->where('id', $purchase_id)->first();
        if (empty($purchase))
            exit('商品已失效');
        else {

        }
        return view('purchase.detail', ['goods' => $purchase]);
    }

    /**
     * 支付页面
     * @param Request $request
     */
    public function pay(Request $request) {
        $purchase_id = $request->input('purchase_id');
        $uid = $request->session()->get('uid');
        if (empty($purchase_id) || empty($uid))
            exit('没有商品');
        $purchase = DB::table("user_purchase")->leftJoin('goods', 'goods.goodsid', '=', 'user_purchase.goods_id')
            ->where('id', $purchase_id)->first();
        if (empty($purchase))
            exit('订单不存在');

        if (empty($purchase->order_no)) {
            $count = DB::table('orderinfo')->where('create_time', '>=', date("Y-m-d"))
                ->count();
            $orderno = date("Ymd") . mt_rand(1000, 9999) . ($count + 1);
            $rs = DB::table("orderinfo")->insert([
                'order_no' => $orderno,
                'uid' => $uid,
                'is_comm' => 1
            ]);
            if ($rs)
                DB::table("user_purchase")->where('id', $purchase_id)->update(['order_no' => $orderno]);
        }
        $address = DB::table('useraddress')->where([
            ['uid', '=', $uid],
            ['is_default', '=', 1]
        ])->first();

        $user = DB::table('user')->where(['userid'=>$uid])->first();
        $goods[] = $purchase;
        return view('purchase.pay', ['goods'=>$goods, 'user' => $user, 'address' => $address, 'orderno'=>empty($orderno)?$purchase->order_no:$orderno]);
    }

    public function wxpay(Request $request) {
        $address_id = $request->input('address_id');
        $order_no = $request->input('order_no');
        $express_price = 0;
        $score = $request->input('score');
        $price = $request->input('price');

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
            ]);
            if (!$rs)
                throw new \Exception('修改订单状态失败');

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
            'body'          =>  '代购商品',
            'detail'        =>  '购买商品',
            'out_trade_no'  =>  $order_no,
            'total_fee'     => $price * 100,
            'notify_url'    => 'http://www.jingyuxuexiao.com/purchase/wxnotify',
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
        $app = new Application(config('wx'));
        $response = $app->payment->handleNotify(function($notify, $successful){
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
            if ($order->is_comm != 1)
                return true;

            // 用户是否支付成功
            if ($successful) {
                DB::beginTransaction();
                try {
                    $info = DB::table('orderinfo')->where('info_id', $order->info_id)->update([
                        'status' => $this->state['ORDER_WAIT_SEND'],
                        'pay_time' => date("Y-m-d H:i:s"),
                        'transaction_id'=>$notify->transaction_id
                    ]);
                    if (!$info) {
                        throw new \Exception("修改订单状态失败");
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
                    DB::table('user_purchase')->where('order_no', $order->order_no)->update(['is_pay' => 2]);
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

    /**
     * 会员卡支付
     * @param Request $request
     * @return mixed
     */
    public function cardpay(Request $request) {
        $address_id = $request->input('address_id');
        $order_no = $request->input('order_no');
        $express_price = 0;
        $score = $request->input('score');
        $price = $request->input('price');

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
            ]);
            if (!$rs)
                throw new \Exception('修改订单状态失败');

            if (!empty($score)) {
                if ($user->score < $score)
                    throw new \Exception('积分不够');

                DB::table('user')->where('userid', $user->userid)->decrement('score', $score);
            }
            DB::table('user_purchase')->where('order_no', $order->order_no)->update(['is_pay' => 2]);
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollback();
            return response()->json(['rs' => 0, 'errmsg' => "订单出现异常"]);
        }

        return response()->json(['rs' =>1, 'errmsg' => "支付成功"]);
    }

    /**
     * 申请退款
     */
    public function returnpay(Request $request) {
        $purchase_id = $request->input('purchase_id');
        if (empty($purchase_id))
            return response()->json(['rs'=>0, 'errmsg' => '数据存在异常']);
        $purchase = DB::table('user_purchase')->where('id', $purchase_id)->first();
        if (empty($purchase) || $purchase->is_pay != 1 || $purchase->is_back != 1)
            return response()->json(['rs' => 0, 'errmsg' => '订单数据异常']);

        $app = new Application(config('wx'));
        $payment = $app->payment;
        $result = $payment->refundByTransactionId($purchase->transaction_id, $purchase->prepay_order_no, $purchase->pay_money);
        if ($result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {
            DB::table('user_purchase')->where('id', $purchase_id)->update(['is_pay' => 3]);
            return response()->json(['rs' => 1]);
        }
        else
            return response()->json(['rs' => 0]);

    }
}