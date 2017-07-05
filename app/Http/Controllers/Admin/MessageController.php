<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Excel;
use EasyWeChat\Foundation\Application;

class MessageController extends Controller {

	public function index(Request $request) {
		$messages = DB::table('message')->where('is_delete', 0)->orderBy('create_time', 'desc')
            ->paginate(5);
        return view('admin.message.index', ['messages' => $messages]);
	}

	public function delete(Request $request) {
	    $message_id = $request->input("id");
	    if (empty($message_id))
            return response()->json(['rs' => 0]);

	    $rs = DB::table("message")->where('id', $message_id)->update(['is_delete' => 1]);
        return response()->json(['rs' => $rs]);
    }

    public function add(Request $request) {
	    $content = $request->input('content');
	    if (empty(trim($content))) {
	        return response()->json(['rs' => 0]);
        }
        $rs = DB::table('message')->insert([
            'content' => trim($content)
        ]);
	    return response()->json(['rs' => $rs]);
    }

    public function update(Request $request) {
        $content = $request->input('content');
        $id = $request->input('id');
        if (empty(trim($content)) || empty($id)) {
            return response()->json(['rs' => 0]);
        }
        $rs = DB::table('message')->where('id', $id)->update([
            'content' => trim($content)
        ]);
        return response()->json(['rs' => $rs]);
    }

    public function send(Request $request) {
	    $id = $request->input('id');
	    if (empty($id))
	        return response()->json(['rs' => 0]);
	    $content = DB::table('message')->where('id', $id);
	    var_dump($content);
	    if (!empty($content)) {
	        $app = new Application(config('wx'));
            $broadcast = $app->broadcast;
            $broadcast->sendText($content);
        }
        return response()->json(['rs' => 1]);
    }
}