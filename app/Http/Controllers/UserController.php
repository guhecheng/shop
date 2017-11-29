<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/15
 * Time: 20:19
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Log;

class UserController extends Controller {

    /**
     * 我的页面信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $uid = $request->session()->get('uid');
        $user = DB::table('user')->where('userid', $uid)->first();
        $coupon = DB::table("user_levelup_coupon")->where('uid', $uid)->get();
        return view('user.index', ['user' => $user, 'has_coupon' => empty($coupon) ? false : true]);
    }

    public function info(Request $request) {
        $user = DB::table('user')->leftJoin('children', 'user.child_id', '=', 'children.relate_id')
            ->where("userid", $request->session()->get('uid'))->first();
        return view("user.info", ['user'=>$user]);
    }

    /**
     * 孩子关联父母信息
     * @param Request $request
     * @return mixed
     */
    public function relate(Request $request) {
        $phone = $request->input('phone');
        $pass = $request->input('pass');
        if (empty($phone) || empty($pass)) {
            return response()->json(['rs'=>0, 'errmsg'=>'手机号或者密码错误']);
        }
        $user = DB::table("user")->where('userid', $request->session()->get('uid'))->first();
        if (empty($user))
            return response()->json(['rs' => 0, 'errmsg' => '用户信息错误']);
        if ($user->child_id > 0)
            return response()->json(['rs' => 0, 'errmsg' => '用户已经关联过老用户信息']);

        $olduser = DB::table('olduser')
            ->leftJoin('children', 'children.parent_id', '=', 'olduser.id')
            ->where(['phone'=>$phone, 'password'=>$pass])->select('olduser.*', 'children.relate_id')->first();
        if (empty($olduser->id)) {
            return response()->json(['rs' => 0, 'errmsg'=>'手机号或者密码错误']);
        }
        $rs = DB::table('user')->where(['userid' => $request->session()->get('uid')])
            ->update([
                'phone' => $phone,
                'nickname' => $olduser->name,
                'child_id' => $olduser->relate_id,
                'is_old' => 1,
                'score' => $user->score + $olduser->score
            ]);
        if ($rs)
            return response()->json(['rs' => 1]);
        return response()->json(['rs'=>0, 'errmsg'=>'关联失败']);
    }

    public function modinfo(Request $request) {
        $index = $request->input('index');
        $content = trim($request->input('content'));
        $uid = $request->session()->get("uid");
        if (empty($index) || empty($content)) {
            return response()->json(['rs' => 0]);
        }
        $data = [];
        switch ($index) {
            case 'myname':
                $rs = DB::table('user')->where('userid', $uid)->update([
                    'nickname' => $content
                ]);
                break;
            case 'phone':
                $count = DB::table('user')->where([
                    ['userid', '=', $uid],
                    ['phone', '=', $content]
                ])->count();
                if ($count >= 1)
                    return response()->json(['rs'=>0, 'errmsg' => '手机号码已存在']);
                $rs = DB::table('user')->where('userid', $uid)->update([
                    'phone' => $content
                ]);
                break;
            case 'child_name':
                $rs = $this->addOrUpdateChild($uid, ['name' => $content]);
                break;
            case 'child_birth_date':
                $rs = $this->addOrUpdateChild($uid, ['birth_date' => $content]);
                break;
            case 'child_sex':
                $rs = $this->addOrUpdateChild($uid, [
                    'sex' => $content == '男' ? 1 : ($content == '女' ? 2 : 0)
                ]);
                break;
            case 'child_school':
                $rs = $this->addOrUpdateChild($uid, ['school' => $content]);
                break;
            case 'child_brand':
                $rs = $this->addOrUpdateChild($uid, ['like_brands' => rtrim($content, ',')]);
                break;
        }
        return response()->json(['rs' => $rs]);
    }

    /**
     * 根据用户id获取孩子信息从而判断是添加还是修改
     * @param $uid
     * @param $data
     */
    private function addOrUpdateChild($uid, $data) {
        $user = DB::table('user')->where('userid', $uid)->select('child_id', 'userid')->first();
        if (empty($user->child_id)) {
            //return DB::table('children')->where('relate_id', $user->child_id)->insert($data);
           $id = DB::table("children")->insertGetId($data);
           return DB::table('user')->where('userid', $user->userid)->update(['child_id' => $id]);
        } else {
            return DB::table('children')->where('relate_id', $user->child_id)->update($data);
        }
    }
    /**
     * 获取个人流水
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function money(Request $request) {
        $page = $request->has('page') ? $request->input('page') : 0;
        $pagesize = 10;
        $pagenow = $page * $pagesize;
        $money = DB::table('usertransmoney')->where('uid', $request->session()->get('uid'))
            ->orderBy('create_time', 'desc')
            ->paginate($pagesize);
        return $request->has('page') ? response()->json(['money' => $money]) : view('money', ['money' => $money]);
    }

    /**
     * 个人积分
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function score(Request $request) {
        $page = $request->has('page') ? $request->input('page') : 0;
        $pagesize = 50;
        $pagenow = $page * $pagesize;
        $scores = DB::table('scorechange')->where([
                ['uid', '=', $request->session()->get('uid')],
                ['score', '>', 0]
            ])
            ->orderBy('create_time', 'desc')
            ->paginate($pagesize);
        return $request->has('page') ? response()->json(['scores' => $scores]) : view('score', ['scores' => $scores]);
    }


    public function fit(Request $request) {
        if ($request->ajax()) {
            $concat_phone = $request->input('concat_phone');
            $concat_name = $request->input('concat_name');
            $age = $request->input('age');
            $desc = $request->input('desc');
            $gender = $request->input('gender');
            $size = $request->input('size');
            if (empty($gender) || ($gender != '男' && $gender == '女')) {
                return response()->json(['rs' => 0, 'errmsg' => '缺少信息']);
            }
            if (empty($concat_name) || empty($concat_phone) || empty($age) || empty($desc)) {
                return response()->json(['rs' => 0, 'errmsg' => '缺少信息']);
            }
            $rs = DB::table('user_diamond_fit_info')->insert([
                'uid'   => $request->session()->get('uid'),
                'uname' => $concat_name,
                'concat_phone' => $concat_phone,
                'age' => $age,
                'detail' => $desc,
                'gender' => empty($gender) ? 0 : ($gender == '男' ? 1 : 2),
                'size' => $size
            ]);
            if ($rs)
                return response()->json(['rs' => 1]);
            return response()->json(['rs' => 0, 'errmsg' => '添加失败']);        }
        return view('user.fit');
    }

    public function overStudy(Request $request) {
        if ($request->ajax()) {
            $concat_phone = $request->input('concat_phone');
            $concat_name = $request->input('concat_name');
            $age = $request->input('age');
            $desc = $request->input('desc');
            if (empty($concat_name) || empty($concat_phone) || empty($age) || empty($desc)) {
                return response()->json(['rs' => 0, 'errmsg' => '缺少信息']);
            }
            $rs = DB::table('user_oversea_consulation')->insert([
                'uid'   => $request->session()->get('uid'),
                'uname' => $concat_name,
                'age' => $age,
                'concat_phone' => $concat_phone,
                'concat_desc' => $desc
            ]);
            if ($rs)
                return response()->json(['rs' => 1]);
            return response()->json(['rs' => 0, 'errmsg' => '添加失败']);
        }
        return view('user.overstudy');
    }


    public function luxurySale(Request $request) {
        if ($request->ajax()) {
            $goods_name = $request->input('goods_name');
            $goods_price = $request->input('goods_price');
            $concat_phone = $request->input('concat_phone');
            $goods_desc = $request->input('goods_desc');
            $goods_image = $request->input('goods_image');
            if (empty($goods_price) || empty($goods_name) || empty($concat_phone) || empty($goods_desc))
                return response()->json(['rs' => 0, 'errmsg' => '缺少信息']);
            $rs = DB::table('user_luxury_sale')->insert([
                'goods_name' => trim($goods_name),
                'detail' => trim($goods_desc),
                'concat_phone' => trim($concat_phone),
                'goods_price' => intval($goods_price) * 100,
                'uid' => $request->session()->get("uid"),
                'goods_image' => rtrim($goods_image, ',')
            ]);
            if ($rs)
                return response(['rs' => 1]);
            return response()->json(['rs' => 0, 'errmsg' => "添加失败"]);
        }
        return view('user.luxurysale');
    }

    public function shareCoupon(Request $request) {
        //if ($request->session()->get("uid") != 1) exit;
        $coupons = DB::table("user_levelup_coupon")->where(['uid'=>$request->session()->get('uid')])->orderBy('create_time', 'desc')->get();
        return view('user.sharecoupon', ['coupons' => $coupons]);
    }

    public function levelCoupon(Request $request) {
        $id = $request->input('id');
        if (empty($id)) exit('数据缺失');
        $user = DB::table('user_levelup_coupon')->leftJoin('user', 'user.userid', '=', 'user_levelup_coupon.uid')->where('user_levelup_coupon.id', $id)->select('user.*', 'user_levelup_coupon.*')->first();
        if (empty($user)) exit('数据缺少');
        $app = new Application(config('wx'));
        $login_user = $request->session()->get('uid') ?? 0;
        if (!empty($user) && $login_user != $user->uid ){
            if (!empty($login_user)) {
                $login_user = DB::table('user')->where('userid', $login_user)->first();
                if ($login_user->level >= intval($user->type) + 1)
                    exit('对不起,优惠券等级不匹配');
                $openid = $login_user->openid;
                $name = $login_user->uname;
            }
            if (empty($openid) && isset($_GET['code']))
                $wx_user = $app->oauth->user();
            if (empty($openid) && empty($wx_user)) {
                $share_url = urlencode("http://www.jingyuxuexiao.com/user/levelcoupon?id=" . $id);
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxd8b6b83c91c44ac3&redirect_uri={$share_url}&response_type=code&scope=snsapi_userinfo&state=fjalskdfdas#wechat_redirect";
                header("Location:" . $url);exit;
            }
            if (!empty($wx_user)) {
                $openid = $wx_user->id;
                $name = $wx_user->name;
            }
            return view('user.levelcoupon', ['js' => $app->js, 'id'=>$id, 'user' => $user, 'flag' => true, 'openid' => $openid, 'uname' => $name]);
        }
        return view('user.levelcoupon', ['js' => $app->js, 'id'=>$id, 'user' => $user, 'flag' => false]);
    }

    public function share(Request $request) {
        /*$app = new Application(config('wx'));
        $user = $app->oauth->user();
        if (empty($user)) {
            $share_url = urlencode("http://www.jingyuxuexiao.com/share?coupon_id=" . $id);
            $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxd8b6b83c91c44ac3&redirect_uri={$share_url}&response_type=code&scope=snsapi_userinfo&state=fjalskdfdas#wechat_redirect";
            header("Location:" . $url);exit;
        }
        $id = $request->input('coupon_id');
        var_dump($user);exit;
        if (empty($id))
            exit('数据缺失');
        $user = DB::table('user_levelup_coupon')->leftJoin('user', 'user.userid', '=', 'user_levelup_coupon.uid')->where('user_levelup_coupon.id', $id)->select('user.*', 'user_levelup_coupon.*')->first();
        return view('user.share', ['user' => $user, 'id' => $id, 'openid' => '']);*/
    }

    /**
     * 领取优惠券
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recvCoupon(Request $request) {
        $openId = $request->input('openid');
        $coupon_id = $request->input('coupon_id');
        $uname = $request->input('uname');
        Log::info(json_encode($_REQUEST));
        if (empty($openId) || empty($coupon_id))
            return response()->json(['rs' => 0, 'errmsg' => '缺少数据']);
        $coupon = DB::table('user_levelup_coupon')->where('id', $coupon_id)->first();
        if (empty($coupon) || !empty($coupon->openid))
            return response()->json(['rs'=>0, 'errmsg' => '优惠券已失效']);
        $user = DB::table('user')->where('userid', $coupon->uid)->first();
        if (!empty($user) && $user->openid == $coupon->openid)
            return response()->json(['rs'=>0, 'errmsg' => '自己不可以领取代金券']);
        if ($user->level >= ($coupon->type + 1))
            return response()->json(['rs' => 0, 'errmsg' => '自己等级高于优惠券等级']);
        $rs = DB::table('user_levelup_coupon')->where('id', $coupon_id)->update(['openid'=>$openId, 'recv_time' => date("Y-m-d H:i:s"), 'recv_uname' => $uname]);
        return response()->json(['rs' => $rs?1:0]);
    }

    /**
     * 上传图片
     * @param Request $request
     * @return json
     */
    public function uploadImage(Request $request) {
        $file = $request->file('file');
        if ($file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'luxury/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool)
                return response()->json(['imgurl' => '/uploads/' . $fileName]);
        }
    }
}