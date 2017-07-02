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
        return view("user.info");
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