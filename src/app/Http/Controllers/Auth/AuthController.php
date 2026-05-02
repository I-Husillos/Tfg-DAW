<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use App\Services\DefaultCategoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // Muestra la página de login / landing.
    public function showLogin()
    {
        return view('auth.login');
    }

    // Procesa el intento de login.
    // Permite iniciar sesión con email o username.
    // SRP: la validación la gestiona LoginRequest,
    // la autenticación Auth::attempt, el controlador
    // solo orquesta.
    public function login(LoginRequest $request)
    {
        $input = $request->validated();

        // Detectamos si el campo contiene un @ para
        // saber si es email o username y construimos
        // las credenciales correctas para Auth::attempt.
        $field       = str_contains($input['email'], '@') ? 'email' : 'username';
        $credentials = [
            $field     => $input['email'],
            'password' => $input['password'],
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withErrors(['email' => __('app.invalid_credentials')])
            ->onlyInput('email');
    }

    // Muestra el formulario de registro.
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        // DB::transaction garantiza que ambas
        // inserciones (users y profiles) son atómicas.
        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'username' => $data['username'],
                'email'    => $data['email'],
                'password' => $data['password'], // el modelo lo hashea via cast
            ]);

            // El profile se crea con valores por defecto.
            // El usuario los rellenará desde /profile.
            Profile::create([
                'user_id'  => $user->id,
                'currency' => 'EUR',
                'language' => 'es',
                'timezone' => 'Europe/Madrid',
            ]);

            app(DefaultCategoryService::class)->createFor($user);

            return $user;
        });

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    // Cierra la sesión del usuario.
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}