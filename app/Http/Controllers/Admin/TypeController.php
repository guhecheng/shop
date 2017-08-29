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

    public function index(Request $request) {
        $brand_id = $request->input("brand_id");
        if (empty($brand_id)) exit;
        $brand = DB::table('brands')->where('id', $brand_id)->first();
        $types = DB::table('goodstype')->where([ 'is_delete' => 0, 'brand_id' => $brand_id])->orderBy('sort', 'asc')->paginate(5);
        return view('admin.type.index', ['types' => $types, 'brand_id' => $brand_id, 'brand' => $brand]);
    }

    public function add(Request $request) {
        $typename = $request->input('typename');
        $brand_id = $request->input('brand_id');
        if (empty($typename) || empty($brand_id))
            return response()->json(['rs' => 0]);
        $id = DB::table('goodstype')->insertGetId([
            'typename' => trim($typename),
            'brand_id' => $brand_id
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

    public function changeorder(Request $request) {
        $type_ids = $request->input('type_ids');
        $orders = $request->input('orders');
        $type_arr = explode(",", $type_ids);
        $order_arr = explode(",", $orders);
        foreach ($type_arr as $key => $value) {
            DB::table('goodstype')->where('typeid', $value)->update(['sort'=>$order_arr[$key]]);
        }
    }
}