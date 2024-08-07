<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\barangModel;
use App\Models\jenisBarangModel;
use App\Models\merkModel;
use App\Models\qrCodeModel;
use App\Models\satuanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }


    public function registration()
    {
        return view('auth.registration');
    }


    public function postLogin(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only(
            'email',
            'password'
        );
        if (Auth::attempt($credentials)) {
            return redirect()->intended('dashboard')
                ->withSuccess('You have Successfully loggedin');
        }

        return redirect("login")->withSuccess('Oppes!You have entered invalid credentials');
    }


    public function postRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        return redirect("dashboard")->withSuccess('Great! You have Successfully loggedin');
    }


    public function dashboard()
    {
        if (Auth::check()) {
            $totalkategori = jenisBarangModel::count();
            $totalmerk = merkModel::count();
            $totalbarang = barangModel::count();
            $totalqrcode = qrCodeModel::count();
            $barangs = barangModel::all();
            return view('dashboard.index', compact('barangs', 'totalbarang', 'totalmerk', 'totalkategori', 'totalqrcode'));
        }

        return redirect("login")->withSuccess('Opps!You do not have access');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }


    public function logout()
    {
        Session::flush();
        Auth::logout();

        return Redirect('login');
    }
}
