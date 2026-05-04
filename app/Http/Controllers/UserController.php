<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;
class UserController extends Controller {
    public function index(Request $request) {
        $query=User::with('roles');
        if ($request->filled('search')) { $s=$request->search; $query->where(fn($q)=>$q->where('name','like',"%$s%")->orWhere('email','like',"%$s%")); }
        if ($request->filled('role')) $query->role($request->role);
        $users=$query->orderBy('name')->paginate(15)->appends($request->query()); $roles=Role::all();
        return view('users.index', compact('users','roles'));
    }
    public function create() { return view('users.create',['roles'=>Role::all()]); }
    public function store(Request $request) {
        $data=$request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users','password'=>['required','confirmed',Password::min(8)],'role'=>'required|exists:roles,name','cargo'=>'nullable|string|max:100','dependencia'=>'nullable|string|max:150','telefono'=>'nullable|string|max:20']);
        $user=User::create(['name'=>$data['name'],'email'=>$data['email'],'password'=>Hash::make($data['password']),'cargo'=>$data['cargo']??null,'dependencia'=>$data['dependencia']??null,'telefono'=>$data['telefono']??null,'email_verified_at'=>now()]);
        $user->assignRole($data['role']);
        return redirect()->route('users.index')->with('success','✅ Usuario creado.');
    }
    public function show(User $user) { $user->load('roles','enrollments.course','certificates.course','quizAttempts.quiz'); return view('users.show', compact('user')); }
    public function edit(User $user) { return view('users.edit',['user'=>$user,'roles'=>Role::all()]); }
    public function update(Request $request, User $user) {
        $data=$request->validate(['name'=>'required|string|max:100','email'=>'required|email|unique:users,email,'.$user->id,'cargo'=>'nullable|string|max:100','dependencia'=>'nullable|string|max:150','telefono'=>'nullable|string|max:20','role'=>'required|exists:roles,name','active'=>'boolean']);
        $user->update(['name'=>$data['name'],'email'=>$data['email'],'cargo'=>$data['cargo']??null,'dependencia'=>$data['dependencia']??null,'telefono'=>$data['telefono']??null,'active'=>$request->boolean('active')]);
        $user->syncRoles([$data['role']]);
        return redirect()->route('users.index')->with('success','✅ Usuario actualizado.');
    }
    public function destroy(User $user) {
        if ($user->id===auth()->id()) return back()->with('error','No puedes eliminar tu propia cuenta.');
        $user->delete(); return redirect()->route('users.index')->with('success','🗑️ Usuario eliminado.');
    }
}
