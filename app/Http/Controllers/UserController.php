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

class UserController extends Controller {

    /**
     * 我的页面信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request) {
        $uid = $request->session()->get('uid');
        $user = DB::table('user')->where('userid', $uid)->first();
        return view('user.index', ['user' => $user]);
    }

    public function info(Request $request) {
        $user = DB::table('user')->leftJoin('children', 'user.child_id', '=', 'children.relate_id')
            ->where("userid", $request->session()->get('uid'))->first();
        return view("user.info", ['user'=>$user]);
    }

    public function relate(Request $request) {
        $phone = $request->input('phone');
        $pass = $request->input('pass');
        if (empty($phone) || empty($pass)) {
            return response()->json(['rs'=>0, 'errmsg'=>'手机号或者密码错误']);
        }
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
                'is_old' => 1
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
                $rs = $this->addOrUpdateChild($uid, ['like_brandss' => $content]);
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
        $user = DB::table('user')->where('userid', $uid)->select('child_id')->first();
        if (empty($user->child_id)) {

        }
        if (empty($user->child_id)) {
            return DB::table('children')->where('relate_id', $user->child_id)->insert($data);
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

}