<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/20
 * Time: 21:47
 */
namespace App\Http\Controllers\Admin;

use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AdminController extends Controller {

    public function index() {
        return view('admin.index');
    }

    public function login(Request $request) {
        return view('admin.login', ['name' => Cookie::get('name')]);
    }

    public function check(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['rs' => 0, 'msg' => '填写不完整']);
        } else {
            $user = DB::table('adminuser')->where([
                ['name', '=', $request->input('name')],
                ['is_disabled', '=', 0]
            ])->first();
            if (Hash::check($request->input('password'), $user->password)) {
                if ($request->input('remeber')) {
                    Cookie::make('name', $request->input('name'));
                }
                session(['sysuid' => $user->admin_id]);
                return response()->json(['rs' => 1]);
            } else
                return response()->json(['rs' => 0, 'msg' => '账号密码不一致']);
        }
    }
}