@section('pageTitle', 'Create New Student')

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-start gap-4">
            <x-back-link />
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Student') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('students.create') }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('students.store') }}">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">School</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="school_id" id="school_id">
                                <option disabled selected>Select School</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}">School of {{ ucfirst($school->name) }}</option>
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

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Enrollment Number</span>
                            </label>
                            <input type="text" class="input text-neutral input-bordered bg-white w-full max-w-xl" name="enrollment_number" id="enrollment_number" placeholder="Enter Enrollment Number" value="{{ old('enrollment_number') }}" title="Only numbers and alphabets are allowed" oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '');"/>
                            <x-input-error :messages="$errors->get('enrollment_number')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Full Name</span>
                            </label>
                            <input type="text" class="input text-neutral input-bordered bg-white w-full max-w-xl" name="student_name" placeholder="Enter Full Name" value="{{ old('student_name') }}"/>
                            <x-input-error :messages="$errors->get('student_name')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Semester</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="semester">
                                <option disabled>Select Semester</option>
                                @for ($i = 1; $i <= 8; $i++)
                                    <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>Semester {{ $i }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('semester')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Start Year</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="start_year" id="start_year">
                                <option disabled selected>Select Start Year</option>
                                @for ($year = date('Y'); $year >= date('Y') - 50; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('start_year')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">End Year</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="end_year" id="end_year">
                                <option disabled selected>Select End Year</option>
                                @for ($year = date('Y') + 5; $year >= date('Y') - 60; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('end_year')" class="mt-2" />
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

    <script>
        $(document).ready(function () {
        const schoolSelect = $('#school_id');
        const departmentSelect = $('#department_id');
        const programSelect = $('#program_id');


        // const oldSchoolId = "{{ old('school_id') }}";
        // if (oldSchoolId) {
        //     schoolSelect.val(oldSchoolId);
        //     schoolSelect.trigger('change');
        // }

        schoolSelect.on('change', function () {
            const selectedSchoolId = schoolSelect.val();

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
                        }
                    },
                    complete: function () {
                        departmentSelect.prop('disabled', false);

                        // const oldDepartmentId = "{{ old('department_id') }}";
                        // if (oldDepartmentId) {

                        //     departmentSelect.val(oldDepartmentId).trigger('change');
                        // }
                    }
                });
            }
        });

        departmentSelect.on('change', function () {
            const selectedDepartmentId = departmentSelect.val();

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
                        }
                    },
                    complete: function () {
                        programSelect.prop('disabled', false);

                        // const oldProgramId = "{{ old('program_id') }}";
                        // if (oldProgramId) {
                        //     programSelect.val(oldProgramId);
                        // }
                    }
                });
            }
        });
    });


    </script>
</x-app-layout>
