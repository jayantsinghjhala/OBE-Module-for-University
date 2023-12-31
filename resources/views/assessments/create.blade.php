@section('pageTitle', 'Create New Assessment')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-start gap-4">
            <x-back-link />
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Assessment') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('assessments.create') }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('assessments.store') }}">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Assessment Type</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="assessment_type" id="assessment_type">
                                <option disabled selected>Select Assessment Type</option>    
                                <option value="CIA" {{ old('assessment_type') == 'CIA' ? 'selected' : '' }}>CIA</option>
                                <option value="ETA" {{ old('assessment_type') == 'ETA' ? 'selected' : '' }}>ETA</option>
                            </select>
                            <x-input-error :messages="$errors->get('assessment_type')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Assessment Name</span>
                            </label>
                            <input type="text" class="input text-neutral input-bordered bg-white w-full max-w-xl" name="assessment_name" placeholder="Enter Assessment Name" value="{{ old('assessment_name') }}" />
                            <x-input-error :messages="$errors->get('assessment_name')" class="mt-2" />
                        </div>
                        
                        <!-- Add more fields as needed -->
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <x-back-link>{{ __('Cancel') }}</x-back-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
