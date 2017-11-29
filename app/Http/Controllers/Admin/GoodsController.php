<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/21
 * Time: 19:55
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB, Log;
use Illuminate\Support\Facades\Storage;

/**
 * 商品操作
 * Class GoodsController
 * @package App\Http\Controllers\Admin
 */
class GoodsController extends Controller {

    public function index(Request $request) {
        $brand_id = empty($request->input('brand_id')) ? '' : $request->input('brand_id');
        $goods_name = empty($request->input('goods_name')) ? '' : $request->input('goods_name');
        $brand = DB::table('brands')->where('is_del', 0)->orderBy('sort', 'desc')->get();
        empty($brand_id) ? '' : $where[] = ['brand_id', '=', $brand_id];
        $where[] =['is_delete', '=', 0];
        if (!empty($goods_name)) $where[] = ['goodsname', 'like', "%{$goods_name}%"];
        $goods = DB::table('goods')->where($where)->paginate(20);
        return view('admin.goods.index', [ 'goods' => $goods, 'brand' => $brand, 'brand_id' => $brand_id, 'goods_name' => $goods_name]);
    }

    public function add() {
        $brands = DB::table('brands')->where('is_del', 0)->orderBy('sort', 'desc')->get();
        $types = DB::table('goodstype')->where('is_delete', 0)->get();
        if (!empty($types)) {
            foreach ($types as $key=>$type) {
                $cnt = DB::table('propertykey')->where([
                    ['type_id', '=', $type->typeid],
                    ['is_enum', '=', 1]
                ])->count('type_id');
                if (empty($cnt))
                    unset($types[$key]);
            }
        }
        return view('admin.goods.add', ['types' => $types, 'brands' => $brands]);
    }

    public function getTypesByBrand(Request $request) {
        $brand_id = $request->input('brand_id');
        if (empty($brand_id)) exit;
        $types = DB::table('goodstype')->where(['is_delete' => 0, 'brand_id' => $brand_id])->orderBy('sort', 'desc')->get();
        $brand = DB::table('brands')->where('id', $brand_id)->first();
        return response()->json(['types' => $types, 'brand' => $brand]);
    }

    public function getproperty(Request $request) {
        $propertys = DB::table('propertykey')
                    ->leftJoin('propertyvalue', 'propertykey.key_id', '=', 'propertyvalue.key_id')
                    ->where([
                        ['propertykey.type_id', '=', $request->input('typeid')],
                        ['propertykey.is_delete', '=', 0],
                        ['propertyvalue.is_delete', '=', 0]
                    ])
                    ->select('propertykey.*', 'propertyvalue.value_name', 'propertyvalue.is_delete', 'propertyvalue.value_id')
                    ->get();
        return response()->json(['propertys' => $propertys]);
    }

    public function upload(Request $request) {
        $file = $request->file('img');
        if ($file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'luxury/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool)
                return response()->json(['imgurl' => '/uploads/' . $fileName]);
        }
    }

    public function create(Request $request) {
        $goodsname = $request->input('goodsname');
        $goodsprice = $request->input("goodsprice");
        $goodstype = $request->input('goodstype');
        $logo = $request->input('logo');
        $imglist = $request->input('imglist');
        $is_hot = $request->input('is_hot');
        $score_award = $request->input('score_award');
        $content = $request->input('content');
        $discount_num = $request->input('discount_num');
        $act_price = $request->input('act_price');
        $brand_id = $request->input('goodsbrand');
        $common_discount = $request->input('common_discount');
        $ordinary_discount = $request->input('ordinary_discount');
        $golden_discount = $request->input('golden_discount');
        $platinum_discount = $request->input('platinum_discount');
        $diamond_discount = $request->input('diamond_discount');
        if (empty($goodsname) || empty($goodsprice) || empty($goodstype) || empty($logo)
            || empty($imglist) || empty($content) || empty($request->input('price'))) {
            echo "<script>alert('缺少信息,添加失败'); history.go(-1)</script>";exit;
        }
            //return redirect('admin/goods')->with('error', '缺少信息');

        DB::beginTransaction();
        $goodsid = DB::table('goods')->insertGetId([
            'goodsname' => trim($goodsname),
            'goodsicon' => $logo,
            'goodspic' => $imglist,
            'is_hot' => empty($is_hot) ? 0 : $is_hot,
            'typeid' => $goodstype,
            'price' => $goodsprice * 100,
            'goodsdesc' => $content,
            'brand_id' => $brand_id,
            'is_discount' => empty($request->input('is_discount')) ? 0 : $request->input('is_discount'),
            'score_award' => empty($score_award) ? 0 : $score_award,
            'discount' => empty($discount_num) ? 0 : $discount_num,
            'act_price' => empty($act_price) ? 0 : $act_price * 100,
            'common_discount' => empty($common_discount) ? 0 : $common_discount * 10,
            'ordinary_discount' => empty($ordinary_discount) ? 0 : $ordinary_discount * 10,
            'golden_discount' => empty($golden_discount) ? 0 : $golden_discount * 10,
            'platinum_discount' => empty($platinum_discount) ? 0 : $platinum_discount * 10,
            'diamond_discount' => empty($diamond_discount) ? 0 : $diamond_discount * 10
        ]);
        if ($goodsid) {
            if (!empty($request->input('common_attr'))) {
                foreach ($request->input('common_attr') as $key => $value) {
                    $value_id = DB::table('propertyvalue')->insertGetId([
                        'value_name' => $value[0],
                        'key_id' => $key
                    ]);
                    DB::table('goodsproperty')->insertGetId([
                        'goods_id' => $goodsid,
                        'key_id' => $key,
                        'value_id' => $value_id
                    ]);
                }
            }
            if (!empty($request->input('add_attr_key'))) {
                foreach ($request->input('add_attr_key') as $key=>$value) {
                    if ($request->input['add_attr_value'][$key]) {
                        $key_id = DB::table('propertykey')->insertGetId([
                            'key_name' => $value
                        ]);
                        $value_id = DB::table('propertyvalue')->insertGetId([
                            'value_name' => $this->input['add_attr_value'][$key]
                        ]);
                        DB::table('goodsproperty')->insert([
                            'key_id' => $key_id,
                            'value_id' => $value_id,
                            'goods_id' => $goodsid
                        ]);
                    }
                }
            }

            foreach ($request->input('price') as $key=>$value) {
                if (empty($request->input('num')[$key])) continue;
                $sku_id = DB::table('goodssku')->insertGetId([
                    'goods_id' => $goodsid,
                    'num' => $request->input('num')[$key],
                    'price' => empty($value) ? (empty($act_price) ? $goodsprice * 100 : $act_price * 100 ) : $value * 100
                ]);
                for($i = 1, $len = $request->input('len'); $i <= $len; $i++) {
                    DB::table('goodsproperty')->insert([
                        'sku_id' =>$sku_id,
                        'goods_id' => $goodsid,
                        'is_sku' => 1,
                        'key_id' => $request->input('keys'. $i)[$key],
                        'value_id' => $request->input('values' . $i)[$key]
                    ]);
                }
            }

        } else {
            DB::rollback();
            echo "<script>alert('添加失败'); history.go(-1)</script>";exit;
        }
        DB::commit();
        echo "<script>alert('添加成功'); location.href='/admin/goods'</script>";exit;
    }

    public function delete(Request $request) {
        $goodsid = $request->input('goodsid');
        if (!empty($goodsid))
            $rs = DB::table('goods')->where('goodsid', $goodsid)
                            ->update([
                                'is_delete' => 1
                            ]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function changesale(Request $request) {
        $goodsid = $request->input('goodsid');
        $status = $request->input('status');
        if (!empty($goodsid))
            $rs = DB::table('goods')->where('goodsid', $goodsid)
                ->update([
                    'is_sale' => 1 - $status
                ]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function changehot(Request $request) {
        $goodsid = $request->input('goodsid');
        $status = $request->input('status');
        if (!empty($goodsid))
            $rs = DB::table('goods')->where('goodsid', $goodsid)
                ->update([
                    'is_hot' => $status
                ]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function changead(Request $request) {
        $goodsid = $request->input('goodsid');
        $status = $request->input('status');
        if (!empty($goodsid))
            $rs = DB::table('goods')->where('goodsid', $goodsid)
                ->update([
                    'is_ad' => $status
                ]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    /**
     * 修改商品信息页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function edit(Request $request) {
        $goods_id = $request->input('goods_id');
        if (empty($goods_id)) exit;
        $goods = DB::table('goods')->where('goodsid', $goods_id)->first();
        if (!empty($goods)) {
            $types = DB::table('goodstype')->where('brand_id', $goods->brand_id)->get();
            $brands = DB::table('brands')->where('is_del', 0)->orderBy('sort', 'desc')->get();
            $goods_property = DB::table('goodsproperty')->leftJoin('propertykey', 'propertykey.key_id', '=', 'goodsproperty.key_id')
                ->leftJoin('propertyvalue', 'propertyvalue.value_id', '=', 'propertyvalue.value_id')
                ->leftJoin('goodssku', 'goodssku.sku_id', '=', 'goodsproperty.sku_id')
                ->where(['goodsproperty.goods_id'=>$goods_id])->get();
        }
        return view('admin.goods.edit', ['goods' => $goods, 'goods_property' => $goods_property, 'brands' => $brands, 'types' => $types]);
    }

    public function update(Request $request) {
        $goods_id = $request->input("goods_id");
        $goodsname = $request->input('goodsname');
        $goodsprice = $request->input("goodsprice");
        $goodstype = $request->input('goodstype');
        $logo = $request->input('logo');
        $imglist = $request->input('imglist');
        $is_hot = $request->input('is_hot');
        $content = $request->input('content');
        $act_price = $request->input('act_price');
        $brand_id = $request->input('goodsbrand');
        $common_discount = $request->input('common_discount');
        $ordinary_discount = $request->input('ordinary_discount');
        $golden_discount = $request->input('golden_discount');
        $platinum_discount = $request->input('platinum_discount');
        $diamond_discount = $request->input('diamond_discount');
        if (empty($goodsname) || empty($goodsprice) || empty($content))
            return redirect('admin/goods')->with('error', '数据不全');

        if ($common_discount < 0 || $common_discount > 10)
            return redirect('admin/goods')->with('error', '折扣数据不能小于0不能大于10');
        DB::beginTransaction();
        try {
            $update_data = [
                'goodsname' => trim($goodsname),

                'price' => $goodsprice * 100,
                'goodsdesc' => $content,
                'is_discount' => empty($request->input('is_discount')) ? 0 : $request->input('is_discount'),
                'act_price' => empty($act_price) ? 0 : $act_price * 100,
                'common_discount' => empty($common_discount) ? 0 : $common_discount * 10,
                'ordinary_discount' => empty($ordinary_discount) ? 0 : $ordinary_discount * 10,
                'golden_discount' => empty($golden_discount) ? 0 : $golden_discount * 10,
                'platinum_discount' => empty($platinum_discount) ? 0 : $platinum_discount * 10,
                'diamond_discount' => empty($diamond_discount) ? 0 : $diamond_discount * 10
            ];
            if (!empty($logo))
                $update_data['goodsicon'] = $logo;
            if (!empty($imglist))
                $update_data['goodspic'] = $imglist;
            if (!empty($brand_id)) {
                $update_data['brand_id'] = $brand_id;
            }
            if (!empty($goodstype)) {
                $update_data['typeid'] = $goodstype;
            }
            if (!empty($is_hot))
                $update_data['is_hot'] = $is_hot;
            if (!DB::table('goods')->where('goodsid', $goods_id)->update($update_data))
                throw new \Exception('更新商品失败');

            /*if (!empty($request->input('common_attr'))) {
                foreach ($request->input('common_attr') as $key => $value) {
                    $value_id = DB::table('propertyvalue')->insertGetId([
                        'value_name' => $value[0],
                        'key_id' => $key
                    ]);
                    DB::table('goodsproperty')->insertGetId([
                        'goods_id' => $goods_id,
                        'key_id' => $key,
                        'value_id' => $value_id
                    ]);
                }
            }
            if (!empty($request->input('add_attr_key'))) {
                foreach ($request->input('add_attr_key') as $key=>$value) {
                    if ($request->input['add_attr_value'][$key]) {
                        $key_id = DB::table('propertykey')->insertGetId([
                            'key_name' => $value
                        ]);
                        $value_id = DB::table('propertyvalue')->insertGetId([
                            'value_name' => $this->input['add_attr_value'][$key]
                        ]);
                        DB::table('goodsproperty')->insert([
                            'key_id' => $key_id,
                            'value_id' => $value_id,
                            'goods_id' => $goodsid
                        ]);
                    }
                }
            }

            foreach ($request->input('price') as $key=>$value) {
                if (empty($request->input('num')[$key])) continue;
                $sku_id = DB::table('goodssku')->insertGetId([
                    'goods_id' => $goodsid,
                    'num' => $request->input('num')[$key],
                    'price' => empty($value) ? (empty($act_price) ? $goodsprice * 100 : $act_price * 100 ) : $value * 100
                ]);
                for($i = 1, $len = $request->input('len'); $i <= $len; $i++) {
                    if (!DB::table('goodsproperty')->insert([
                        'sku_id' =>$sku_id,
                        'goods_id' => $goodsid,
                        'is_sku' => 1,
                        'key_id' => $request->input('keys'. $i)[$key],
                        'value_id' => $request->input('values' . $i)[$key]
                    ]))
                        throw new Exception('属性插入失败');
                }
            }*/
            DB::commit();
        } catch (Exception $e) {
            Log::error($e);
            DB::rollback();
        }

        return redirect('admin/goods');
    }

    /**
     * 批量修改商品上架状态
     * @param Request $request
     */
    public function batchAct(Request $request) {
        $goods_ids = $request->input('goods_ids');
        $act_type = $request->input('act_type');
        if (empty($goods_ids)) return response()->json(['rs' => 1, 'errmsg' => "缺少数据"]);
        $goods_ids = rtrim($goods_ids, ',');
        DB::update("update goods set is_sale={$act_type} where goodsid in ({$goods_ids})");
        return response()->json(['rs' => 0]);
    }
}