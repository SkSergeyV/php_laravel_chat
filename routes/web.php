<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Services\LoginController;
use App\Http\Controllers\Services\RegisterController;
use App\Http\Controllers\Admin\Home\HomeController as AdminHomeController;
use App\Http\Controllers\Site\User\Home\HomeController as UserHomeController;
use App\Http\Controllers\Site\User\Chat\ChatController as UserChatController;
use App\Http\Controllers\Site\Home\HomeController as HomeController;
use App\Http\Controllers\Services\ActionController;




// -- Main ---------------------------------
Route::get('/', [HomeController::class, 'index'])->name('site.home');  

Route::get('/home', function () {
    return redirect('/');
});
// -----------------------------------------




// -- Login & Logout --  ------------
Route::group(['middleware' => 'guest'], function () {  
    Route::post('/login', [LoginController::class, 'login'])->name('login');
});
Route::get('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
// ------------------------------------------




// -- ACTIONS--------------------------------------
Route::group(['prefix' => 'app'], function () {
   Route::post('/send_message', [ActionController::class, 'sendMessage'])->name('app.send_message'); 
   Route::post('/upload_file', [ActionController::class, 'uploadFile'])->name('app.upload_file');
   Route::post('/get_attachment', [ActionController::class, 'getAttachment'])->name('app.get_attachment'); 
   Route::post('/set_read_message', [ActionController::class, 'setReadMessage'])->name('app.set_read_message'); 
   Route::post('/send_action', [ActionController::class, 'sendAction'])->name('app.send_action'); 
   Route::post('/set_status', [ActionController::class, 'setStatus'])->name('app.set_status'); 
   Route::post('/set_logout', [ActionController::class, 'setLogout'])->name('app.set_logout'); 
   Route::post('/get_messages', [ActionController::class, 'getMessages'])->name('app.get_messages'); 
   Route::post('/edit_message', [ActionController::class, 'editMessage'])->name('app.edit_message'); 
   Route::post('/delete_message', [ActionController::class, 'deleteMessage'])->name('app.delete_message');
   Route::post('/quote_message', [ActionController::class, 'quoteMessage'])->name('app.quote_message');            
});
//----------------------------------------------



// -- ADMIN--------------------------------------
Route::group(['middleware' => 'admin', 'prefix' => 'admin'], function () {
   Route::get('/', [AdminHomeController::class, 'index'])->name('admin.home');  
});
//----------------------------------------------


   

// -- USER -------------------------------------
Route::group(['middleware' => 'user', 'prefix' => 'user'], function () {
     Route::get('/', [UserHomeController::class, 'index'])->name('user.home');  
     Route::get('/chat', [UserChatController::class, 'index'])->name('user.chat');  
});
// ---------------------------------------------


  
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    $exitCode = Artisan::call('config:cache');
    return 'Cache cleaned!';
});





