<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Department;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', ['users' => User::with('department')->when(request('q'), fn ($q, $s) => $q->where(fn ($x) => $x->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))->when(request('role'), fn ($q, $v) => $q->where('role', $v))->paginate(15)->withQueryString()]);
    }

    public function create()
    {
        return view('users.form', ['managedUser' => new User, 'departments' => Department::orderBy('name')->get()]);
    }

    public function store(UserRequest $r)
    {
        User::create($r->validated());

        return to_route('users.index')->with('success', 'Pengguna ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.form', ['managedUser' => $user, 'departments' => Department::orderBy('name')->get()]);
    }

    public function update(UserRequest $r, User $user)
    {
        $data = $r->validated();
        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }$user->update($data);

        return to_route('users.index')->with('success', 'Pengguna diperbarui.');
    }

    public function destroy(User $user)
    {
        abort_if($user->is(request()->user()), 422, 'Tidak dapat menghapus akun sendiri.');
        $user->delete();

        return back()->with('success','Pengguna dinonaktifkan dan dihapus secara lunak.');
    }
}
