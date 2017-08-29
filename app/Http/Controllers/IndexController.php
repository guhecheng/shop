<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/6/13
 * Time: 22:15
 */
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EasyWeChat\Foundation\Application;
use Illuminate\Support\Facades\Redis;
use EasyWeChat\Message\News;


class IndexController extends Controller {
    private $app;
    private $url = "http://www.jingyuxuexiao.com";      // 菜单请求地址
    public function __construct() {
        $this->app = new Application(config('wx'));
    }

    /**
     * 微信接收事件
     * @param Request $request
     * @return mixed
     */
    public function wx(Request $request) {
        $server = $this->app->server;
        $userService = $this->app->user;
        $server->setMessageHandler(function ($message) use ($request, $userService) {
            Log::info($message);
            $user = DB::table('user')->where("openid", $message->FromUserName)->first();
            if ($user) {
                DB::table('user')->where('userid', $user->userid)->update([
                    'last_login_time' => date("Y-m-d H:i:s")
                ]);
                $request->session()->put('openid', $message->FromUserName);
                $request->session()->put('uid', $user->userid);
            }
            switch ($message->MsgType) {
                case 'event':
                    switch ($message->Event) {
                        case 'subscribe':
                            $user = DB::table('user')->where("openid", $message->FromUserName)->first();
                            if (empty($user)) {
                                $userinfo = $userService->get($message->FromUserName);
                                $id = DB::table("user")->insertGetId([
                                    'openid'	=>  $message->FromUserName,
                                    'uname'		=>  $userinfo->nickname,
                                    'avatar'	=>	$userinfo->headimgurl,
                                    'sex'		=>  $userinfo->sex
                                ]);
                            } else {

                                if ($user->status != 0 )
                                    DB::table('user')->where('openid', $message->FromUserName)->update([
                                        'status' => 0,
                                        'update_time' => date("Y-m-d H:i:s")
                                    ]);
                            }
                            $request->session()->put('openid', $message->FromUserName);
                            $request->session()->put('uid', empty($id) ? $user->userid : $id);
                            return '欢迎来到童马儿童生活';
                            break;
                        case 'unsubscribe':
                            DB::table('user')->where('openid', $message->FromUserName)->update([
                                'status' => 1,
                                'unsb_time' => date("Y-m-d H:i:s")
                            ]);
                            break;
                        default:

                            //return '快乐' . date("Y-m-d") . '一天';
                            break;
                    }
                    break;
                case 'text':
                    DB::table('usersendmsg')->insert([
                        'openid' => $message->FromUserName,
                        'content' => $message->Content,
                    ]);
                    if (strpos($message->Content, '萌宝') !== false ||
                        strpos($message->Content, '投票') !== false ||
                        strpos($message->Content, '萌宝投票') !== false ||
                        strpos($message->Content, '最美小萌宝') !== false
                    )
                        return new News([
                            'title'       => 'T100《最美小萌宝》第三季投票',
                            'description' => '汇集了无数萌宝，萌翻观众一片，这里是《最美小萌宝》第三季的投票现场!',
                            'url'         => 'http://mp.weixin.qq.com/s?__biz=MzIxMDgzMzY3MA==&mid=100000136&idx=1&sn=16a35a4bf898cdf05a1b767324f0c572&chksm=175fd9e1202850f7f2d05c8242e6448652eb68c263475a16807949b8f04e783bfddb6f9ecffa#rd',
                            // ...
                        ]);
                    break;
                case 'image':
                    DB::table('usersendmsg')->insert([
                        'openid' => $message->FromUserName,
                        'content' => $message->PicUrl,
                        'type' => 1
                    ]);
                    break;
            }
        });
        $response = $server->serve();
        return $response;
    }

    public function index(Request $request) {
    	if (!$request->session()->has('uid') && !empty($request->input('code'))){
        	$user = $this->app->oauth->user();
        	if ($openid = $user->id) {
        		$request->session()->put('openid', $openid);		
        		$data = DB::table("user")->where('openid', $openid)->select('userid')->first();
        		if (!empty($data) && !empty($data->userid)) {
        			$request->session()->put('uid', $data->userid);
        		} else {
	        		$uid = DB::table("user")->insertGetId([
	        			'openid'	=>  $user->id,
	        			'uname'		=>  $user->name,
	        			'avatar'	=>	$user->avatar,
	        			'sex'		=>  $user->original['sex']
	        		]);
        			if ($uid) 
        				$request->session()->put("uid", $uid);
        		}
        	}
    	}
        $brands = DB::table("brands")->orderBy('sort', 'desc')->where('is_del', 0)->get();
        return view('index', ['brands' => $brands]);
    }

    public function type(Request $request) {
        $brand_id = $request->input('brand_id');
        if (empty($brand_id)) exit;
        $brand = DB::table("brands")->where('id', $brand_id)->first();
        $types = DB::table('goodstype')->where(['is_delete' => 0, 'brand_id' => $brand_id])
            ->orderBy('sort', 'asc')
            ->get();
        $goods = DB::table('goods')->where([
            ['is_delete', '=', 0],
            ['is_hot', '=', 1],
            ['is_sale', '=', 1],
            ['brand_id', '=', $brand_id]
        ])
            ->orderBy('create_time', 'desc')->get();
        $ads = DB::table('goods')->where([
            ['is_delete', '=', 0],
            ['is_ad', '=', 1],
            ['brand_id', '=', $brand_id]
        ])->limit(5)->get();
        return view('type',
            ['types' => $types, 'goods' => $goods, 'brand_id' => $brand_id,
                'ads'=>$ads, 'brand' => $brand, 'count'=>count($ads), 'index-active'=>'active']);
    }

    /**
     * 添加菜单
     */
    public function addmenu(Request $request) {
        $this->app = new Application(config('wx'));
        $menu = $this->app->menu;
        $buttons = [
            [
                "type" => "view",
                "name" => "商城",
                "url"  => "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxd8b6b83c91c44ac3&redirect_uri=http%3A%2F%2Fwww.jingyuxuexiao.com%2F&response_type=code&scope=snsapi_userinfo&state=cd16a43a0a6e5b6d007c942c8850a111#wechat_redirect"
            ],
            /*[
                "type" => "view",
                "name" => "萌宝投票",
                "url"  => "http://mp.weixin.qq.com/s?__biz=MzIxMDgzMzY3MA==&mid=100000136&idx=1&sn=16a35a4bf898cdf05a1b767324f0c572&chksm=175fd9e1202850f7f2d05c8242e6448652eb68c263475a16807949b8f04e783bfddb6f9ecffa#rd"
            ],*/
            [
                "name"      => "会员卡",
                "sub_button" => [
                    [
                    "type" => 'view',
                    'name' => '会员卡',
                    'url' => $this->url . '/card'
                    ],
                    [
                        'type' => 'view',
                        'name' => '我的',
                        'url' => $this->url .'/my'

                    ]
                ]
            ]
        ];
        $menu->add($buttons);
    }
}