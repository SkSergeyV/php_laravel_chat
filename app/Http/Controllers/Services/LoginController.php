<?php

// Авторизация

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
     

    public function login(Request $request)
    {              
                
        $validator = Validator::make($request->all(), [
            'login' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
             return response()->json([
                'type' => '',
                'message' => 'ERROR',
                'url' => ''
                ], 200);
        }
        
                         
        if (Auth::attempt([
            'email' => trim($request->login),
            'password' => trim($request->password),
        ])) {
                     
           if (Auth::check() && Auth::user()->is_admin) {                 
              return response()->json([
                'type' => 'Admin',
                'message' => 'OK',
                'url' => ''
                ], 200);
            } else {                                  
              return response()->json([
                'type' => 'User',
                'message' => 'OK',
                'url' => ''
                ], 200);
            }
            
            } else {
                return response()->json([
                'type' => '',
                'message' => 'ERROR',
                'url' => ''
                ], 200);
             } 
                                                         
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('site.home');
    }


}