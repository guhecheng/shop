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

    /**
     * 检验登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    public function modify(Request $request) {
        $old_pass = $request->input("old_pass");
        $new_pass = $request->input("new_pass");
        $repeat_pass = $request->input('repeat_pass');
        if (empty($old_pass) || empty($new_pass) || empty($repeat_pass)) {
            return response()->json(['rs' => 0, 'errmsg' => '密码不能为空']);
        }
        if ($new_pass != $repeat_pass) {
            return response()->json(['rs' => 0, 'errmsg' => '新密码两次不一致']);
        }
        if (strlen($new_pass) < 6) {
            return response()->json(['rs' => 0, 'errmsg' => '密码过于简单']);
        }
        $sysuid = $request->session()->get('sysuid');
        $user = DB::table('adminuser')->where('admin_id', $sysuid)
            ->select('password')->first();
        if (!Hash::check($old_pass, $user->password)) {
            return response()->json(['rs' => 0, 'errmsg' => '原密码错误']);
        }
        $rs = DB::table('adminuser')->where('admin_id', $sysuid)
            ->update([
                'password' => Hash::make($new_pass)
            ]);
        return response()->json(['rs' => $rs]);
    }
}