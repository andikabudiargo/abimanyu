@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create User')
@section('breadcrumb-item', 'User')
@section('breadcrumb-active', 'Create User')

@section('content')
<div class="w-full bg-white shadow-md rounded-xl p-4 space-y-4 mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Create New User</h2>

    <form id="user-form" enctype="multipart/form-data">
        @csrf
<!-- Avatar Upload -->
<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Avatar</label>
    <div class="flex items-center gap-4">
        <!-- Avatar Preview -->
        <img id="avatar-preview"
             src="{{ asset('img/avatar-dummy.png') }}"
             alt="Avatar Preview"
             class="w-16 h-16 rounded-full border border-gray-300 object-cover" />

        <!-- Input + Keterangan -->
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
        <input type="checkbox" name="as_customer" value="0" class="form-checkbox text-indigo-600 mr-2">
        <label for="as_customer" class="text-sm text-gray-700">Linked with Employee?</label>
        </div>
    </div>
</div>




        <!-- Full Name -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name <small class="text-red-600">*</small></label>
                <input type="text" name="name" id="name"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
        </div>

        <!-- Username & Email -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username <small class="text-red-600">*</small></label>
                <input type="text" name="username" id="username"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
            </div>
        </div>

        <!-- Password -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password <small class="text-red-600">*</small></label>
                <input type="password" name="password" id="password"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password <small class="text-red-600">*</small></label>
                <input type="password" name="confirm_password" id="confirm_password"
                       class="w-full px-3 py-2 border border-gray-300 rounded shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                       required />
            </div>
        </div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <!-- Department -->
<div class="col-span-2">
    <label for="departments" class="block text-sm font-medium text-gray-700 mb-1">Departments <small class="text-red-600">*</small></label>
    <select name="departments[]" id="departments" multiple
        class="select2 w-full border border-gray-300 rounded shadow-sm text-sm" required>
         @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
    </select>
</div>

<!-- Roles -->
<div class="col-span-2">
    <label for="roles" class="block text-sm font-medium text-gray-700 mb-1">Roles <small class="text-red-600">*</small></label>
    <select name="roles[]" id="roles" multiple
        class="select2 w-full border border-gray-300 rounded shadow-sm text-sm" required>
         @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
    </select>
</div>
</div>


        <!-- Actions -->
        <div class="flex justify-start items-center gap-2 mt-4">
            <button type="reset" class="bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-600">Reset</button>
            <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded shadow hover:bg-green-700">Save</button>
        </div>
    </form>
</div>


@push('scripts')
<script>
    document.getElementById('avatar').addEventListener('change', function (e) {
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(this.files[0]);
    });

     $(document).ready(function() {
        $('#departments').select2({
            placeholder: '-- Select Departments --',
            width: '100%'
        });
        $('#roles').select2({
            placeholder: '-- Select Roles --',
            width: '100%'
        });
    });

   $('#user-form').off('submit').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

   $.ajax({
    url: '{{ route("setting.user.store") }}',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
        if (res.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'User succesfully saved!',
                showConfirmButton: false,
                timer: 2000
             }).then(() => {
                location.reload(); // ✅ Reload setelah OK ditekan
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Failed',
                text: res.message,
                confirmButtonText: 'OK'
            });
        }
    },
    error: function (err) {
        console.error(err.responseText);
        Swal.fire({
            icon: 'error',
            title: 'Failed',
            text: 'Failed to save user.',
            confirmButtonText: 'OK'
        });
    }
});

});


</script>
@endpush
@endsection