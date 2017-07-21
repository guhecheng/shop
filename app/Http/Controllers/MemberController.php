<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/17
 * Time: 10:34
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;
use Illuminate\Support\Facades\Log;


class MemberController extends Controller {
    private $card_redis = 'card:list';
    public function card(Request $request) {
        $uid = $request->session()->get('uid');
        $user = DB::table("user")->where('userid', $uid)->first();
        $card = DB::table('card')->select('card_img')->where([ ['is_delete', '=',  0], ['card_level', '=', $user->level]])->first();
        return view('card', ['card_no'=>$user->card_no,
            'level' => $user->level,
            'card' => $card]);
    }

    public function collect(Request $request) {
        $money = $request->input('money');
        DB::table('user')->where('userid', $request->session()->get('uid'))
            ->update();
    }

    public function pay(Request $request) {
        $money = $request->input('money');
        if (empty($money)) {
            return response()->json(['rs' => 0, 'errmsg' => '请确定支付金额']);
        }
         try {
            DB::beginTransaction();
            $charge_order = DB::table("cardrecharge")->orderBy('id', 'desc')->select('id')->limit(1)->first();
            $charge_no = date("Ymd") . mt_rand(1111, 9999) . ( empty($charge_order->id) ? 0 : $charge_order->id + 1);
            $charge_id = DB::table('cardrecharge')->insertGetId([
                'uid' => $request->session()->get('uid'),
                'money' => $money * 100,
                'charge_no' => $charge_no
            ]);
            if (!$charge_id)
                throw new \Exception("订单创建失败");
             DB::commit();
         } catch (\Exception $e) {
             DB::rollback();
             return response()->json(['rs' => 0, 'errmsg' => '交易出现问题，请点击重新支付']);
         }

        $app = new Application(config('wx'));
        $payment = $app->payment;
        $openid = $request->session()->get('openid');
        $attributes = [
            'trade_type'    =>  'JSAPI',
            'body'          =>  '会员卡充值',
            'detail'        =>  '会员卡充值',
            'out_trade_no'  =>  $charge_no,
            'total_fee'     => $money * 100,
            'notify_url'    => 'http://www.jingyuxuexiao.com/card/notify',
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

    public function notify() {
        $app = new Application(config('wx'));
        $response = $app->payment->handleNotify(function($notify, $successful){
            $order = DB::table('cardrecharge')->where('charge_no', $notify->out_trade_no)->first();

            if (!$order) { // 如果订单不存在
                return 'Order not exist.'; // 告诉微信，我已经处理完了，订单没找到，别再通知我了
            }
            // 如果订单存在
            // 检查订单是否已经更新过支付状态
            if ($order->status == 1) { // 假设订单字段“支付时间”不为空代表已经支付
                return true; // 已经支付成功了就不再更新了
            }
            // 用户是否支付成功
            if ($successful) {
                DB::beginTransaction();

                // 不是已经支付状态则修改为已经支付状态
                try {
                    $order_rs = DB::table('cardrecharge')->where('id', $order->id)
                                ->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s")]);
                    if (!$order_rs) {
                        throw new \Exception('订单状态修改失败');
                    }
                    $trans['insert'] = DB::table('usertransmoney')->insert([
                        'uid'   => $order->uid,
                        'trans_type'    => 1,
                        'trans_money' => $order->money,
                        'order_no' => $notify->out_trade_no
                    ]);
                    if (!$trans['insert'])
                        throw new \Exception('操作失败');

                    if (!empty($add_score = intval($order->money / 100))) {
                        $score = DB::table('scorechange')->insert([
                            'type' => 1,
                            'paytype' => 1,
                            'score' => $add_score,
                            'uid' => $order->uid
                        ]);
                    }
                    $user = DB::table('user')->where("userid", $order->uid)->first();
                    if (empty($user)) {
                        throw new \Exception('用户不存在');
                    }
                    $level = 0;
                    if ($user->level < 3) {
                        $money = DB::table('usertransmoney')->where([
                            ['uid', '=', $user->userid],
                            ['create_time', '>=', date("Y-m-d H:i:s", strtotime("-1 year"))]
                        ])->sum('trans_money');
                        $card = DB::table('card')->where([
                            ['is_delete', '=>', 0],
                            ['card_score', '<=', $money / 100]
                        ])->orderBy('card_level', 'desc')->select('card_level')->limit(1)->first();
                        if (!empty($card->card_level)) {
                            $level = $user->level > $card->card_level ? $user->level : $card->card_level;
                        }  else
                            $level = $user->level;
                    } else
                        $level = $user->level;

                    if (empty($user->card_no)) {
                        $card_no = DB::table("user")->max("card_no");
                        if (empty($card_no)) {
                            $no = 1;
                            $card_no = str_pad(1, 8, "0", STR_PAD_LEFT);
                        }
                    } else {
                        $card_no = $user->card_no;
                    }
                    $user_rs = DB::table('user')->where("userid", $order->uid)->update([
                        'money' => $user->money + $order->money,
                        'level' => $level,
                        'score' => $user->score + intval($order->money / 100),
                        'card_no' => $card_no
                    ]);
                    if (!$user_rs) {
                        throw new \Exception("修改用户金额失败");
                    }
                    DB::commit();
                } catch (\Exception $e) {
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

    public function forward(Request $request) {
        return redirect('/card');
    }
}