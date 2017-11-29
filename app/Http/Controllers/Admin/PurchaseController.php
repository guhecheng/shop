<?php
/**
 *
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB, Log;
use Illuminate\Support\Facades\Storage;

/**
 * 商品操作
 * Class PurchaseController
 * @package App\Http\Controllers\Admin
 */
class PurchaseController extends Controller
{
    public function index(Request $request) {
        $purchases = DB::table('user_purchase')->orderBy('create_time', 'desc')->paginate(20);
        return view('admin.purchase.index', ['purchases'=>$purchases]);
    }

    /**
     * 展示增加代购链接页面
     * @param Request $request
     * @return mixed
     */
    public function add(Request $request)
    {
        $purchase_id = $request->input('purchase_id');
        if (empty($purchase_id))
            return back()->with('error', '数据缺失');
        $purchase = DB::table('user_purchase')->where('id', $purchase_id)->first();
        return view('admin.purchase.add', ['purchase' => $purchase]);
    }

    /**
     *
     * @param Request $request
     * @return mixed
     */
    public function mod(Request $request) {
        $goodsname = $request->input('goodsname');
        $goodsprice = $request->input("goodsprice");
        $logo = $request->input('logo');
        $imglist = $request->input('imglist');
        $content = $request->input('content');
        $act_price = $request->input('act_price');
        $goods_id = $request->input('goods_id');

        if (empty($goodsname) || empty($goodsprice)  || empty($content))
            return back()->with('error', '没有填写完整');

        $purchase = [
            'goodsname' => trim($goodsname),
            'price' => $goodsprice * 100,
            'goodsdesc' => $content,
            'act_price' => empty($act_price) ? 0 : $act_price * 100,
        ];
        if (!empty($logo))
            $purchase['goodsicon'] = $logo;
        if (!empty($imglist))
            $purchase['goodspic'] = $imglist;


        $update_row = DB::table('goods')->where('goodsid', $goods_id)->update($purchase);
        if ($update_row) {
            return redirect('/admin/purchase');
        }
        return back()->with('error', '修改失败');
    }

    /**
     * 增加商品
     * @param Request $request
     * @return mixed
     */
    public function create(Request $request) {
        $goodsname = $request->input('goodsname');
        $goodsprice = $request->input("goodsprice");
        $logo = $request->input('logo');
        $imglist = $request->input('imglist');
        $content = $request->input('content');
        $act_price = $request->input('act_price');
        $purchase_id = $request->input('purchase_id');

        if (empty($goodsname) || empty($goodsprice) || empty($purchase_id) || empty($imglist) || empty($content))
            return back()->with('error', '缺少信息');

        DB::beginTransaction();
        $goodsid = DB::table('goods')->insertGetId([
            'goodsname' => trim($goodsname),
            'goodsicon' => $logo,
            'goodspic' => $imglist,
            'price' => $goodsprice * 100,
            'goodsdesc' => $content,
            'act_price' => empty($act_price) ? 0 : $act_price * 100,
        ]);
        if (!empty($goodsid)) {
            DB::table("user_purchase")->where('id', $purchase_id)->update(['goods_id'=>$goodsid]);
            DB::commit();
            return redirect('/admin/purchase')->with('success', '产生成功');
        }
        else
            DB::rollback();

    }

    /**
     * 修改退款状态
     *
     */
    public function sureBack(Request $request) {
        $purchase_id = $request->input('purchase_id');
        if (empty($purchase_id))
            return response()->json(['rs' => 0, 'errmsg' => '缺少数据']);
        $rs = DB::table('user_purchase')->where('id', $purchase_id)->update(['is_back' => 1]);
        if ($rs)
            return response()->json(['rs' => 1]);
        else
            return response()->json(['rs' => 0, 'errmsg' => '修改失败']);
    }

    /**
     * 展示修改页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View|string
     */
    public function modify(Request $request) {
        $purchase_id = $request->input('purchase_id');
        if (empty($purchase_id))
            return back()->with('error', '数据缺失');
        $purchase = DB::table('user_purchase')->leftJoin('goods', 'goods.goodsid', '=', 'user_purchase.goods_id')->where('id', $purchase_id)->first();
        return view('admin.purchase.modify', ['purchase' => $purchase]);
    }
}