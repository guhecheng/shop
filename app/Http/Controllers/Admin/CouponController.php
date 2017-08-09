<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/8/8
 * Time: 21:27
 */

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Exception;

class CouponController extends Controller
{
    public function index() {
        $coupons = DB::table('coupon')->paginate(10);
        $brands = DB::table('brands')->where('is_del', '0')->orderBy('sort')->get();

        return view('admin.coupon.index', ['coupons' => $coupons, 'brands' => $brands]);
    }


    public function add(Request $request) {
        $goods_price = $request->input('goods_price');
        $discount_price = $request->input('discount_price');
        $brands = $request->input('brands');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $user_type = $request->input('user_type');

        if (empty($goods_price) || empty($discount_price) || empty($brands) || empty($start_date) || empty($end_date) || empty($user_type))
            exit('信息不全');
        DB::beginTransaction();
        try {
            $id = DB::table('coupon')->insertGetId([
                'goods_price' => trim($goods_price) * 100,
                'discount_price' => trim($discount_price) * 100,
                'start_date' => date("Y-m-d", strtotime(trim($start_date))),
                'end_date' => date("Y-m-d", strtotime(trim($end_date))),
                'user_type' => implode(",", $user_type)
            ]);
            if (empty($id))
                throw new Exception('优惠券创建失败');
            $data = [];
            foreach ($brands as $brand) {
                $data[] = ['coupon_id' => $id, 'brand_id' => $brand];
            }
            $rs = DB::table('coupon_brand')->insert($data);
            if (!$rs)
                throw new Exception('优惠券创建失败');
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            exit($e->getMessage());
        }
    }
}