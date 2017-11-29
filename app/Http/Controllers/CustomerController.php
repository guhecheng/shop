<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Payment\Order;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller {
    public function qrcode(Request $request) {
        $type = $request->input('type');
        switch ($type) {
            case 1: $qrcode_img = 'qrcode_T100.png'; break;
            case 2: $qrcode_img = 'qrcode_jnby.png'; break;
            case 3: $qrcode_img = ''; break;
            case 4: $qrcode_img = 'qrcode_mini_peace.jpg'; break;
        }

        return view('qrcode', ['qrcode_img' => '/images/qrcode/kf/'.$qrcode_img]);
    }
}