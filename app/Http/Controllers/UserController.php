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
    public function index(Request $request) {
        $uid = $request->session()->get('uid');
        $user = DB::table('user')->where('userid', $uid)->first();
        return view('user.index', ['user' => $user]);
    }

    public function score(Request $request) {
        $page = $request->has('page') ? $request->input('page') : 0;
        $pagesize = 10;
        $pagenow = $page * $pagesize;
        $scores = DB::table('usertransmoney')->where('uid', $request->session()->get('uid'))
            ->orderBy('create_time', 'desc')
            ->paginate($pagesize);
        return view('user.score', ['scores' => $scores]);
    }
}