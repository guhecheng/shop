<?php
namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CouponController extends Controller {
    public function index(Request $request) {
        $type = empty($request->input('status')) ? 0 : $request->input('status');
        $uid = $request->session()->get('uid');
        switch ($type) {
            case 1:
                $where1 = "user_coupon.is_delete=0 and user_coupon.user_id={$uid} and status=0 and coupon.is_sub=0 and coupon.end_date >=" . date("Y-m-d");
                $where2 = "user_coupon.is_delete=0 and user_coupon.user_id={$uid} and status=0 and coupon.is_sub=1";
                break;
            case 2:
                $where = "user_coupon.is_delete=0 and status=1";
                break;
            case 3:
                $where = "user_coupon.is_delete=0 and status=0 and coupon.is_sub=0 and coupon.end_date<" . date("Y-m-d");
                break;
            case 0:
                $where = "coupon.coupon_type=2 and num>0";
        }

        $brands = DB::table('brands')->where('is_del', 0)->get();
        if (!empty($where)) {
            if (empty($type))
                $sql = "select `coupon`.*, group_concat(coupon_brand.brand_id) as brand_ids from `coupon` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where}) group by `coupon_brand`.`coupon_id` order by `coupon`.`start_date` desc";
            else
                $sql = "select `user_coupon`.*, `coupon`.*, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where}) group by `coupon_brand`.`coupon_id` order by `coupon`.`start_date` desc";
        } else
            $sql = "(select `user_coupon`.*, `coupon`.*, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where1}) group by `coupon_brand`.`coupon_id` order by `coupon`.`start_date` desc)
                    union 
                    (select `user_coupon`.*, `coupon`.*, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where2}) group by `coupon_brand`.`coupon_id`)";

        $coupons = DB::select($sql);
        if (!empty($coupons)) {
            foreach ($coupons as &$coupon) {
                $ids = explode(',', $coupon->brand_ids);
                $brand_names = [];
                foreach ($brands as $brand) {
                    if (in_array($brand->id, $ids))
                        $brand_names[] = $brand->brand_name;
                }
                $coupon->brand_names = !empty($brand_names) ? implode(',', $brand_names) : '';
                $coupon->start_date = date("Y年m月d日", strtotime($coupon->start_date));
                $coupon->end_date = date("Y年m月d日", strtotime($coupon->end_date));
            }
        }
        if ($request->ajax())
            return response()->json(['coupons' => empty($coupons) ? '' : $coupons]);
        return view('coupon', ['coupons' => $coupons, 'brands' => $brands]);
    }

    public function getCoupon(Request $request) {
        $coupon_id = $request->input('coupon_id');
        if (empty($coupon_id))
            return response()->json(['rs'=>0, 'errmsg'=>'信息错误']);

        try {
            DB::beginTransaction();
            $coupon = DB::table('coupon')->where('id', $coupon_id)->first();
            if (empty($coupon) || $coupon->type !=2)
                throw new Exception('优惠券不存在');
            if ($coupon->num <= 0)
                throw new Exception('优惠券已经领完了');
            $uid = $request->session()->get('uid');
            $user_coupon = DB::table('user_coupon')->where([
                ['user_id', '=', $uid],
                ['coupon_id', '=', $coupon_id]
            ])->first();
            if (!empty($user_coupon))
                throw new Exception('优惠券已被领取');

            $insert_rs = DB::table('user_coupon')->insert([
                'coupon_id' => $coupon_id,
                'num' => 1,
                'user_id' => $uid
            ]);
            if (!$insert_rs)
                throw new Exception('领取优惠券失败');

            $update_rs = DB::table('coupon')->where('id', $coupon_id)->decrement('num', 1);
            if (!$update_rs)
                throw new Exception('领取优惠券失败');

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['rs' => 0, 'errmsg'=> $e->getMessage()]);
        }
        return response()->json(['rs' => 1]);
    }
}