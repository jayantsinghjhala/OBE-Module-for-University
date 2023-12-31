@section('pageTitle', "Programs")
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Programs') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{ Breadcrumbs::render('programs.create') }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('programs.store') }}">
                    @csrf

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">School</span>
                        </label>
                        <select name="school_id" placeholder="School Name" class="select select-bordered w-full max-w-xs">
                        <option value="" selected disabled class="initial-option">Select School</option>    
                        @foreach ($schools as $school)
                                <option value="{{ $school->id }}">{{ ucfirst($school->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Department</span>
                        </label>
                        <select name="department_id" placeholder="Department Name" class="select select-bordered w-full max-w-xs">
                            <!-- Departments will be loaded dynamically based on the selected school -->
                            <option value="" selected disabled>Select Department</option>
                        </select>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Program Name</span>
                        </label>
                        <input type="text" name="name" placeholder="Program Name"
                               class="input input-bordered w-full max-w-xs" value="{{ old('name') }}"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <a href="{{ route('programs.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            const schoolSelect = $('select[name="school_id"]');
            const departmentSelect = $('select[name="department_id"]');
            const initialOption = schoolSelect.find('.initial-option');
            // Disable department select initially
            departmentSelect.prop('disabled', true);
            schoolSelect.on('change', function () {
                // Disable department select initially
                departmentSelect.prop('disabled', true);
                const selectedSchoolId = schoolSelect.val();
                $.ajax({
                    url: `/get_departments/${selectedSchoolId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if(res.status==1){
                            departmentSelect.empty(); // Clear existing options
                            departmentSelect.append(`<option value="" selected disabled>Select Department</option>`);
                           
                            $.each(res.departments, function (index, department) {
                                departmentSelect.append($('<option>', {
                                    value: department.id,
                                    text: department.name
                                }));
                            });
                        }
                    },
                    complete: function(){
                        // Enable department select
                        departmentSelect.prop('disabled', false);
                    }
                });
            });
            initialOption.addClass('initial-option');
        });
    </script>
</x-app-layout>
