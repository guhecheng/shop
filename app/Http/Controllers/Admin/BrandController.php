<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/8/8
 * Time: 21:28
 */

namespace App\Http\Controllers\Admin;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use model\parter\emp\emp;

class BrandController extends Controller
{

    public function index() {
       $brands = DB::table('brands')->where('is_del', '0')->orderBy('sort')->paginate(10);

       return view('admin.brand.index', ['brands' => $brands]);
    }

    public function add(Request $request) {
        $brand_name = $request->input('brand_name');
        $common_discount = $request->input('common_discount');
        $ordinary_discount = $request->input("ordinary_discount");
        $golden_discount = $request->input('golden_discount');
        $diamond_discount = $request->input('diamond_discount');
        $platinum_discount = $request->input('platinum_discount');

        if (empty($common_discount) || empty($ordinary_discount) || empty($golden_discount) ||
            empty($diamond_discount) || empty($platinum_discount)) {

        }
        if (empty($brand_name))
            exit('没有填写数据');
        $file = $request->file('img');
        if ($file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'brands/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool)
                //return response()->json(['imgurl' => '/uploads/' . $fileName]);
                $brand_img = '/uploads/' . $fileName;
        }
        $rs = DB::table('brands')->insert([
            'brand_name' => trim($brand_name),
            'brand_img' => trim($brand_img),
            'common_discount' => intval($common_discount * 10) ,
            'ordinary_discount' => intval($ordinary_discount * 10),
            'golden_discount' => intval($golden_discount * 10),
            'platinum_discount' => intval($platinum_discount * 10),
            'diamond_discount' => intval($diamond_discount * 10)
        ]);
        if ($rs)
            return redirect('/admin/brand');
    }

    public function del(Request $request) {
        $brand_id = $request->input('brand_id');
        $rs = DB::table('brands')->where('id', $brand_id)->update(['is_del' => 1]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function getBrand(Request $request) {
        $brand_id = $request->input('brand_id');
        if (empty($brand_id)) exit;
        $brand = DB::table('brands')->where(['id'=>$brand_id, 'is_del'=>0])->first();
        return response()->json(['brand' => empty($brand)?'': $brand]);
    }

    public function mod(Request $request) {
        $img = $request->input('img');
        $brand_name = $request->input('brand_name');
        $common_discount = $request->input('common_discount');
        $ordinary_discount = $request->input("ordinary_discount");
        $golden_discount = $request->input('golden_discount');
        $diamond_discount = $request->input('diamond_discount');
        $platinum_discount = $request->input('platinum_discount');
        $brand_id = $request->input('brand_id');

        if (empty($brand_name) || empty($brand_id))
            exit('没有填写数据');
        $file = $request->file('mod_img');
        if (!empty($file) && $file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'brands/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool)
                //return response()->json(['imgurl' => '/uploads/' . $fileName]);
                $brand_img = '/uploads/' . $fileName;
        }
        $data = [
            'brand_name' => trim($brand_name),
            'common_discount' => intval($common_discount * 10),
            'ordinary_discount' => intval($ordinary_discount * 10),
            'golden_discount' => intval($golden_discount * 10),
            'platinum_discount' => intval($platinum_discount * 10),
            'diamond_discount' => intval($diamond_discount * 10)
        ];
        empty($brand_img) ? '' : $data['brand_img'] = trim($brand_img);
        $rs = DB::table('brands')->where('id', $brand_id)->update($data);
        if ($rs)
            return redirect('/admin/brand');
    }
}