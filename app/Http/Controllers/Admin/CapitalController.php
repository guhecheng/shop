<?php
/**
 * 流水操作
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/7/9
 * Time: 10:56
 */
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CapitalController extends Controller {

    public function index(Request $request) {
        $search_no = $request->input('search_no');
        $search_name = $request->input('search_name');
        $where = [];
        if (!empty($search_no))
            $where[] = ['order_no', '=', $search_no];
        if (!empty($search_name)) {
            $where[] = ['user.uname', '=', $search_name];
        }
        $data = DB::table('usertransmoney')->leftJoin('user', 'user.userid', '=', 'usertransmoney.uid')
                 ->orderBy('create_time', 'desc')->select('usertransmoney.*', 'user.uname')->where($where)->paginate(20);
        return view('admin.capital', ['data' => $data, 'search_no'=>$search_no, 'search_name'=>$search_name]);
    }
}