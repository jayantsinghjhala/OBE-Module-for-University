@section('pageTitle', 'Edit Course Faculty')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course Faculty') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('assign_courses.edit', $course) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ $isPrimary ? route('assign_courses.update_primary', $course->id) : route('assign_courses.update_secondary', $course->id) }}">
                    @csrf
                    @method('patch')
                    <input type="hidden" name="current_teacher_id" id="" value="{{ $current_teacher->id }}">
                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Course Name</span>
                        </label>
                        <input type="text" name="course_name" placeholder="Course Name"
                               class="input input-bordered w-full max-w-xs" value="{{ $course->name }}" readonly/>
                        <x-input-error :messages="$errors->get('course_name')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Current Faculty</span>
                        </label>
                        <input type="text" name="current_faculty" placeholder="Current Faculty"
                               class="input input-bordered w-full max-w-xs" value="{{ ucwords($current_teacher->name) }}" readonly/>
                        <x-input-error :messages="$errors->get('current_faculty')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">New Faculty</span>
                        </label>
                        <select name="new_teacher_id" class="select select-bordered w-full max-w-xs">
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ $current_teacher->id == $teacher->id ? 'selected' : '' }}>
                                    {{ ucwords($teacher->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('new_teacher_id')" class="mt-2" />
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Update Faculty
                        </button>
                        <a href="{{ route('assign_courses.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
