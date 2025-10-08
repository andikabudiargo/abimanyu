<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index() {
        return view('setting.user');
    }

     public function select()
{
    $users = User::select('id', 'name')
        ->get();

    return response()->json($users);
}

    public function selectProduction()
{
    $users = User::select('id', 'name')
        ->whereHas('departments', function($q) {
            $q->where('name', 'Produksi'); // sesuaikan dengan kolom department
        })
        ->get();

    return response()->json($users);
}


    public function create() {
         $departments = Department::all();
         $roles = Role::all();

        return view('setting.create-user', compact('departments', 'roles'));
    }
    
     public function data(Request $request)
{
    $user = User::with(['departments', 'roles'])->select([
        'id', 'avatar', 'name', 'username', 'email', 'status', 'last_login', 'last_ip', 'created_at', 'updated_at'
    ]);

    return datatables()->of($user)
    ->addColumn('departments', function ($row) {
    return $row->departments->pluck('name')->implode(', ');
})

->addColumn('roles', function ($row) {
    return $row->roles->pluck('name')->implode(', ');
})
->addColumn('user_info', function ($row) {
    $avatar = $row->avatar 
        ? asset('storage/' . $row->avatar)
        : asset('img/avatar-dummy.png');

    return '
    <div class="flex items-center space-x-3">
        <img src="' . $avatar . '" alt="Avatar" class="w-10 h-10 rounded-full object-cover border border-gray-300">
        <div class="text-sm leading-5">
            <div class="font-semibold text-gray-800">' . e($row->name) . '</div>
            <div class="text-gray-500">' . e($row->username) . '</div>
        </div>
    </div>';
})
->editColumn('status', function ($row) {
    $checked = $row->status ? 'checked' : '';
    $statusText = $row->status ? 'Active' : 'Inactive';
    $textClass = $row->status ? 'text-green-600' : 'text-red-500';

    return '
        <div class="flex items-center space-x-2">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" class="sr-only peer status-toggle" data-id="'.$row->id.'" '.$checked.'>
                <div class="w-14 h-7 bg-gray-300 rounded-full peer peer-checked:bg-green-500 transition duration-300 ease-in-out"></div>
                <div class="absolute left-1 top-1 w-5 h-5 bg-white rounded-full shadow transform peer-checked:translate-x-7 transition duration-300 ease-in-out"></div>
            </label>
            <span class="status-label '.$textClass.' text-sm font-medium">'.$statusText.'</span>
        </div>
    ';
})



        ->editColumn('created_at', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d-m-Y H:i');
        })
         ->editColumn('updated_at', function ($row) {
            return \Carbon\Carbon::parse($row->updated_at)->format('d-m-Y H:i');
        })
        ->addColumn('action', function ($row) {
    $id = $row->id;
    $dropdownId = 'dropdown-' . $row->id;
    $edit_url = route('setting.user.edit', ['id' => $row->id]); // ✅ Diganti $ticket jadi $row


    return '
    <div class="relative inline-block text-left">
      <button type="button" onclick="toggleDropdown(\'' . $dropdownId . '\')" class="inline-flex justify-center w-full text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none">
       <i data-feather="align-justify"></i>
      </button>
      <div id="' . $dropdownId . '" class="hidden origin-top-right absolute right-100 mt-2 w-28 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
        <div class="py-1 text-sm text-gray-700">
          <a href="'. $edit_url .'" class="block px-4 py-2 hover:bg-gray-100"><i data-feather="edit" class="w-4 h-4 inline mr-2"></i></i>Edit</a>
          <form action="" method="POST" onsubmit="return confirm(\'Yakin ingin hapus?\')" class="block">
            ' . csrf_field() . method_field('DELETE') . '
            <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-500 hover:text-gray-100"><i data-feather="trash-2" class="w-4 h-4 inline mr-2"></i>Delete</button>
          </form>
        </div>
      </div>
    </div>';
})
        ->rawColumns(['action','user_info','status'])
        ->make(true);
}

   public function store(Request $request)
{
    $validated = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'username' => 'required|string|unique:users',
        'email' => 'nullable|email|unique:users',
        'password' => 'required|min:6|same:confirm_password',
        'avatar' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        'departments' => 'required|array',
        'roles' => 'required|array',
    ]);

    if ($validated->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validated->errors()->first()
        ], 422);
    }

    $data = $validated->validated();
    $data['password'] = Hash::make($data['password']);

    // Upload avatar jika ada
   if ($request->hasFile('avatar')) {
    $file = $request->file('avatar');
    Log::info('Avatar filename:', ['name' => $file->getClientOriginalName()]);
    $path = $file->store('avatars', 'public');
    $data['avatar'] = $path;
} else {
    Log::warning('Avatar tidak diterima oleh server');
}

    $user = User::create($data);

    // Simpan relasi pivot
    $user->departments()->sync($request->departments);
    $user->roles()->sync($request->roles);

    return response()->json([
        'success' => true,
        'message' => 'User berhasil disimpan.',
        'data' => $user
    ]);
}

public function toggleStatus(Request $request)
{
    $user = User::findOrFail($request->id);
    $user->status = !$user->status;
    $user->save();

    return response()->json([
        'success' => true,
        'status' => $user->status
    ]);
}

  public function edit($id)
    {
        $user = User::findOrFail($id);
        $departments = Department::all();
        $roles = Role::all(); // jika pakai spatie/permission

        $userDepartments = $user->departments()->pluck('departments.id')->toArray();
        $userRoles = $user->roles()->pluck('roles.id')->toArray();

        // Cek apakah user membuka halaman sendiri
    $readonly = auth()->id() == $user->id;

        return view('setting.edit-user', compact(
            'user', 'departments', 'roles', 'userDepartments', 'userRoles', 'readonly'
        ));
    }

      public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi
        $rules = [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,'.$user->id,
            'email' => 'nullable|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|confirmed|min:6',
            'departments' => 'nullable|array|min:1',
            'roles' => 'nullable|array|min:1',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];

        $validated = $request->validate($rules);

        // Update field dasar
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'] ?? null;

        // Password jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // Avatar
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama
            if ($user->avatar && Storage::exists($user->avatar)) {
                Storage::delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        // Sync departments & roles
        $user->departments()->sync($validated['departments']);
        $user->roles()->sync($validated['roles']); // gunakan 'role', bukan 'roles'

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }
}
