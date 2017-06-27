<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/23
 * Time: 22:50
 */
namespace App\Http\Controllers;

use Validator;
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

    public function getgoods(Request $request) {
        $typeid = $request->input('typeid');
        $goods = DB::table('goods')->where([
            ['is_delete', '=', 0],
            ['typeid', '=', $typeid]
        ])
            ->limit(8)
            ->get();
        return response()->json(['goods' => $goods]);
    }

    /**
     * 根据商品列表页获取skuid
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getgoodssku(Request $request) {
        $attr = $request->input('attr');
        $goodsid = $request->input('goodsid');
        $num = $request->input('num');
        $validator = Validator::make($request->all(), [
            'num' => 'required',
            'attr' => 'required',
            'goodsid' => 'required',
        ]);
        if ($validator->fails()) {
            return response()
                ->json(['rs' => 0, 'msg' => '信息不全']);
        }
        $sql = 'select distinct(a.sku_id) from goodsproperty as a';
        foreach ($attr as $key=>$value) {
            if (empty($value)) continue;
            $sql .= ' left join goodsproperty as p'.$key. ' on a.sku_id= p'.$key.'.sku_id';
        }
        $sql .= " where ";
        foreach ($attr as $key=>$item) {
            if (empty($item)) continue;
            $sql .= " p".$key.".value_id=$item and";
        }
        $sql .=" a.goods_id=". $goodsid;
        $rs = DB::select($sql);

        if ($rs[0]->sku_id) {
            $sku = DB::table('goodssku')->where('sku_id', $rs[0]->sku_id)
                ->first();
            if ($sku->num < $request->input('num'))
                return response()->json(['rs'=>0,
                    'msg'=>'商品仅剩'.$sku->num,
                    'num' => $sku->num]);
        }
        return response()->json(['rs' => 1, 'sku_id' => $rs[0]->sku_id]);
    }
}