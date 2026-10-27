<?php

// Пользователь, главная

namespace App\Http\Controllers\Site\User\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class HomeController extends Controller
{
    public function index(){
        $user=array();
        return view('site.user.home.index', compact('user'));
    }
}