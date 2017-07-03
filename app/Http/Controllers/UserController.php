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
            return response()->json(['rs'=>0]);
        }
        $olduser = DB::table('olduser')
            ->leftJoin('children', 'children.parent_id', '=', 'olduser.id')
            ->where(['phone'=>$phone, 'password'=>$pass])->select('olduser.*', 'children.relate_id')->first();
        if ($olduser->id) {
            $rs = DB::table('user')->where(['userid' => $request->session()->get('uid')])
                ->update([
                    'phone' => $phone,
                    'nickname' => $olduser->name,
                    'child_id' => $olduser->relate_id,
                    'is_old' => 1
                ]);
            return response()->json(['rs' => $rs]);
        }
        return response()->json(['rs'=>0]);
    }

    public function modinfo(Request $request) {
        $index = $request->input('index');
        $content = $request->input('content');
        $uid = $request->session()->get("uid");
        if (empty($index) || empty($content)) {
            return response()->json(['rs' => 0]);
        }
        $data = [];
        switch (index) {
            case 0:
                DB::table('user')->where('uid', $uid)->update([
                    'nickname' => $content
                ]);
                break;
            case 1:
                DB::table('user')->where('uid', $uid)->update([
                    'phone' => $content
                ]);
                break;
            case 2:
                DB::table('children')->where('uid', $uid)->update([
                    'name' => $content
                ]);
                break;
            case 3:
                DB::table('children')->where('uid', $uid)->update([
                    'birth_date' => $content
                ]);
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
        $pagesize = 10;
        $pagenow = $page * $pagesize;
        $scores = DB::table('scorechange')->where('uid', $request->session()->get('uid'))
            ->orderBy('create_time', 'desc')
            ->paginate($pagesize);
        return $request->has('page') ? response()->json(['scores' => $scores]) : view('score', ['scores' => $scores]);
    }

}