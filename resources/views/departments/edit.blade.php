@section('pageTitle', 'Edit Department')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Department') . ": $department->name" }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{ Breadcrumbs::render('departments.edit',$department) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('departments.update', $department) }}">
                    @csrf
                    @method('patch')

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Department Name</span>
                        </label>
                        <input type="text" name="name" placeholder="Department Name"
                               class="input input-bordered w-full max-w-xs" value="{{ old('name', $department->name) }}"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">School</span>
                        </label>
                        <select name="school_id" class="select select-bordered w-full max-w-xs">
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ $department->school_id == $school->id ? 'selected' : '' }}>
                                    {{ ucfirst($school->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('school_id')" class="mt-2"/>
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <a href="{{ route('departments.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
