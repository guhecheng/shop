<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/7/2
 * Time: 14:10
 */
namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Validator;

class AuthController extends Controller {
    public function index() {
        $data = DB::table('auth')->orderBy('create_time')->get();
        $auths = $this->getAuths($data);
        return view('admin.auth.index', ['auths' => $auths]);
    }

    private function getAuths($data, $pid = 0, $pos = 0) {
        static $auths;
        foreach ($data as $item) {
            if ($item->auth_pid == $pid) {
                $item->pos = $pos;
                $auths[] = $item;
                $this->getAuths($data, $item->auth_id, $pos + 1 );
            }
        }
        return $auths;

    }

    /**
     * 添加权限
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'auth_name' => 'required',
            /*'auth_url' => 'required',*/
            'parent_auth_id' => 'required',
            'is_show' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['rs' => 0, 'msg' => '填写不完整']);
        }
            $rs = DB::table('auth')->insert([
            'auth_name' => $request->input('auth_name'),
            'auth_url' => empty($request->input('auth_url')) ? '' : $request->input('auth_url'),
            'auth_pid' => $request->input('parent_auth_id'),
            'is_show' => $request->input('is_show')
        ]);
        return response()->json(['rs' => 1]) ;
    }

    public function adminauth(Request $request) {
        $adminuser = DB::table('adminuser')->where('admin_id', '>', 1)->get();
        $data = DB::table('auth')->orderBy('create_time')->get();
        $auths = $this->getAuths($data);
        return view('admin.auth.admin', ['auths' => $auths, 'adminuser'=>$adminuser]);
    }

    public function addAdmin(Request $request) {
        $validator = Validator::make($request->all(), [
            'admin_name' => 'required',
            'auth_ids' => 'required',
            'login_name' => 'required',
            'login_pass' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['rs' => 0, 'msg' => '填写不完整']);
        }
        $rs = DB::table('adminuser')->insert([
            'name' => trim($request->input('login_name')),
            'nick_name' => trim($request->input('admin_name')),
            'password' => Hash::make(trim($request->input('login_pass'))),
            'auth_ids' => rtrim($request->input('auth_ids'), ',')
        ]);
        return response()->json(['rs' => $rs ? 1 : 0]);
    }

    public function disable(Request $request) {
        $rs = DB::table('adminuser')->where('admin_id', $request->input('admin_id'))
            ->update(['is_disabled'=>$request->input('status')]);
        return response()->json(['rs'=>$rs]);
    }
}