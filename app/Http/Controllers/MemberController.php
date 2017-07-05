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

class MemberController extends Controller {

    public function card(Request $request) {
        $uid = $request->input("uid");
        $user = DB::table("user")->where('userid', $uid)->first();
        $card_no = 123456789;
        $level = 1;
        $pic = 'xx';
        return view('card', ['card_no'=>$card_no, 'pic'=>$pic, 'level' => $level]);
    }
}