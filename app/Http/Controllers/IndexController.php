<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/13
 * Time: 22:15
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class IndexController extends Controller {
    public function index() {
        $types = DB::table('goodstype')->where('is_delete', 0)
                            ->get();
        $goods = DB::table('goods')->where([
            ['is_delete', '=', 0],
            ['is_hot', '=', 1],
            ['is_sale', '=', 1]
        ])
            ->orderBy('create_time', 'desc')->get();
        return view('index', ['types' => $types, 'goods' => $goods]);
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
}