@section('pageTitle', 'Edit Course Outcome')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Course Outcome') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('course_outcomes.edit', $course,$course_outcome) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                <form method="POST" action="{{ route('course_outcomes.update',[$course, $course_outcome]) }}">
                    @csrf
                    @method('put')
                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Current Course</span>
                        </label>
                        <input type="text" name="current_course" placeholder="Current Faculty"
                               class="input input-bordered w-full max-w-xs" value="{{ ucwords($course->name) }}" readonly/>
                        <x-input-error :messages="$errors->get('current_course')" class="mt-2" />
                    </div>
                    <input type="hidden" name="course_id" value="{{$course->id}}">
                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Course Outcome Name</span>
                        </label>
                        <select name="name" class="select select-bordered w-full max-w-xs">
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="CO{{ $i }}" {{ (old('name', $course_outcome->name) === "CO$i") ? 'selected' : '' }}>CO{{ $i }}</option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Outcome Description</span>
                        </label>
                        <textarea name="outcome_description" placeholder="Enter Outcome Description" class="textarea textarea-bordered w-full max-w-xs">{{ old('outcome_description', $course_outcome->outcome_description) }}</textarea>
                        <x-input-error :messages="$errors->get('outcome_description')" class="mt-2" />
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <a href="{{ route('course_outcomes.add_outcomes',$course) }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
