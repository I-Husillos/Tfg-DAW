<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Muestra la vista de perfil con los datos
    // del usuario y su profile asociado.
    // Se carga el profile con with() para evitar
    // una query adicional en la vista.
    public function edit()
    {
        $user    = Auth::user()->load('profile');
        $profile = $user->profile;

        // Lista de zonas horarias para el selector.
        $timezones = \DateTimeZone::listIdentifiers();

        return view('profile.edit', compact('user', 'profile', 'timezones'));
    }

    // Actualiza los datos del perfil personal.
    // Solo toca la tabla profiles, no users.
    // SRP: este método tiene una única responsabilidad.
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        // updateOrCreate por si el profile no existe
        // aún (caso de usuarios creados antes de
        // implementar profiles).
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $request->validated()
        );

        return redirect()
            ->route('profile.edit')
            ->with('success', __('app.profile_updated'));
    }

    // Actualiza la contraseña del usuario.
    // Solo toca la tabla users, no profiles.
    // La validación de current_password la hace
    // Laravel automáticamente en el FormRequest.
    public function updatePassword(UpdatePasswordRequest $request)
    {
        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('profile.edit')
            ->with('success', __('app.password_updated'));
    }
}