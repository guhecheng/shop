<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/21
 * Time: 12:15
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Excel;
use model\parter\emp\emp;

class UserController extends Controller {
    private $is_deleted = 1;        //已删除
    private $no_deleted = 0;        // 未删除

    /**
     * 用户展示信息页 (存在查询条件bug)
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function index(Request $request) {
        $user_name = empty($request->input("user_name")) ? '' : trim($request->input('user_name'));
        $level_ids = $request->input('level_ids') ?? '';
        if (!empty($user_name))
            if (is_numeric($user_name)) {
                if (strlen($user_name) >= 10)
                    $where[] = ['phone', '=', $user_name];
                else
                $where[] = ['userid', '=', $user_name];
            }
            else
                $where[] = ['uname', 'like', "%{$user_name}%"];
        $where[] = ['is_delete', '=', $this->no_deleted];

        $db = DB::table('user')->where($where);
        if (!empty($level_ids))
            $db = $db->whereIn('level', explode(',', rtrim($level_ids, ',')));

        // 获取对应等级会员人数
        $count = DB::select("select count(*) cnt,level from user group by level order by level asc");
        // 获取会员列表
        $users = $db->orderBy('create_time', 'desc')->paginate(20);
        return view('admin.user.index', ['users' => $users, 'count' => $count, 'user_name' => empty($user_name) ?'':$user_name, 'level_ids' => rtrim($level_ids, ',')]);
    }

    /**
     * 删除用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request) {
        if (!empty($request->input('uid'))) {
            $rs = DB::table('user')->where('userid', $request->input('uid'))
                ->update(['is_delete' => $this->is_deleted]);
        }
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    /**
     * 导入用户
     */
    public function userExport(Request $request) {
        set_time_limit(0);
        if ($request->method() == 'POST') {
            $file = $request->file('upload');
            if (empty($file)) {
                return view('/admin/user/userexport', ['error' => '没有选择文件']);
            }
            if ( $file->isValid() ) {
                $ext = $file->getClientOriginalExtension(); // 文件扩展
                $realPath = $file->getRealPath();
                if ($ext != 'xls' && $ext != 'xlsx')
                    return view('/admin/user/userexport', ['error' => '文件类型不对']);
                $insert_num = $repeat_phone = 0;
                Excel::load($realPath, function ($reader) use (&$insert_num, &$repeat_phone) {
                    // 将第一张表数据导入转成数据
                    $data = $reader->getSheet(0)->toArray();
                   /* foreach ($data as $key => $item) {
                        if ($key == 0) continue;
                        if (($key== 0) || empty($item[0]) || empty($item[1]) || empty($item[3])) continue;
                        $count = DB::table('olduser')->where('phone', $item[1])->count();
                        if ($count) {
                            $repeat_phone++;
                            continue;
                        }
                        $id = DB::table('olduser')->insertGetId(
                            ['name'=>$item[0], 'phone'=>$item[1], 'password'=>'1234']
                        );
                        $sex = trim($item[3]) == '男' ? '1' : ($item[3] == '女' ? 2 : 0);
                        DB::table('children')->insert([
                            ['name'=>$item[2], 'sex'=>$sex, 'birth_date'=>$item[4], 'parent_id' => $id]
                        ]);
                        $insert_num++;
                    }*/
                    foreach ($data as $key => $item) {
                        if ($key == 0) continue;
                        if (($key== 0) || empty($item[1])) continue;
                        $user = DB::table('olduser')->where('phone', $item[1])->first();
                        $parent_data = [
                            'name' => empty(trim($item[0])) ? '' : trim($item[0]),
                            'phone' => trim($item[1]),
                            'password' => '1234'
                        ];
                        if (isset($item[5]) && !empty(trim($item[5])))
                            $parent_data['score'] = intval(trim($item[5]));

                        $sex = trim($item[3]) == '男' ? '1' : ($item[3] == '女' ? 2 : 0);
                        $name = trim($item[2]) == '未填写' ? '' : trim($item[2]);
                        $birth_date = trim($item[4]);
                        if (!empty($sex) || !empty($name) || !empty($birth_date))
                            $children_data = [
                                'sex' => $sex,
                                'name' => $name,
                                'birth_date' => $birth_date
                            ];
                        if (empty($user)) {
                            $id = DB::table('olduser')->insertGetId($parent_data);
                            if (!empty($children_data))
                                DB::table('children')->insert(array_merge($children_data, ['parent_id' => $id]));
                        } else {
                            DB::table('olduser')->where('id', $user->id)->update($parent_data);
                            if (!empty($children_data))
                                DB::table('children')->where('parent_id', $user->id)->update($children_data);
                        }

                    }
                });
                return view('/admin/user/userexport', ['error' => '成功添加' . $insert_num .'人， 重复增加' . $repeat_phone.'手机号码']);
            }
        }
        return view('admin.user.userexport');
    }

    public function upload(Request $request) {

        return view('/admin/user/userexport', ['error' => '没有选择文件']);
    }

    public function usercard() {
        $cards = DB::table('user')->where('card_no', '>', '')->paginate(20);
        return view('admin.user.card', ['cards' => $cards]);
    }

    /**
     * 展示个人信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function info(Request $request) {
        $userid = $request->input('userid');
        if (empty($userid)) exit;
        $user = DB::table('user')->where('userid', $userid)->first();
        $child = DB::table('children')->where('relate_id', $user->child_id)->first();

        $orders = DB::table('orderinfo')->leftJoin('order', 'order.order_no', '=', 'orderinfo.order_no')
            ->select('orderinfo.*', 'order.count', 'order.price as per_price', 'order.skuid')
            ->where(['uid' => $userid, 'orderinfo.status'=>2, 'is_comm'=> 0] )->orderBy('order.order_no', 'asc')->get();

        if (empty($orders))
            return view('admin.order.index', ['orders' => null]);
        // 没有获取到数据
        $skuids = $order_no_items = [];
        foreach ($orders as $item) {
            if (!empty($item->skudi))
                $skuids[] = $item->skuid;
            if (key_exists($item->order_no, $order_no_items)) {
                $order_no_items[$item->order_no] += 1;
            } else
                $order_no_items[$item->order_no] = 1;
        }

        if (!empty($skuids)) {
            $sql = "select * from goodsproperty as gp
                left join propertykey as pk on pk.key_id=gp.key_id
                left join propertyvalue as pv on pv.value_id=gp.value_id
                left join goods on goods.goodsid=gp.goods_id
                where gp.sku_id in (".implode(',', $skuids).")";
            $skus = DB::select($sql);
            foreach ($orders as &$item) {
                foreach ($order_no_items as $k=>$v) {
                    if ($k == $item->order_no) {
                        $item->times = $v;
                    }
                }
                $item->property = '';
                foreach ($skus as $value) {
                    if ($value->sku_id == $item->skuid) {
                        $item->goodsicon = $value->goodsicon;
                        $item->goodsname = $value->goodsname;
                        $item->property .= $value->key_name . ':' . $value->value_name . " ";
                    }
                }
            }
        }

        $scores = DB::table("scorechange")->where("uid", $userid)->get();

        $level_coupons = DB::table('user_levelup_coupon')->where([['uid', '=', $userid], ['openid', '!=', '']])->get();

        return view('admin.user.info', ['userid'=> $userid,'user'=>$user,
                                        'child' => $child,
                                        'orders'=>$orders,
                                        'scores' => $scores,
                                        'level_coupons' => $level_coupons]);
    }


    /**
     * 增加备注
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addremark(Request $request) {
        $userid = $request->input("userid");
        $remark = $request->input('remark');
        if (empty($remark) || empty($userid))
            return response()->json(['rs' => 0, 'errmsg' => '信息不全']);
        return DB::table("user")->where("userid", $userid)->update(['remark'=>trim($remark)]);
    }

    public function addmoney(Request $request) {
        $userid = $request->input('userid');
        $money = $request->input('money');
        if (empty($userid) || empty($money)) {
            return response()->json(['rs' => 0, 'errmsg' => '缺少信息']);
        }

        DB::beginTransaction();
        try {
            $user = DB::table('user')->where("userid", $userid)->first();
            if (empty($user)) {
                throw new \Exception('用户不存在');
            }

            if ($money < 0  || $user->level >= 4) {
                $level = $user->level;
            } else {
                $card = DB::table('card')->where([
                    ['is_delete', '=', 0],
                    ['card_id', '!=', 5],
                    ['card_score', '<=', intval($user->total_score + $money)]
                ])->orderBy('card_level', 'desc')->select('card_level')->limit(1)->first();
                if (!empty($card->card_level)) {
                    $level = $user->level > $card->card_level ? $user->level : $card->card_level;
                }  else
                    $level = $user->level;

                if ($level > $user->level && $level > 1) {
                    $count = DB::table('user_levelup_coupon')->where([['uid', '=', $userid], ['type', '=', $level-1]])->count();
                    if ($count < $level)
                        for ($i = 0; $i < $level - $count; $i++)
                            DB::table("user_levelup_coupon")->insert(['uid'=>$userid,
                                'type'=>$level-1,
                                'start_at' => date("Y-m-d"),
                                'end_at' => date("Y-m-d", time() + 30 * 24 * 3600)]);
                }

            }

            if (empty($user->card_no)) {
                $card_no = DB::table("user")->max("card_no");
                if (!empty($card_no)) {
                    $card_no = str_pad(intval($card_no) + 1, 8, "0", STR_PAD_LEFT);
                } else
                    $card_no = '00000001';
            } else {
                $card_no = $user->card_no;
            }
            $user_rs = DB::table('user')->where("userid", $userid)->update([
                'money' => $user->money + $money * 100,
                'level' => $level,
                'score' => intval($money)<=0 ? $user->score : ($user->score + intval($money)),
                'total_score' => intval($money) <= 0 ? $user->total_score : ($user->total_score + intval($money)),
                'card_no' => $card_no
            ]);
            if (!$user_rs) {
                throw new \Exception("修改用户金额失败");
            }
            DB::table('add_money_log')->insert([
                'money' => $money * 100,
                'act_user' => $request->session()->get('sysuid'),
                'userid' => $userid
            ]);
            DB::table('usertransmoney')->insert([
                'uid' => $userid,
                'trans_money' => $money * 100,
                'trans_type' => 2
            ]);
            if (!empty($add_score = intval($money)) && intval($money) > 0) {
                DB::table('scorechange')->insert([
                    'type' => 3,
                    'paytype' => 3,
                    'score' => $add_score,
                    'uid' => $userid
                ]);
            }
            DB::commit();
            return response()->json(['rs' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['rs' => 0]);
        }
    }

    /**
     * 增加积分
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addscore(Request $request) {
        $userid = $request->input('userid');
        $score = $request->input('score');
        if (empty($userid) || empty($score) || $score <= 0) {
            return response()->json(['rs' => 0, 'errmsg' => '缺少信息']);
        }

        DB::beginTransaction();
        try {
            $user = DB::table('user')->where("userid", $userid)->first();
            if (empty($user)) {
                throw new \Exception('用户不存在');
            }

            if ($user->level < 4) {
                $card = DB::table('card')->where([
                    ['is_delete', '=', 0],
                    ['card_id', '!=', 5],
                    ['card_score', '<=', intval($user->total_score + $score)]
                ])->orderBy('card_level', 'desc')->select('card_level')->limit(1)->first();
                if (!empty($card->card_level)) {
                    $level = $user->level > $card->card_level ? $user->level : $card->card_level;
                }  else
                    $level = $user->level;
            } else
                $level = $user->level;
            if ($level > $user->level && $level > 1) {
                $count = DB::table('user_levelup_coupon')->where([['uid', '=', $userid], ['type', '=', $level-1]])->count();
                if ($count < $level)
                    for ($i = 0; $i < $level - $count; $i++)
                        DB::table("user_levelup_coupon")->insert(['uid'=>$userid,
                            'type'=>$level-1,
                            'start_at' => date("Y-m-d"),
                            'end_at' => date("Y-m-d", time() + 30 * 24 * 3600)]);
            }

            if (empty($user->card_no)) {
                $card_no = DB::table("user")->max("card_no");
                if (!empty($card_no)) {
                    $card_no = str_pad(intval($card_no) + 1, 8, "0", STR_PAD_LEFT);
                } else
                    $card_no = '00000001';
            } else {
                $card_no = $user->card_no;
            }
            $user_rs = DB::table('user')->where("userid", $userid)->update([
                'level' => $level,
                'score' => intval($score)<=0 ? $user->score : ($user->score + intval($score)),
                'total_score' => $user->total_score + $score,
                'card_no' => $card_no
            ]);
            if (!$user_rs) {
                throw new \Exception("修改用户金额失败");
            }
            if (!empty($add_score = intval($score)) && intval($score) > 0) {
                DB::table('scorechange')->insert([
                    'type' => 3,
                    'paytype' => 3,
                    'score' => $add_score,
                    'uid' => $userid
                ]);
            }
            DB::commit();
            return response()->json(['rs' => 1]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['rs' => 0]);
        }
    }

    /**
     * 查看用户优惠券
     * @param Request $request
     * @return mixed
     */
    public function lookCoupons(Request $request) {
        $uid = $request->input("uid");
        if (empty($uid)) return response()->json(['rs'=>0]);

        // 获取品牌
        $brands = DB::table('brands')->where('is_del', 0)->get();

        $where1 = "user_coupon.is_delete=0 and user_coupon.user_id={$uid} and coupon.is_sub=0 and status=0 and coupon.end_date >=" . date("Y-m-d");
        $where2 = "user_coupon.is_delete=0 and user_coupon.user_id={$uid} and status=0 and coupon.is_sub=1";

        $sql = "(select `coupon`.*, `user_coupon`.*,  coupon.id as coupon_id, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where1}) group by `coupon_brand`.`coupon_id` order by `coupon`.`start_date` desc)
                    union 
                    (select `coupon`.*, `user_coupon`.*, coupon.id as coupon_id, group_concat(coupon_brand.brand_id) as brand_ids from `user_coupon` left join `coupon` on `coupon`.`id` = `user_coupon`.`coupon_id` left join `coupon_brand` on `coupon_brand`.`coupon_id` = `coupon`.`id` where ({$where2}) group by `coupon_brand`.`coupon_id`)";
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

    }

    /**
     * 删除优惠券
     * @param Request $request
     * @return mixed
     */
    public function delCoupon(Request $request) {
        $coupon_id = $request->input('coupon_id');
        if (empty($coupon_id)) return response()->json(['rs'=>0]);
        $rs = DB::table("user_coupon")->where('id', $coupon_id)->update(['is_delete'=>1]);
        if ($rs == 1) {
            DB::table("del_coupon_log")->insert([
                'admin_id' => $request->session()->get('sysuid'),
                'user_coupon_id' => $coupon_id
            ]);
            return response()->json(['rs' => 1]);
        }
        return response()->json(['rs' => 0]);
    }

    public function fit(Request $request) {
        $data = DB::table('user_diamond_fit_info')->orderBy('create_time', 'desc')->paginate(20);
        return view('admin.user.fit', ['data' => $data]);
    }

    public function consulation(Request $request) {
        $data = DB::table('user_oversea_consulation')->orderBy('create_time', 'desc')->paginate(20);
        return view('admin.user.consulation', ['data' => $data]);
    }


    public function addsinglemoney(Request $request) {
        $money = $request->input('money');
        $user_id = $request->input('userid');

        if (empty($money) || empty($user_id)) {
            return response(['rs' => 0]);
        }
        $user = DB::table("user")->where('userid', $user_id)->first();
        if (empty($user))
            return response(['rs' => 0]);
        if ($money < 0 && abs($money) > $user->money / 100)
            return response(['rs' => 0]);
        $rs = DB::table('user')->where(['userid' => $user_id])->update(['money' => $user->money + 100 * $money]);

        DB::table('add_money_log')->insert([
            'money' => $money * 100,
            'act_user' => $request->session()->get('sysuid'),
            'userid' => $user_id
        ]);
        return response(['rs' => empty($rs) ? 0 : 1]);
    }


    public function secondSale(Request $request) {
        $records = DB::table('user_luxury_sale')->orderBy('create_time', 'desc')->paginate(20);
        return view('admin.user.secondsale', ['records' => $records]);
    }

    public function addCoupon(Request $request) {
        $users = DB::table('user')->where([['level', '>=', 2], ['is_delete', '=', '0']])->get();
        foreach ($users as $user) {
            if ($user->level==2) {
                $coupon = DB::table('user_levelup_coupon')->where([['uid', '=', $user->userid], ['type', '=', 0]])->first();
                if (!empty($coupon))
                    continue;
                DB::table('user_levelup_coupon')->insert([
                    'uid' => $user->userid,
                    'type' => 0,
                    'start_at' => date("Y-m-d"),
                    'end_at' => date("Y-m-d", time() + 30 * 24 * 3600)
                ]);
            } else if($user->level > 2) {
                for ($i = 2; $i <= $user->level; $i++) {
                    $coupon = DB::table('user_levelup_coupon')->where([['uid', '=', $user->userid], ['type', '=', $user->level - 2]])->first();
                    if(empty($coupon))
                        DB::table('user_levelup_coupon')->insert([
                            'uid' => $user->userid,
                            'type' => 0,
                            'start_at' => date("Y-m-d"),
                            'end_at' => date("Y-m-d", time() + 30 * 24 * 3600)
                        ]);
                }
            }
        }
    }

    public function test(Request $request) {
        return false;
    }
}