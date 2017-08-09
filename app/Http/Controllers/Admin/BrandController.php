<?php
/**
 * Created by PhpStorm.
 * User: guhec
 * Date: 2017/8/8
 * Time: 21:28
 */

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;

class BrandController extends Controller
{

    public function index() {
       $brands = DB::table('brands')->where('is_del', '0')->orderBy('sort')->paginate(10);

       return view('admin.brand.index', ['brands' => $brands]);
    }
}