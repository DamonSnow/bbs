<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class NotificationsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        //获取登陆用户的所有通知
        $user = Auth::user();
        $notifications = $user->notifications()->paginate(20);
        $user->markAsRead();
        return view('notifications.index',compact('notifications'));
    }
}
