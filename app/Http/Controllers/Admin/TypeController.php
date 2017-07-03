<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/21
 * Time: 21:21
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class TypeController extends Controller {

    public function index() {
        $types = DB::table('goodstype')->where('is_delete', 0)->orderBy('sort', 'desc')->paginate(5);
        return view('admin.type.index', ['types' => $types]);
    }

    public function add(Request $request) {
        $typename = $request->input('typename');
        if (empty($typename))
            return response()->json(['rs' => 0]);
        $id = DB::table('goodstype')->insertGetId([
            'typename' => $typename
        ]);
        return response()->json(['rs'=>$id>0?1:0, 'typeid'=>$id]);
    }

    public function delete(Request $request) {
        $typeid = $request->input('typeid');
        if ($typeid) {
            $rs = DB::table('goodstype')->where('typeid', $typeid)
                                  ->update(['is_delete'=>1]);
        }
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function modify(Request $request) {
        $typename = $request->input('typename');
        $typeid = $request->input('typeid');
        if ($typeid && $typename) {
            DB::table('goodstype')->where('typeid', $typeid)
                                  ->update(['typename' => $typename]);
            return response()->json(['rs' => 1]);
        }
        return response()->json(['rs' => 0]);
    }
}