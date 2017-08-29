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
use Log;

class CouponController extends Controller
{
    private $type = [
        0 => '普通用户',
        1 => '普通会员',
        2 => '黄金会员',
        3 => '铂金会员',
        4 => '钻石会员'
    ];
    public function index() {
        $coupons = DB::table('coupon')
            ->leftJoin('adminuser', 'adminuser.admin_id', '=', 'coupon.add_uid')
            ->orderBy('id', 'desc')
            ->select('coupon.*', 'adminuser.name')
            ->paginate(10);
        if (!empty($coupons)) {
            $ids = [];
            foreach ($coupons as $coupon) {
                $ids[] = $coupon->id;
            }
            if (!empty($ids)) {
                $sql = "select group_concat(brand_id) brands,coupon_id from coupon_brand where coupon_id in (".implode(',', $ids).") group by coupon_id";
                $coupon_brand = DB::select($sql);
                if ($coupon_brand) {
                    foreach ($coupons as &$coupon) {
                        foreach ($coupon_brand as $brand) {
                            if ($brand->coupon_id == $coupon->id)
                                $coupon->brand = $brand->brands;
                        }
                    }
                }
            }
        }
        $brands = DB::table('brands')->where('is_del', '0')->orderBy('sort')->get();
        return view('admin.coupon.index', ['coupons' => $coupons, 'brands' => $brands, 'type'=>$this->type]);
    }


    public function add(Request $request) {
        $goods_price = $request->input('goods_price');
        $discount_price = $request->input('discount_price');
        $brands = $request->input('brands');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $user_type = $request->input('user_type');
        if (empty($goods_price) || empty($discount_price) || empty($brands) || empty($start_date) || empty($end_date) || empty($user_type))
            return back()->with('coupon_info', '信息填写不完整');

        if ($discount_price > $goods_price) {
            return back()->with('coupon_info', '商品价小于优惠价');
        }
        if (strtotime($start_date) >= strtotime($end_date))
            return back()->with('coupon_info', '日期选择错误');

        DB::beginTransaction();
        try {
            $id = DB::table('coupon')->insertGetId([
                'goods_price' => trim($goods_price) * 100,
                'discount_price' => trim($discount_price) * 100,
                'start_date' => date("Y-m-d", strtotime(trim($start_date))),
                'end_date' => date("Y-m-d", strtotime(trim($end_date))),
                'user_type' => implode(",", $user_type),
                'add_uid' => session("sysuid")
            ]);
            if (empty($id))
                throw new Exception('优惠券创建失败');
            // 获取到对应的用户
            $users = DB::table('user')->whereIn('level', $user_type)->get();

            $data = [];
            $insert_user_coupon = [];
            foreach ($brands as $brand) {
                $data[] = ['coupon_id' => $id, 'brand_id' => $brand];
            }

            if (!empty($users)) {
                foreach ($users as $user) {
                    $insert_user_coupon[] = ['user_id'=>$user->userid, 'coupon_id' => $id];
                }
            }

            if (!DB::table('coupon_brand')->insert($data))
                throw new Exception('优惠券创建失败');

            if (!DB::table('user_coupon')->insert($insert_user_coupon))
                throw new Exception('分配优惠券失败');

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            exit($e->getMessage());
            return back()->with('coupon_info', '增加优惠券失败');
        }
        return back()->with('coupon_info', '添加成功');
    }

}