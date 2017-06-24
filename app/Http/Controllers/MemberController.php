<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/17
 * Time: 10:34
 */

namespace App\Http\Controllers;

class MemberController extends Controller {

    public function card() {
        $card_no = 123456789;
        $level = 1;
        return view('card', ['card_no'=>$card_no, 'level' => $level]);
    }
}