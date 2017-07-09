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

class UserController extends Controller {
    private $is_deleted = 1;        //已删除
    private $no_deleted = 0;        // 未删除


    public function index(Request $request) {
        $users = DB::table('user')->where('is_delete', $this->no_deleted)
            ->orderBy('create_time', 'desc')
            ->paginate(5);
        return view('admin.user.index', ['users' => $users]);
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

                Excel::load($realPath, function ($reader) {
                    // 将第一张表数据导入转成数据
                    $data = $reader->getSheet(0)->toArray();
                    foreach ($data as $key => $item) {
                        if ($key == 0) continue;
                        $count = DB::table('olduser')->where('phone', $item[1])->count();
                        if ($count) {
                            continue;
                        }

                        $id = DB::table('olduser')->insertGetId(
                            ['name'=>$item[0], 'phone'=>$item[1], 'password'=>'1234']
                        );
                        $sex = trim($item[3]) == '男' ? '1' : ($item[3] == '女' ? 2 : 0);
                        DB::table('children')->insert([
                            ['name'=>$item[2], 'sex'=>$sex, 'birth_date'=>$item[4], 'parent_id' => $id]
                        ]);
                    }
                });
                return view('/admin/user/userexport', ['error' => '加入成功']);
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
}