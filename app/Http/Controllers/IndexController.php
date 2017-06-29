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

class IndexController extends Controller {
    private $app;
    private $url = "http://www.jingyuxuexiao.com";      // 菜单请求地址
    public function __construct() {
        $this->app = new Application(config('wx'));
    }
    public function wx(Request $request) {
        Log::info('jfklasdjfdaslfdsa');
        $server = $this->app->server;
        $server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    switch ($message->Event) {
                        case 'subscribe':
                            Log::info($$message->FromUserName);
                            break;
                        case 'unsubscribe':
                            Log::info('取消');
                            break;
                    }
                    break;
                case 'text': return '收到文字信息'; break;
            }
            return '您好，欢迎关注温江童马儿童高端服务';
        });
        $response = $server->serve();
        return $response;
    }

    public function index(Request $request) {
    	if (!$request->session()->has('uid')){
        	$user = $this->app->oauth->user();
        	if ($user->id) {
        		$openid = $user->id;
        		$name = $user->name;
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
        //@override 获取微信账号
        $types = DB::table('goodstype')->where('is_delete', 0)
                            ->get();
        $goods = DB::table('goods')->where([
            ['is_delete', '=', 0],
            ['is_hot', '=', 1],
            ['is_sale', '=', 1]
        ])
            ->orderBy('create_time', 'desc')->get();
        $ads = DB::table('goods')->where([
            ['is_delete', '=', 0],
            ['is_ad', '=', 1]
        ])->limit(5)->get();
        return view('index',
                    ['types' => $types, 'goods' => $goods,
                              'ads'=>$ads, 'count'=>count($ads), 'index-active'=>'active']);
    }

    /**
     * 获取个人流水
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function money(Request $request) {
        $page = $request->has('page') ? $request->input('page') : 0;
        $pagesize = 10;
        $pagenow = $page * $pagesize;
        $money = DB::table('usertransmoney')->where('uid', $request->session()->get('uid'))
                                            ->orderBy('create_time', 'desc')
                                            ->paginate($pagesize);
        return $request->has('page') ? response()->json(['money' => $money]) : view('money', ['money' => $money]);
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
                "url"  => $this->url . "/"
            ],
            [
                "type"      => "view",
                "name"      => "会员卡",
                "url"       => $this->url . "/card"
            ],
            [
                "type"      => "view",
                "name"      => "我的",
                "url"       => $this->url . "/my"
            ],
        ];
        $menu->add($buttons);
    }

}