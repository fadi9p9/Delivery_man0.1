<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::paginate($request->get('per_page', 16));

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
