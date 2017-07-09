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

class CapitalController extends Controller {

    public function index() {
        $data = DB::table('usertransmoney')->orderBy('create_time', 'desc')->paginate(20);
        return view('admin.capital', ['data' => $data]);
    }
}