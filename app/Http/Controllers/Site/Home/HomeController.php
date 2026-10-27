<?php

// Сайт, главная

namespace App\Http\Controllers\Site\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function index(){      
      if (Auth::check()) {
          if (Auth::user()->is_admin) {
               return redirect()->route('admin.home');
          } else {
              return redirect()->route('user.chat');
          }                   
      } else {
         $data=array();
         return view('site.home.index', compact('data'));
      }      
    }
    
    
}