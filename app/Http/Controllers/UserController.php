<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
{
    $search = $request->get('search');

    $query = User::query();

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('lastName', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phoneNumber', 'like', "%{$search}%");
        });
    }

    $users = $query->paginate($request->get('per_page', 16));

    $users->getCollection()->transform(function ($user) {
        if ($user->img) {
            $user->img = asset('storage/' . $user->img);
        }
        return $user;
    });

    return response()->json($users);
}


    public function show($id)
    {
        $user = User::findOrFail($id);

        if ($user->img) {
            $user->img = asset('storage/' . $user->img); 
        }

        return response()->json($user);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'lastName' => 'nullable|string|max:50',
            'email' => 'nullable|email|unique:users,email|max:100',
            'phoneNumber' => 'nullable|string|max:15|unique:users,phoneNumber',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,Customer,Vendor,DeliveryMan',
            'location' => 'nullable|string|max:255',
            'img' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('img')) {
            $path = $request->file('img')->store('users', 'public');
            $validated['img'] = $path;
        } else {
            $validated['img'] = 'users/default_user.png'; 
        }

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        $user->img = asset('storage/' . $user->img);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
        ], 201);
    }

    public function getVendors(Request $request)
{
    $vendors = User::where('role', 'Vendor') 
        ->select('id', 'name', 'email') 
        ->paginate($request->get('per_page', 16));

    return response()->json($vendors);
}


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phoneNumber' => 'nullable|string|max:15|unique:users,phoneNumber,' . $user->id,
            'lastName' => 'required|string|max:255',
            'role' => 'required|in:Admin,Customer,Vendor,DeliveryMan',
            'location' => 'nullable|string|max:255',
            'img' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('img')) {
            if ($user->img) {
                Storage::disk('public')->delete($user->img);
            }

            $path = $request->file('img')->store('users', 'public');
            $validated['img'] = $path;
        }

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        $user->update($validated);

        if ($user->img) {
            $user->img = asset('storage/' . $user->img); 
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->img) {
            Storage::disk('public')->delete($user->img);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
