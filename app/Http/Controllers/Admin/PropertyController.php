<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/21
 * Time: 22:40
 */
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PropertyController extends Controller {

    public function index(Request $request) {
        $typeid = $request->input('typeid');
        $propertys = DB::table('propertykey')->where([
                    ['type_id', '=', $typeid],
                    ['is_delete', '=', 0]
                    ])
                    ->get();
        $type = DB::table('goodstype')->where('typeid', $typeid)->select('typename')->first();
        return view('admin.property.index', ['propertys' => $propertys, 'typeid' => $typeid, 'typename'=>$type->typename]);
    }

    public function addkey(Request $request) {
        $typeid = $request->input('typeid');
        $propkey = $request->input('keyname');
        $is_enum = $request->input('is_enum');
        if (!empty($typeid) && !empty($propkey)) {
            DB::table('propertykey')->insert([
                'type_id' => $typeid,
                'key_name' => $propkey,
                'is_enum' => empty($is_enum) ? 0 : 1,
            ]);
        }
        return redirect('/admin/property?typeid=' . $typeid);
    }

    public function deletekey(Request $request) {
        $keyid = $request->input('keyid');
        if ($keyid) {
            $rs = DB::table('propertykey')->where('key_id', $keyid)
                                    ->update(['is_delete' => 1]);
        }
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }

    public function modifykey(Request $request) {
        $typeid = $request->input('typeid');
        $keyid = $request->input('keyid');
        $keyname = $request->input('keyname');
        $is_enum = $request->input('edit_is_enum');
        if (!empty($keyid) && !empty($keyname)) {
            DB::table('propertykey')->where('key_id', $keyid)
                ->update([
                'key_name' => $keyname,
                'is_enum' => empty($is_enum) ? 0 : 1,
            ]);
        }
        return redirect('/admin/property?typeid=' . $typeid);
    }

    public function listvalue(Request $request) {
        $keyid = $request->input('keyid');
        $typeid = $request->input('typeid');
        $values = DB::table('propertyvalue')->where([
                            ['key_id', '=', $keyid],
                            ['is_delete', '=', 0]
                    ])->get();
        $type = DB::table('goodstype')->where('typeid', $typeid)->select('typename')->first();
        $key = DB::table('propertykey')->where('key_id', $keyid)->select('key_name')->first();
        return view('admin.property.listvalue', ['values' => $values, 'keyid' => $keyid,
            'typeid'=>$typeid, 'typename'=>$type->typename, 'key_name'=>$key->key_name]);
    }

    public function addvalue(Request $request) {
        $keyid = $request->input('keyid');
        $valuename = $request->input('valuename');
        if (!empty($keyid) && !empty($valuename))
            DB::table('propertyvalue')->insert([
                                    'key_id' => $keyid,
                                    'value_name' => $valuename
                                ]);
        return redirect('/admin/property/listvalue?keyid=' . $keyid );
    }

    public function modifyvalue(Request $request) {
        $valueid = $request->input('valueid');
        $keyid = $request->input('keyid');
        $valuename = $request->input('valuename');
        if (!empty($keyid) && !empty($valuename)) {
            DB::table('propertyvalue')->where('value_id', $valueid)
                ->update([
                    'value_name' => $valuename,
                ]);
        }
        return redirect('/admin/property?keyid=' . $keyid);
    }

    public function deletevalue(Request $request) {
        $valueid = $request->input('valueid');
        if ($valueid) {
            $rs = DB::table('propertyvalue')->where('value_id', $valueid)
                ->update(['is_delete' => 1]);
        }
        return response()->json(['rs' => empty($rs) ? 0 : 1]);
    }
}