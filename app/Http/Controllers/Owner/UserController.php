<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\UserCreateRequest;
use App\Http\Requests\Owner\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')->latest();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role_id') && $request->role_id) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->has('is_banned')) {
            $query->where('is_banned', $request->is_banned === 'true');
        }

        $users = $query->paginate($request->get('per_page', 15))->withQueryString();
        $roles = Role::all();

        return Inertia::render('owner/users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role_id', 'is_banned']),
        ]);
    }

    public function create()
    {
        $roles = Role::all();

        return Inertia::render('owner/users/Create', [
            'roles' => $roles,
        ]);
    }

    public function store(UserCreateRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('owner.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load(['role', 'sessions' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return Inertia::render('owner/users/Show', [
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::all();

        return Inertia::render('owner/users/Edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('owner.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('owner.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function ban(User $user)
    {
        $user->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => request('reason'),
        ]);

        return redirect()->back()->with('success', 'User banned successfully.');
    }

    public function unban(User $user)
    {
        $user->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
        ]);

        return redirect()->back()->with('success', 'User unbanned successfully.');
    }

    public function resetPassword(User $user)
    {
        $newPassword = request('password');

        if (! $newPassword) {
            $newPassword = \Illuminate\Support\Str::random(12);
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully.');
    }
}
