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

        return view('admin.property.index', ['propertys' => $propertys, 'typeid' => $typeid]);
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
        $values = DB::table('propertyvalue')->where([
                            ['key_id', '=', $keyid],
                            ['is_delete', '=', 0]
                    ])->get();
        return view('admin.property.listvalue', ['values' => $values, 'keyid' => $keyid]);
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