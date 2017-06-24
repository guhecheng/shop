<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/21
 * Time: 12:15
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller {
    private $is_deleted = 1;        //已删除
    private $no_deleted = 0;        // 未删除


    public function index(Request $request) {
        $users = DB::table('user')->where('is_delete', $this->no_deleted)
                                  ->orderBy('create_time', 'desc')
                                  ->paginate(5);
        return view('admin.user.index', ['users' => $users]);
    }

    /**
     * 删除用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) {
        if (!empty($request->input('uid'))) {
            $rs = DB::table('user')->where('userid', $request->input('uid'))
                             ->update(['is_delete' => $this->is_deleted]);
        }
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }
}