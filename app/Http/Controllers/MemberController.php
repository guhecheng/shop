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
use Illuminate\Support\Facades\Redis;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Payment\Order;


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
        if (empty($money) || intval($money) == 0) {
            return response()->json(['rs' => 0, 'errmsg' => '请确定支付金额']);
        }
        $rs = DB::table('cardrecharge')->insertGetId([
            'uid' => $request->sessoin()->get('uid'),
            'money' => intval($money)
        ]);
        if (empty($rs)) {
            return response()->json(['rs' => 0, 'errmsg' => '交易出现问题，请点击重新支付']);
        }
        $app = new Application(config('wx'));
        $payment = $app->payment;
        $attributes = [
            'trade_type'    =>  'JSAPI',
            'body'          =>  '会员卡充值',
            'detail'        =>  '会员卡充值',
            'out_trade_no'  =>  $rs->id,
            'total_fee'     => $money,
            'notify_url'    => 'http://www.jingyuxuexiao.com/card/pay',
            'sub_openid'    => $request->session()->get('openid')
        ];
        $order = new Order($attributes);
        $result = $payment->prepare($order);
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS'){
            $prepayId = $result->prepay_id;
            exit($payment->configForAppPayment($prepayId));
        }
        return response()->json(['rs' => 0, 'errmsg' => "网络出现异常"]);
    }

    public function paycallback() {
        $app = new Application(config('wx'));

        $response = $app->payment->handleNotify(function($notify, $successful){
            $order = DB::table('chardcharge')->where('id', $notify->out_trade_no)->get();
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
                    $order_rs = DB::table('order')->where('id', $order->id)->update(['status' => 1, 'pay_time' => date("Y-m-d H:i:s")]);
                    if (!$order_rs) {
                        throw new \Exception('订单状态修改失败');
                    }
                    $trans['insert'] = DB::table('usertransmoney')->insert([
                        'uid'   => $order->uid,
                        'trans_type'    => 1,
                        'trans_money' => $order->money
                    ]);
                    if (!$trans['insert'])
                        throw new \Exception('操作失败');

                    $user = DB::table('user')->get("userid", $order->uid)->first();
                    if (empty($user))
                        throw new \Exception('用户不存在');
                    $level = 0;
                    if ($user->level < 3) {
                        $money = DB::table('usertransmoney')->where([
                            ['uid', '=', $user->userid],
                            ['create_time', '>=', date("Y-m-d H:i:s", strtotime("-1 year"))]
                        ])->sum('trans_money');
                        $card = DB::table('card')->where([
                            ['is_delete', '=>', 0],
                            ['card_score', '<=', $money]
                        ])->orderBy('card_level', 'asc')->select('card_level')->get();
                        if (!empty($card)) {
                            $level = $card->card_level;
                        }
                    } else
                        $level = $this->level;

                    $user_rs = DB::table('user')->where("uid", $order->uid)->update([
                        'money' => $user->money + $order->money,
                        'level' => $level
                    ]);
                    if (!$user_rs) {
                        throw new \Exception("修改用户金额失败");
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollback();
                }
            } else { // 用户支付失败
                return false;
            }
            return true; // 返回处理完成
        });
        return $response;
    }
}