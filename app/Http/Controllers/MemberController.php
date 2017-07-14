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
}