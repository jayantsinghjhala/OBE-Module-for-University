@section('pageTitle', "Edit Student")

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Student').": $student->student_name" }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('students.edit', $student) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <input type="hidden" name="department_store" value="{{$current_department}}">
                <input type="hidden" name="program_store" value="{{$student->program_id }}">
                <form method="POST" action="{{ route('students.update', $student) }}">
                    @csrf
                    @method('patch')
                    <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">School</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="school_id" id="school_id" >
                                <option disabled selected>Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}"{{ $current_school == $school->id? "selected": "" }}>School of {{ ucfirst($school->name) }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Department</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="department_id" id="department_id" disabled>
                                <option disabled selected>Select Department</option>
                            </select>
                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Program</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="program_id" id="program_id" disabled>
                                <option disabled selected>Select Program</option>
                            </select>
                            <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
                        </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Student Name</span>
                        </label>
                        <input type="text" name="student_name" placeholder="Student Name" class="input input-bordered w-full max-w-xs" value="{{ $student->student_name }}" />
                        <x-input-error :messages="$errors->get('student_name')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Enrollment Number</span>
                        </label>
                        <input type="text" name="enrollment_number" placeholder="Enrollment Number" class="input input-bordered w-full max-w-xs" value="{{ $student->enrollment_number }}" />
                        <x-input-error :messages="$errors->get('enrollment_number')" class="mt-2" />
                    </div>


                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Current Semester</span>
                        </label>
                        <select name="semester" placeholder="Current Semester" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select Semester</option>
                            @for ($i = 1; $i <= 8; $i++)
                                <option value="{{ $i }}" {{ $student->semester == $i ? 'selected' : '' }}>
                                    Semester {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    @php
                        $session=explode('-',$student->session);
                        $start_year=$session[0];
                        $end_year=$session[1];
                    @endphp
                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Start Year</span>
                        </label>
                        <select class="select select-bordered w-full max-w-xs" name="start_year" id="start_year">
                            <option disabled selected>Select Start Year</option>
                            @for ($year = date('Y'); $year >= date('Y') - 50; $year--)
                                <option value="{{ $year }}" {{ $start_year == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('start_year')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">End Year</span>
                        </label>
                        <select class="select select-bordered w-full max-w-xs" name="end_year" id="end_year">
                            <option disabled selected>Select End Year</option>
                            @for ($year = date('Y') + 5; $year >= date('Y') - 60; $year--)
                                <option value="{{ $year }}" {{ $end_year == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('end_year')" class="mt-2" />
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <a href="{{ route('students.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const schoolSelect = $('#school_id');
            const departmentSelect = $('#department_id');
            const programSelect = $('#program_id');

            // Function to fetch departments based on the selected school
            function fetchDepartments(selectedSchoolId) {
                // Disable department and program selects initially
                departmentSelect.prop('disabled', true);
                programSelect.prop('disabled', true);

                if (selectedSchoolId !== '') {
                    // Fetch departments based on the selected school via AJAX
                    $.ajax({
                        url: `/get_departments/${selectedSchoolId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == 1) {
                                departmentSelect.empty(); // Clear existing options
                                departmentSelect.append(`<option value="" selected disabled>Select Department</option>`);

                                $.each(res.departments, function (index, department) {
                                    departmentSelect.append($('<option>', {
                                        value: department.id,
                                        text: department.name
                                    }));
                                });

                                // Set the department value if it exists in the input element
                                const departmentStoreValue = $("input[name='department_store']").val();
                                if (departmentStoreValue) {
                                    departmentSelect.val(departmentStoreValue);
                                    $("input[name='department_store']").val(''); // Clear the department_store input
                                }

                                // Trigger the change event to load programs for the selected department
                                departmentSelect.trigger('change');
                            }
                        },
                        complete: function () {
                            departmentSelect.prop('disabled', false);
                        }
                    });
                }
            }

            // Function to fetch programs based on the selected department
            function fetchPrograms(selectedDepartmentId) {
                // Disable program select initially
                programSelect.prop('disabled', true);

                if (selectedDepartmentId !== '') {
                    // Fetch programs based on the selected department via AJAX
                    $.ajax({
                        url: `/get_programs/${selectedDepartmentId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == 1) {
                                programSelect.empty(); // Clear existing options
                                programSelect.append(`<option value="" selected disabled>Select Program</option>`);

                                $.each(res.programs, function (index, program) {
                                    programSelect.append($('<option>', {
                                        value: program.id,
                                        text: program.name
                                    }));
                                });

                                // Set the program value if it exists in the input element
                                const programStoreValue = $("input[name='program_store']").val();
                                if (programStoreValue) {
                                    programSelect.val(programStoreValue);
                                    $("input[name='program_store']").val(''); // Clear the program_store input
                                }
                            }
                        },
                        complete: function () {
                            programSelect.prop('disabled', false);
                        }
                    });
                }
            }

            // Handle school selection change event
            schoolSelect.on('change', function () {
                const selectedSchoolId = schoolSelect.val();
                fetchDepartments(selectedSchoolId);
            });

            // Handle department selection change event
            departmentSelect.on('change', function () {
                const selectedDepartmentId = departmentSelect.val();
                fetchPrograms(selectedDepartmentId);
            });

            // Trigger initial loading based on the selected school (if it exists)
            const selectedSchoolId = schoolSelect.val();
            if (selectedSchoolId) {
                fetchDepartments(selectedSchoolId);
            }

            
        });
    </script>
</x-app-layout>
