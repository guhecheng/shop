<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/7/2
 * Time: 14:10
 */
namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;

class AuthController extends Controller {
    public function index() {
        $auths = DB::table('auth')->get();
        return view('admin.auth.index', ['auths' => $auths]);
    }

    public function add() {

    }
}