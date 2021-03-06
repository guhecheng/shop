<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/24
 * Time: 11:55
 */
namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CarController extends Controller {

    public function add(Request $request) {
        $uid = $request->session()->get('uid');          // 用户id
        $attr = $request->input('attr');        // 属性
        $goodsid = $request->input('goodsid');  // 商品id
        if (empty($request->input('num'))) {
            return response()->json(['rs' => 0, 'msg' => '商品数量有错']);
        }
        if (empty($request->input('attr'))) {
            return response()->json(['rs'=>0, 'msg' => '属性没有选全']);
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
        $cart = DB::table('cart')->where([
            ["skuid", '=', $rs[0]->sku_id],
            ['goodsid', '=', $goodsid],
            ['is_delete', '=', 0],
            ['uid', '=', $uid]
        ])->first();
        if ($cart) {
            $add = DB::table('cart')->where('cartid', $cart->cartid)
                ->update([
                    'goodscount' => $cart->goodscount + $request->input('num')
                ]);
        } else {
            $add = DB::table('cart')->insert([
                'goodsid' => $goodsid,
                'skuid' => $rs[0]->sku_id,
                'uid' => $uid,
                'goodscount' => $request->input('num')
            ]);
        }
        if ($add)
            return response()->json(['rs' => 1]);
    }

    public function index(Request $request) {
        $uid = $request->session()->get("uid");
        $cars = DB::table('cart')
                    ->leftJoin("goods", "goods.goodsid", '=', 'cart.goodsid')
                    ->leftJoin("goodssku", 'goodssku.sku_id', '=', 'cart.skuid')
                    ->select('cart.*', 'goods.goodsname', 'goods.goodsicon', 'goods.is_delete as goods_delete', 'goods.price',
                        'goodssku.num', 'goodssku.price as sku_price')
                    ->where([
                        ['uid', '=', $uid],
                        ['cart.is_delete', '=', 0],
                        ['goods.is_delete', '=', 0]
                    ])
                    ->get();
        if (!$cars->isEmpty()) {
            $skuids = [];
            foreach ($cars as $item) {
                $skuids[] = $item->skuid;
            }

            $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                where gp.sku_id in (".implode(',', $skuids).")";
            $skus = DB::select($sql);
            foreach ($cars as &$item) {
                $item->property = '';
                foreach ($skus as $value) {
                    if ($value->sku_id == $item->skuid) {
                        $item->property .= $value->value_name. " ";
                    }
                }
            }
        }
        return view('car', ['cars' => $cars]);
    }

    public function delcar(Request $request) {
        $uid = $request->session()->get('uid');
        $car_ids = $request->input('car_ids');
        foreach ($car_ids as $item) {
            DB::table('cart')->where('cartid', $item)
                            ->update(['is_delete'=>1]);
        }
        return response()->json(['rs' => 1]);
    }

    /**
     * 根据cartid修改购物车数量(@Override 没有判断库存多少是否上限)
     * @param Request $request
     */
    public function modcar(Request $request) {
        $cartid = $request->input('cartid');
        $count = $request->input('count');
        if (empty($cartid) || empty($count))
            return response()->json(['rs'=>0, 'errmsg'=>'信息错误']);
        $sku = DB::table('cart')->leftJoin('goodssku', 'goodssku.sku_id', '=', 'cart.skuid')
                    ->where('cart.cartid', $cartid)->first();
        if ($sku->num < $count) {
            return response()->json(['rs' => 0, 'max_count'=>$sku->num]);
        }
        DB::table('cart')->where('cartid', $cartid)->update(['goodscount'=>$count]);
        return response()->json(['rs' => 1]);
    }
}