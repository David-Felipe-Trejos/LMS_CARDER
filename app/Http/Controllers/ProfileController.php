<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash};
use Illuminate\Validation\Rules\Password;
class ProfileController extends Controller {
    public function edit() { return view('profile.edit',['user'=>Auth::user()]); }
    public function update(Request $request) {
        Auth::user()->update($request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email,'.Auth::id(),'cargo'=>'nullable|string|max:100','dependencia'=>'nullable|string|max:150','telefono'=>'nullable|string|max:20']));
        return back()->with('success','✅ Perfil actualizado.');
    }
    public function updatePassword(Request $request) {
        $request->validate(['current_password'=>'required','password'=>['required','confirmed',Password::min(8)]]);
        if (!Hash::check($request->current_password,Auth::user()->password)) return back()->withErrors(['current_password'=>'Contraseña actual incorrecta.']);
        Auth::user()->update(['password'=>Hash::make($request->password)]);
        return back()->with('success','✅ Contraseña actualizada.');
    }
}
