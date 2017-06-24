<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/15
 * Time: 21:08
 */
namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller {

    public function create(Request $request) {
        $uid = $request->session()->get("uid");
        $car_ids = $request->input('car_ids');
        if (empty($uid) || empty($car_ids)) {
            exit;
        }
        $address = DB::table('useraddress')->where([
            ['uid', '=', $uid],
            ['is_default', '=', 1]
        ])->first();
        $cars = DB::table('cart')
            ->leftJoin("goods", "goods.goodsid", '=', 'cart.goodsid')
            ->leftJoin("goodssku", 'goodssku.sku_id', '=', 'cart.skuid')
            ->select('cart.*', 'goods.goodsname', 'goods.goodsicon', 'goods.is_delete as goods_delete', 'goods.price',
                'goodssku.num', 'goodssku.price as sku_price')
            ->where([
                ['uid', '=', $uid],
                ['cart.is_delete', '=', 0]
            ])
            ->get();
        $sql = "select cart.*, goods.goodsname, goods.goodsicon, goods.is_delete as goods_delete, goods.price,
                goodssku.num, goodssku.price as sku_price from cart left join goods on goods.goodsid=cart.goodsid 
                left join goodssku on goodssku.sku_id=cart.skuid where cart.uid=$uid and cart.is_delete=0 and 
                cart.cartid in (".rtrim($car_ids,',').")";
        $goods = DB::select($sql);

        if ($goods) {
            $skuids = [];
            foreach ($cars as $item) {
                $skuids[] = $item->skuid;
            }

            $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                where gp.sku_id in (".implode(',', $skuids).")";
            $skus = DB::select($sql);
            foreach ($goods as &$item) {
                $item->property = '';
                foreach ($skus as $value) {
                    if ($value->sku_id == $item->skuid) {
                        $item->property .= $value->key_name .':'.$value->value_name. " ";
                    }
                }
            }
        }
        $user = DB::table('user')->where('userid', $uid)->first();
        return view('order.create', ['goods' => $goods, 'address' => $address, 'user' => $user]);
    }
}