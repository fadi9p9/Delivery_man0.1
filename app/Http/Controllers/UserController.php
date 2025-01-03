<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    
    public function index(Request $request)
    {
        $users = User::paginate($request->get('per_page', 16));
    
        // تعديل الصور لكل المستخدمين في المجموعة
        $users->getCollection()->transform(function ($user) {
            if (isset($user->img)) { // تحقق من وجود الصورة
                $user->img = $user->img ? asset('storage/' . $user->img) : null;
            }
            return $user;
        });
    
        return response()->json($users);
    }





    public function show($id)
    {
        $user = User::findOrFail($id);
    
        // تعديل رابط الصورة إذا وُجدت
        $user->img = $user->img ? asset('storage/' . $user->img) : null;
    
        return response()->json($user);
    }
       
    






    public function updateuser(Request $request, $id)
{
    $user = User::findOrFail($id);

    $defaultImage = 'default_images/default_user.png'; // المسار الافتراضي للصورة

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'password' => 'nullable|string|min:8',
        'phoneNumber' => 'nullable|string|max:15|unique:users,phoneNumber,' . $user->id,
        'lastName' => 'required|string|max:255',
        'role' => 'required|in:Admin,Customer,Vendor,DeliveryMan',
        'location' => 'nullable|string|max:255',
        'img' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // تأكيد أن img صورة
    ]);

    // تحديث الصورة إذا تم رفع صورة جديدة
    if ($request->hasFile('img')) {
        // حذف الصورة القديمة إذا لم تكن الصورة الافتراضية
        if ($user->img && $user->img !== $defaultImage) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->img);
        }

        // حفظ الصورة الجديدة
        $path = $request->file('img')->store('users', 'public');
        $validated['img'] = $path;
    } elseif ($request->missing('img')) {
        // إذا لم يتم إرسال الحقل img، اجعل الصورة الافتراضية
        $validated['img'] = $defaultImage;
    }

    // تحديث كلمة المرور إذا تم إرسالها
    if (isset($validated['password'])) {
        $validated['password'] = bcrypt($validated['password']);
    }

    $user->update($validated);

    return response()->json([
        'message' => 'User updated successfully',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'img' => $user->img ? asset('storage/' . $user->img) : asset('storage/' . $defaultImage), // إنشاء رابط للصورة
        ],
    ]);
}



    


    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // حذف الصورة إذا وُجدت
        if ($user->img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->img);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
