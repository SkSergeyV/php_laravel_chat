<?php

// Админ, главная

namespace App\Http\Controllers\Admin\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class HomeController extends Controller
{
    public function index(){
        $users=array();
        return view('admin.home.index', compact('users'));
    }
}