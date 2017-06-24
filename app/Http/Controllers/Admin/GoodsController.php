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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GoodsController extends Controller {

    public function index() {
        $goods = DB::table('goods')->where('is_delete', 0)->get();
        return view('admin.goods.index', [ 'goods' => $goods]);
    }

    public function add() {
        $types = DB::table('goodstype')->where('is_delete', 0)->get();
        return view('admin.goods.add', ['types' => $types]);
    }

    public function getproperty(Request $request) {
        $propertys = DB::table('propertykey')
                    ->leftJoin('propertyvalue', 'propertykey.key_id', '=', 'propertyvalue.key_id')
                    ->where([
                        ['propertykey.type_id', '=', $request->input('typeid')],
                        ['propertykey.is_delete', '=', 0],
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
            $fileName = 'goods/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
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
        DB::beginTransaction();
        $goodsid = DB::table('goods')->insertGetId([
            'goodsname' => trim($goodsname),
            'goodsicon' => $logo,
            'goodspic' => $imglist,
            'is_hot' => $is_hot,
            'typeid' => $goodstype,
            'price' => $goodsprice * 100,
            'goodsdesc' => $request->input('content')
        ]);
        if ($goodsid) {
            foreach ($request->input('common_attr') as $key=>$value) {
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

            foreach ($request->input('price') as $key=>$value) {
                if (empty($request->input('num')[$key])) continue;
                $sku_id = DB::table('goodssku')->insertGetId([
                    'goods_id' => $goodsid,
                    'num' => $request->input('num')[$key],
                    'price' => empty($value) ? $goodsprice * 100 : $value * 100
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

        } else
            DB::rollback();
        DB::commit();
        return redirect('admin/goods');
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

    public function changehot(Request $request) {
        $goodsid = $request->input('goodsid');
        $status = $request->input('status');
        if (!empty($goodsid))
            $rs = DB::table('goods')->where('goodsid', $goodsid)
                ->update([
                    'is_sale' => 1 - $status
                ]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }
}