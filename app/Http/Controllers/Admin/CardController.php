<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CardController extends Controller
{
    private $no_delete = 0;     // 未删除
    private $deleted = 1;       // 已删除
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cards = DB::table('card')->where('is_delete', '=', $this->no_delete)->orderBy('card_score', 'desc')->get();
        return view('admin.card.list', ['cards' => $cards]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $card_name = $request->input("cardname");
        $card_score = $request->input("cardscore");
        $file = $request->file('add_img');
        if ($file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'card/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool) {
                DB::table('card')->insert([
                    'card_name' => $card_name,
                    'card_score' => $card_score,
                    'card_img' => '/uploads/' . $fileName
                ]);
            }
        }
        return redirect('/admin/card');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$card_name = $request->input("cardname");
        $card_score = $request->input("cardscore");
        $file = $request->file('update_img');
        if (!empty($file) && $file->isValid()) {
            $ext = $file->getClientOriginalExtension(); // 文件扩展
            $type = $file->getClientMimeType();
            $realPath = $file->getRealPath();
            $fileName = 'card/' . date('Y-m-d-H-i-s').'-'.uniqid().'.'.$ext;
            $bool = Storage::disk('uploads')->put($fileName, file_get_contents($realPath));
            if ($bool) {
                DB::table('card')->where('card_id', $id)
                    ->update([
                    /*'card_name' => $card_name,*/
                    'card_score' => $card_score,
                    'card_img' => '/uploads/' . $fileName
                ]);
            }
        } else {
            if (empty($card_score)) return redirect('/admin/card');
            DB::table('card')->where('card_id', $id)
                ->update([
                    /*'card_name' => $card_name,*/
                    'card_score' => $card_score,
                ]);
        }
        return redirect('/admin/card');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rs = DB::table('card')->where('card_id', $id)
                        ->update(['is_delete' => $this->deleted]);
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function recharge(Request $request) {
        $search_no = $request->input('search_no');
        $search_name = $request->input('search_name');
        $where[] = ['cardrecharge.status', '=', 1];
        $where = [];
        if (!empty($search_no))
            $where[] = ['charge_no', '=', $search_no];
        if (!empty($search_name)) {
            $where[] = ['user.uname', '=', $search_name];
        }
        /*$records = DB::table('cardrecharge')
            ->leftJoin('user', 'cardrecharge.uid', '=', 'user.userid')
            ->orderBy('pay_time', 'desc')->select('cardrecharge.*', 'user.uname')->where($where)->paginate(20);*/
        $records = DB::table('user')->leftJoin("cardrecharge", 'cardrecharge.uid', '=', 'user.userid')->where($where)->orderBy('cardrecharge.pay_time', 'desc')->groupBy('cardrecharge.uid')
                    ->select('user.*', 'cardrecharge.pay_time')->paginate(20);
        return view('/admin/card/recharge', ['records' => $records, 'search_no'=>$search_no, 'search_name'=>$search_name]);
    }
}
