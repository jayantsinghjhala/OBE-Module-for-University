@section('pageTitle', 'Create New Program Outcome')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Program Outcome') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('program_outcomes.create') }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('program_outcomes.store') }}">
                    @csrf

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">School</span>
                        </label>
                        <select name="school_id" placeholder="School Name" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select School</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>School of {{ ucfirst($school->name) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('school_id')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Department</span>
                        </label>
                        <select name="department_id" placeholder="Department Name" class="select select-bordered w-full max-w-xs">
                            <!-- Departments will be loaded dynamically based on the selected school -->
                            <option value="" selected disabled>Select Department</option>
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Program</span>
                        </label>
                        <select name="program_id" placeholder="Program Name" class="select select-bordered w-full max-w-xs">
                            <!-- Programs will be loaded dynamically based on the selected department -->
                            <option value="" selected disabled>Select Program</option>
                        </select>
                        <x-input-error :messages="$errors->get('program_id')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Program Outcome Name</span>
                        </label>
                        <select name="name" class="select select-bordered w-full max-w-xs">
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="PO{{ $i }}" {{ old('name') == 'PO'.$i ? 'selected' : '' }}>PO{{ $i }}</option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Outcome Description</span>
                        </label>
                        <textarea name="outcome_description" placeholder="Enter Outcome Description" class="textarea textarea-bordered w-full max-w-xs">{{ old('outcome_description') }}</textarea>
                        <x-input-error :messages="$errors->get('outcome_description')" class="mt-2"/>
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Create Program Outcome
                        </button>
                        <a href="{{ route('program_outcomes.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const schoolSelect = $('select[name="school_id"]');
            const departmentSelect = $('select[name="department_id"]');
            const programSelect = $('select[name="program_id"]');

            // Retain selected school value when the page is reloaded
            const selectedSchoolId = "{{ old('school_id') }}";
            if (selectedSchoolId) {
                schoolSelect.val(selectedSchoolId);
            }

            // Disable department and program selects initially
            departmentSelect.prop('disabled', true);
            programSelect.prop('disabled', true);

            schoolSelect.on('change', function () {
                const selectedSchoolId = schoolSelect.val();

                // AJAX request to load departments based on the selected school
                $.ajax({
                    url: `/get_departments/${selectedSchoolId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 1) {
                            departmentSelect.empty(); // Clear existing options
                            departmentSelect.append('<option value="" selected disabled>Select Department</option>');

                            $.each(res.departments, function (index, department) {
                                departmentSelect.append($('<option>', {
                                    value: department.id,
                                    text: department.name
                                }));
                            });

                            // Retain selected department value when the page is reloaded
                            const selectedDepartmentId = "{{ old('department_id') }}";
                            if (selectedDepartmentId) {
                                departmentSelect.val(selectedDepartmentId);
                            }

                            departmentSelect.prop('disabled', false);
                            departmentSelect.trigger('change');
                        }
                    },
                    complete: function () {
                        departmentSelect.prop('disabled', false);
                    }
                });
            });

            departmentSelect.on('change', function () {
                const selectedDepartmentId = departmentSelect.val();

                // AJAX request to load programs based on the selected department
                $.ajax({
                    url: `/get_programs/${selectedDepartmentId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 1) {
                            programSelect.empty(); // Clear existing options
                            programSelect.append('<option value="" selected disabled>Select Program</option>');

                            $.each(res.programs, function (index, program) {
                                programSelect.append($('<option>', {
                                    value: program.id,
                                    text: program.name
                                }));
                            });

                            // Retain selected program value when the page is reloaded
                            const selectedProgramId = "{{ old('program_id') }}";
                            if (selectedProgramId) {
                                programSelect.val(selectedProgramId);
                            }

                            programSelect.prop('disabled', false);
                        }
                    },
                    complete: function () {
                        programSelect.prop('disabled', false);
                    }
                });
            });

            // Trigger change event for schoolSelect initially if there's an old value
            if ("{{ old('school_id') }}") {
                schoolSelect.trigger('change');
            }
        });
    </script>

</x-app-layout>
