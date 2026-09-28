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
        return view('auth.login'); 
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Gunakan guard 'admin'
        if (Auth::guard('admin')->attempt($credentials)) {
            $role = Auth::guard('admin')->user()->role;
            
            // Validasi ketat: Hanya admin/cashier yang boleh masuk lewat guard ini
            if (in_array($role, ['admin', 'cashier'])) {
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            }
            
            // Jika customer nyasar login di halaman admin, tendang keluar
            Auth::guard('admin')->logout();
            return back()->withErrors(['email' => 'Akses ditolak. Anda bukan staff.'])->onlyInput('email');
        }

        return back()->withErrors(['email' => 'Email atau password salah!'])->onlyInput('email');
    }

    // --- CUSTOMER AUTH ---
    public function showCustomerLogin()
    {
        return view('customer.auth'); 
    }

    public function customerLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Gunakan guard 'web' (default) untuk customer
        if (Auth::guard('web')->attempt($credentials)) {
            $role = Auth::guard('web')->user()->role;
            
            // Validasi ketat: Hanya customer yang boleh masuk lewat guard ini
            if ($role === 'customer') {
                $request->session()->regenerate();
                return redirect()->route('customer.account')->with('success', 'Berhasil masuk!');
            }
            
            // Jika admin nyasar login di halaman customer frontend, tendang keluar
            Auth::guard('web')->logout();
            return back()->withErrors(['login_email' => 'Akun staff tidak bisa digunakan di sini. Silakan login di portal ERP.'])->onlyInput('email');
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

            // Login spesifik ke guard web
            Auth::guard('web')->login($user);

            return redirect()->route('customer.menu')->with('success', 'Akun berhasil dibuat! Diskon 50% pesanan pertama Anda sudah aktif.');
        } catch (\Exception $e) {
            return back()->with('error_msg', 'Gagal mendaftar: ' . $e->getMessage())->with('active_tab', 'register');
        }
    }

    public function logout(Request $request)
    {
        // Logout dari kedua guard untuk memastikan keamanan bersih
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}