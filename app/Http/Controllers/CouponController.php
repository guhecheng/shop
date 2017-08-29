<?php
namespace App\Http\Controllers;

use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CouponController extends Controller {
    public function index(Request $request) {
        $type = $request->input('status');
        switch ($type ?: 1) {
            case 1:
                $where = "status=0 and coupon.end_date >=" . date("Y-m-d");
                break;
            case 2:
                $where = "status=1";
                break;
            case 3:
                $where = "status=0 and coupon.end_date<" . date("Y-m-d");
                break;
        }

        $brands = DB::table('brands')->where('is_del', 0)->get();
        $sql = "select `user_coupon`.*, `coupon`.*, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where}) group by `coupon_brand`.`coupon_id` order by `coupon`.`start_date` desc";
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
}