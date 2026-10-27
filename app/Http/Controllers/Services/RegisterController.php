<?php

// Регистрация 

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\UserProfile;


class RegisterController extends Controller
{
    public function registerForm()
    {
        return view('services.register');
    }

    public function register(Request $request)
    {
       
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);


        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'remember_token' => Str::uuid(),
            'current_token' => md5(date('d.m.Y H:i:s').$request->name)
        ]);
        
        
    
           
    }




}