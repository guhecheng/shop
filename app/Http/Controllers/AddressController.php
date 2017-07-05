<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $uid = $request->session()->get('uid');
        $addresses = DB::table('useraddress')->where('uid', $uid)->get();
        if ($request->input('from_order')) {
            $request->session()->put('orderno', $request->input('orderno'));
        }
        return view('address.index', ['addresses' => $addresses,
                                    'from_order' => $request->input('from_order'),
                                    'orderno' => $request->input('orderno')]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('address/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $uid = $request->session()->get('uid');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()
                    ->json(['rs' => 0, 'msg' => '信息不全']);
        } else {
            DB::beginTransaction();
            if ($request->input('checked')) {
                DB::table('useraddress')->where('uid', $uid)
                    ->update(['is_default' => 0]);
            }
            DB::table('useraddress')->insert(
                ['name' => $request->input('name'),
                    'phone' => $request->input('phone'),
                    'address' => $request->input('address'),
                    'location' => $request->input('location'),
                    'is_default' => $request->input('checked') == 'true' ? 1 : 0,
                    'uid' => $uid
                ]
            );
            DB::listen(function($sql) {
                dump($sql);
            });
            DB::commit();

            return response()->json(['rs' => 1]);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $address = DB::table("useraddress")->where('address_id', $id)->first();
        return view('address.edit', ['address' => $address]);
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
        //
        $uid = $request->session()->get('uid');
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'location' => 'required',
        ]);
        if ($validator->fails()) {
            return response()
                ->json(['rs' => 0, 'msg' => '信息不全']);
        } else {
            DB::beginTransaction();
            if ($request->input('checked')) {
                DB::table('useraddress')->where('uid', $uid)
                    ->update(['is_default' => 0]);
            }
            DB::table('useraddress')->where('address_id', $id)
                ->update(
                        ['name' => $request->input('name'),
                            'phone' => $request->input('phone'),
                            'address' => $request->input('address'),
                            'location' => $request->input('location'),
                            'is_default' => $request->input('checked') == 'true' ? 1 : 0,
                        ]
                    );
            DB::commit();

            return response()->json(['rs' => 1]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function setdefault(Request $request) {
        $address_id = $request->input('address_id');
        $flag = $request->input('flag');
        $uid = $request->session()->get("uid");
        if ($flag) {
            DB::table("useraddress")->where("uid", $uid)
                                    ->update(['is_default' => 0]);
        }
        DB::table("useraddress")->where('address_id', $address_id)
            ->update(['is_default' => $flag]);
        return response()->json(['rs' => 1]);

    }
}
