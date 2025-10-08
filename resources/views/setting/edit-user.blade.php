@extends('layouts.app')

@section('title', 'Edit User')
@section('page-title', 'Edit User')
@section('breadcrumb-item', 'User')
@section('breadcrumb-active', 'Edit User')

@section('content')
<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Edit User</h2>

    <form id="user-form" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Avatar Upload -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Avatar</label>
            <div class="flex items-center gap-4">
                <img id="avatar-preview"
                     src="{{ $user->avatar ? asset('storage/'.$user->avatar) : asset('img/avatar-dummy.png') }}"
                     alt="Avatar Preview"
                     class="w-16 h-16 rounded-full border border-gray-300 object-cover" />

                <div class="flex flex-col">
                    <input type="file" name="avatar" id="avatar" accept="image/png, image/jpeg, image/jpg, image/webp"
                        class="text-sm file:bg-blue-500 file:hover:bg-blue-700 file:text-white
                               file:px-4 file:py-2 file:border-0 file:cursor-pointer
                               border border-gray-300 rounded shadow-sm" />
                    <small class="text-xs text-gray-500 mt-1">
                        Allowed Format: <strong>JPG, JPEG, PNG, WEBP</strong>. Max size: <strong>2 MB</strong>.
                    </small>
                </div>

                <div class="flex items-center h-full pt-6">
                    <input type="checkbox" value="1"
                        class="form-checkbox text-indigo-600 mr-2">
                    <label class="text-sm text-gray-700">Linked with Employee Database?</label>
                </div>
            </div>
        </div>

        <!-- Full Name -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name" value="{{ $user->name }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
        </div>

        <!-- Username & Email -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <small class="text-red-600">*</small></label>
                <input type="text" name="username" id="username" value="{{ $user->username }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ $user->email }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password"
                       placeholder="Leave blank if not changing"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       placeholder="Leave blank if not changing"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

     <!-- Departments & Roles -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
    <div class="col-span-2">
        <label for="departments" class="block text-sm font-medium text-gray-700 mb-1">Departments <small class="text-red-600">*</small></label>
        <select name="departments[]" id="departments" multiple
                class="select2 w-full border border-gray-300 rounded shadow-sm text-sm" required>
            @foreach($departments as $dept)
                <option value="{{ $dept->id }}" {{ in_array($dept->id, $userDepartments) ? 'selected' : '' }}>
                    {{ $dept->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-span-2">
        <label for="roles" class="block text-sm font-medium text-gray-700 mb-1">Roles <small class="text-red-600">*</small></label>
        <select name="roles[]" id="roles" multiple
                class="select2 w-full border border-gray-300 rounded shadow-sm text-sm" required>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ in_array($role->id, $userRoles) ? 'selected' : '' }}>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<hr>
 <div class="flex justify-start items-center gap-2 mt-4">
        <a href="{{ route('setting.user.index') }}"
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded shadow">
   ← Back
</a>

<button type="submit" id="btn-submit"
   class="w-28 flex items-center justify-center gap-2 px-4 py-2 bg-green-700 hover:bg-green-800 text-white rounded shadow">
   <i data-feather="save" class="h-4 w-4"></i>
   Update
</button>
      </div>
</form>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    const isReadonly = {{ $readonly ? 'true' : 'false' }};

    // Init select2
    $('#departments').select2({ placeholder: '-- Select Departments --', width: '100%' });
    $('#roles').select2({ placeholder: '-- Select Roles --', width: '100%' });

    if (isReadonly) {
        $('#departments').prop('disabled', true);
        $('#roles').prop('disabled', true);
    }

    // Avatar preview
    $('#avatar').on('change', function () {
        const reader = new FileReader();
        reader.onload = e => $('#avatar-preview').attr('src', e.target.result);
        reader.readAsDataURL(this.files[0]);
    });

    // Hapus semua handler lama biar tidak dobel
    $('#user-form').off('submit').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData();
        const $btn = $('#btn-submit');
        $btn.prop('disabled', true).text('Processing...');

        $(this).find('input, select, textarea').each(function () {
            const name = $(this).attr('name');
            const type = $(this).attr('type');

            if (!name || name === 'as_customer') return;

            if (isReadonly && !['password', 'confirm_password', 'avatar', 'departments[]', 'roles[]', '_token', '_method', 'email', 'username', 'name'].includes(name)) {
                return;
            }

            if (type === 'file') {
                if (this.files.length > 0) formData.append(name, this.files[0]);
            } else {
                formData.append(name, $(this).val());
            }
        });

        const departments = $('#departments').val() || [];
        const roles = $('#roles').val() || [];

        departments.forEach(val => formData.append('departments[]', val));
        roles.forEach(val => formData.append('roles[]', val));

        $.ajax({
            url: '{{ route("setting.user.update", $user->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'User updated successfully!',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        if (isReadonly) {
                            window.location.reload();
                        } else {
                            window.location.href = '{{ route("setting.user.index") }}';
                        }
                    });
                } else {
                    $btn.prop('disabled', false).text('Update');
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: res.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (err) {
                $btn.prop('disabled', false).text('Update');
                console.error(err.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Failed',
                    text: 'Failed to update user.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});

</script>
@endpush
@endsection
