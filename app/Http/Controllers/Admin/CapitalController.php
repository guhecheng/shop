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
        $start_date = empty($request->input('start_date')) ? '' : $request->input('start_date');
        $end_date = empty($request->input("end_date")) ? '' : $request->input('end_date');
        $where = [];
        if (!empty($search_no))
            $where[] = ['order_no', '=', $search_no];
        if (!empty($search_name)) {
            $where[] = ['user.uname', '=', $search_name];
        }
        if (!empty($start_date)) {
            $where[] = ['usertransmoney.create_time', '>=', date("Y-m-d H:i:s", strtotime($start_date))];
        }
        if (!empty($end_date)) {
            $where[] = ['usertransmoney.create_time', '<=', date("Y-m-d H:i:s", strtotime($end_date))];
        }
        $data = DB::table('usertransmoney')->leftJoin('user', 'user.userid', '=', 'usertransmoney.uid')
                 ->orderBy('create_time', 'desc')->select('usertransmoney.*', 'user.uname')->where($where)->paginate(20);
        return view('admin.capital', ['data' => $data, 'search_no'=>$search_no,
                                        'search_name'=>$search_name, 'start_date'=>$start_date, 'end_date'=>$end_date]);
    }
}