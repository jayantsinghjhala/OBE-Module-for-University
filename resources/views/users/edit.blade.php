@section('pageTitle', 'Edit User')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User').": $user->name ($user->email)" }}
        </h2>
    </x-slot>

    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('users.edit',$user) }}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.update', $user) }}">
                        @csrf
                        @method('patch')
                        <div class="form-control w-full max-w-xs p-3">
                            <label class="label">
                                <span class="label-text">Fullname</span>
                            </label>
                            <input type="text" name="name" placeholder="Fullname of the user" class="input input-bordered w-full max-w-xs" value="{{ old('name', $user->name) }}"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xs p-3">
                            <label class="label">
                                <span class="label-text">Email</span>
                            </label>
                            <input type="text" name="email" placeholder="User's email" class="input input-bordered w-full max-w-xs" value="{{ old('email', $user->email) }}"/>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xs p-3">
                            <label class="label">
                                <span class="label-text">Role</span>
                            </label>
                            <select name="role" class="select select-bordered w-full max-w-xs">
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="teacher" {{ old('role', $user->role) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                <!-- <option value="student" {{ old('role', $user->role) == 'student' ? 'selected' : '' }}>Student</option> -->
                            </select>

                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="mt-4 p-4 space-x-2">
                            <button type="submit" class="btn btn-sm px-7">
                                Save
                            </button>
                            <a href="{{ route('users.index') }}">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
