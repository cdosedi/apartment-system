<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $admins = User::admins()->withTrashed()->orderBy('created_at', 'desc')->get();

        return view('admins.index', compact('admins'));
    }

    public function create(): View
    {
        return view('admins.create');
    }

    public function store(StoreAdminRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);
        $data['is_admin'] = true;

        User::create($data);

        return redirect()->route('admins.index')
            ->with('success', 'Admin created successfully.');
    }

    public function edit(User $admin): View
    {
        return view('admins.edit', compact('admin'));
    }

    public function update(UpdateAdminRequest $request, User $admin): RedirectResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $admin->update($data);

        return redirect()->route('admins.index')
            ->with('success', 'Admin updated successfully.');
    }

    public function destroy(User $admin): RedirectResponse
    {
        $admin->delete();

        return redirect()->route('admins.index')
            ->with('success', 'Admin deactivated.');
    }

    public function restore($id): RedirectResponse
    {
        $admin = User::withTrashed()->findOrFail($id);
        $admin->restore();

        return back()->with('success', 'Admin restored.');
    }
}
