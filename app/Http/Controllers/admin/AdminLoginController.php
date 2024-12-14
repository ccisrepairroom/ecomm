<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index()
    {
        return view('admin.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.login')
                ->withErrors($validator)
                ->withInput($request->only('email'));
        }

        if (Auth::guard('admins')->attempt(['email' => $request->email, 'password' => $request->password], $request->get('remember'))) {
            $admin = Auth::guard('admins')->user();

            if ($admin && $admin->role === 1) {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::guard('admins')->logout();
                return redirect()->route('admin.login')->with('error', 'You are not authorized to access the admin panel.');
            }
        } else {
            return redirect()->route('admin.login')->with('error', 'Either Email or Password is incorrect.');
        }
    }
}
