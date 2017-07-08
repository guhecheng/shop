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

class AuthController extends Controller {
    public function index() {
        $auths = DB::table('auth')->get();
        return view('admin.auth.index', ['auths' => $auths]);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'auth_name' => 'required',
            'auth_url' => 'required',
            'parent_auth_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['rs' => 0, 'msg' => '填写不完整']);
        }
        $rs = DB::table('auth')->insert([
            'auth_name' => $request->input('auth_name'),
            'auth_url' => $request->input('auth_url'),
            'auth_pid' => $request->input('parent_auth_id')
        ]);
        return response()->json(['rs' => 1]) ;
    }
}