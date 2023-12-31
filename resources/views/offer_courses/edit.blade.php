@section('pageTitle', 'Edit Course Semester')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course Semester') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('offer_courses.edit', $course) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('offer_courses.update',$course->id) }}">
                    @csrf
                    @method('patch')

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Course Name</span>
                        </label>
                        <input type="text" name="course_name" placeholder="Course Name"
                               class="input input-bordered w-full max-w-xs" value="{{ $course->name }}" readonly/>
                    
                        <x-input-error :messages="$errors->get('course_name')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Current Semester</span>
                        </label>
                        <input type="text" name="current_semester" placeholder="Current Semester"
                               class="input input-bordered w-full max-w-xs" value="{{ $course->semester }}" readonly/>
                        <x-input-error :messages="$errors->get('current_semester')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">New Semester</span>
                        </label>
                        <select name="new_semester" class="select select-bordered w-full max-w-xs">
                            <option value="1" {{ $course->semester == 1 ? 'selected' : '' }}>1</option>
                            <option value="2" {{ $course->semester == 2 ? 'selected' : '' }}>2</option>
                            <option value="3" {{ $course->semester == 3 ? 'selected' : '' }}>3</option>
                            <option value="4" {{ $course->semester == 4 ? 'selected' : '' }}>4</option>
                            <option value="5" {{ $course->semester == 5 ? 'selected' : '' }}>5</option>
                            <option value="6" {{ $course->semester == 6 ? 'selected' : '' }}>6</option>
                            <option value="7" {{ $course->semester == 7 ? 'selected' : '' }}>7</option>
                            <option value="8" {{ $course->semester == 8 ? 'selected' : '' }}>8</option>
                        </select>
                        <x-input-error :messages="$errors->get('new_semester')" class="mt-2"/>
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Update Semester
                        </button>
                        <a href="{{ route('offer_courses.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
