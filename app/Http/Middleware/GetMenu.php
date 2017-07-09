<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/7/9
 * Time: 23:11
 */
namespace App\Http\Middleware;

use Closure;
use DB;

class GetMenu
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $sysuid = session('sysuid');
        $user = DB::table('adminuser')->where('admin_id', $sysuid)->first();
        if ($sysuid == 1) {
            $data = DB::table('auth')->where('is_show', 1)->orderBy('create_time')->get();
        } else {
            $sql = "select * from auth where is_show=1 and auth_id in ({$user->auth_ids})";
            $data = DB::select($sql);
            //$data = DB::table('auth')->where('is_show', 1)->orderBy('create_time')->get();
        }
        $auths = $this->getAuths($data);
        view()->share('menu_auths', $auths);
        return $next($request);
    }


    private function getAuths($data, $pid = 0, $pos = 0) {
        static $auths;
        foreach ($data as $item) {
            if ($item->auth_pid == $pid) {
                $item->pos = $pos;
                $auths[] = $item;
                $this->getAuths($data, $item->auth_id, $pos + 1 );
            }
        }
        return $auths;
    }
}

