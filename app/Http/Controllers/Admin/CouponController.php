<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/8/8
 * Time: 21:27
 */

namespace App\Http\Controllers\Admin;

use DB,Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $users = DB::table('user')->get();
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
        return view('admin.coupon.index', ['coupons' => $coupons, 'brands' => $brands, 'type'=>$this->type, 'users' => $users]);
    }


    public function add(Request $request) {
        $goods_price = empty($request->input('goods_price')) ? 0 : $request->input('goods_price');
        $discount_price = $request->input('discount_price') ?? 0;
        $brands = $request->input('brands');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $coupon_discount = $request->input('coupon_discount') ?? 0;
        $user_type = $request->input('user_type');
        $name = $request->input('name');
        $type = $request->input('type');
        $coupon_type = $request->input('coupon_type');
        $send_content = $request->input('send_content');
        $add_num = $request->input('add_num');
        if (empty($start_date) || empty($end_date))
            return back()->with('coupon_info', '信息填写不完整');
        if ((empty($discount_price) && empty($goods_price)) && empty($coupon_discount))
            return back()->with('coupon_info', '信息填写不完整');
        if (empty($coupon_type) && empty($user_type) && empty($name))
            return back()->with('coupon_info', '必须选择优惠券发送者');

        if ($type!=1 && $discount_price > $goods_price) {
            return back()->with('coupon_info', '商品价小于优惠价');
        }
        if (!empty($discount_price) && !empty($goods_price)) {
            $coupon_discount = 0;
            $discount_type = 0;
        } else
            $discount_type = 1;

        if (strtotime($start_date) >= strtotime($end_date))
            return back()->with('coupon_info', '日期选择错误');
        if ($coupon_type == 3 && empty($send_content))
            return back()->with('coupon_info', '回复优惠券缺少必须添加回复信息');
        DB::beginTransaction();
        try {
            $data = [
                'goods_price' => trim($goods_price) * 100,
                'discount_price' => trim($discount_price) * 100,
                'start_date' => date("Y-m-d", strtotime(trim($start_date))),
                'end_date' => date("Y-m-d", strtotime(trim($end_date))),
                'user_type' => empty($user_type) ? '' : implode(",", $user_type),
                'add_uid' => session("sysuid"),
                'coupon_type' => $coupon_type,
                'type' => $type,
                'num' => $coupon_type == 2 ? $add_num : 0,
                'send_content' => $coupon_type == 3 ? $send_content : '',
                'discount_type' => $discount_type,
                'coupon_discount' => $coupon_discount * 10
            ];
            $id = DB::table('coupon')->insertGetId($data);
            if (empty($id))
                throw new Exception('优惠券创建失败');


            $data = [];
            $insert_user_coupon = [];
            if ($type != 1 ) {
                foreach ($brands as $brand) {
                    $data[] = ['coupon_id' => $id, 'brand_id' => $brand];
                }
                if (!DB::table('coupon_brand')->insert($data))
                    throw new Exception('优惠券创建失败');
            }


            if ($coupon_type < 1){
                if (!empty($name)) {
                    foreach ($name as $item) {
                        $insert_user_coupon[] = ['user_id'=>$item, 'coupon_id' => $id];
                    }
                } else {
                    // 获取到对应的用户
                    $users = DB::table('user')->whereIn('level', $user_type)->get();
                    if (!empty($users)) {
                        foreach ($users as $user) {
                            $insert_user_coupon[] = ['user_id'=>$user->userid, 'coupon_id' => $id];
                        }
                    }
                }
            }



            if (!empty($insert_user_coupon))
                if (!DB::table('user_coupon')->insert($insert_user_coupon))
                    throw new Exception('分配优惠券失败');

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollback();
            return back()->with('coupon_info', '增加优惠券失败');
        }
        return back()->with('coupon_info', '添加成功');
    }

    public function findUser(Request $request) {
        $user_name = $request->input('user_name');
        if (empty($user_name))
            return response()->json(['rs' => 0]);
        $sql = "select * from user where uname like '%{$user_name}%'";
        $users = DB::select($sql);
        return response()->json(['rs' => 1, 'users' => $users]);
    }

}