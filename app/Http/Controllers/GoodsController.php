<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/23
 * Time: 22:50
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GoodsController extends Controller {

    public function index(Request $request) {
        $goodsid = $request->input('goodsid');
        if ($goodsid) {
            $goods = DB::table('goods')->where([
                ['is_delete', '=', 0 ],
                ['goodsid', '=', $goodsid]
            ])
                            ->first();
            $property = DB::table('goodsproperty')
                            ->leftJoin('propertykey', 'propertykey.key_id', '=', 'goodsproperty.key_id')
                            ->leftJoin('propertyvalue', 'propertyvalue.value_id', '=', 'goodsproperty.value_id')
                            ->select('propertykey.key_name', 'propertyvalue.value_name')
                            ->where([
                                ['goodsproperty.is_delete', '=', 0],
                                ['goodsproperty.goods_id', '=', $goodsid],
                                ['is_sku', '=', 0]
                            ])
                            ->get();
            // 库存
            $count = DB::table('goodssku')->where('goods_id', $goodsid)
                            ->sum('num');
            return view('goods', ['goods' => $goods, 'count' => $count, 'property' => $property]);
        }
    }

    public function property(Request $request) {
        $goodsid = $request->input('goodsid');
        if ($goodsid) {
            $propertys = DB::table('goodsproperty')
                            ->leftJoin('goodssku', 'goodssku.sku_id', '=', 'goodsproperty.sku_id')
                            ->leftJoin('propertykey', 'propertykey.key_id', '=', 'goodsproperty.key_id')
                            ->leftJoin('propertyvalue', 'propertyvalue.value_id', '=', 'goodsproperty.value_id')
                            ->where('goodssku.goods_id', $goodsid)
                            ->orderBy('goodsproperty.key_id', 'asc')
                            ->orderBy('goodsproperty.value_id', 'asc')
                            ->get();
            return response()->json(['rs' => 1, 'propertys' => $propertys]);
        }
        return response()->json(['rs' => 0]);
    }
}