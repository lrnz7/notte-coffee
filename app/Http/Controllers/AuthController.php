<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    // --- STAFF / ERP AUTH ---
    public function showLogin()
    {
        return view('auth.login'); // Halaman login ERP Admin bawaan
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $role = Auth::user()->role;
            if ($role === 'admin' || $role === 'cashier') {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->route('home');
        }

        return back()->withErrors(['email' => 'Email atau password salah!'])->onlyInput('email');
    }

    // --- CUSTOMER AUTH ---
    public function showCustomerLogin()
    {
        return view('customer.auth'); // Halaman Login & Register khusus Customer
    }

    public function customerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('customer.account')->with('success', 'Berhasil masuk!');
        }

        return back()->withErrors(['login_email' => 'Email atau password salah!'])->onlyInput('email');
    }

    public function customerRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.unique' => 'Email ini sudah terdaftar!',
            'password.confirmed' => 'Konfirmasi password tidak cocok!',
            'password.min' => 'Password minimal 6 karakter!'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('active_tab', 'register');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'has_claimed_welcome_discount' => false,
            ]);

            Auth::login($user);

            return redirect()->route('customer.menu')->with('success', 'Akun berhasil dibuat! Diskon 50% pesanan pertama Anda sudah aktif.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Gagal mendaftar: ' . $e->getMessage())->with('active_tab', 'register');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}