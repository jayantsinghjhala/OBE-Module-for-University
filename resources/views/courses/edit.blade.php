@section('pageTitle', "Edit Course $course->name")

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-start gap-4">
            <x-back-link />
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Course')." $course->name" }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('courses.edit', $course) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <input type="hidden" name="department_store" value="{{$current_department}}">
                <input type="hidden" name="program_store" value="{{$course->program_id }}">

                <form method="POST" action="{{ route('courses.update', $course) }}">
                    @csrf
                    @method('patch')
                    <div class="grid grid-cols-2 gap-1">
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

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Course Code</span>
                            </label>
                            <input type="text" class="input text-neutral input-bordered bg-white w-full max-w-xl" name="code" placeholder="Enter Course Code" value="{{ old('code', $course->code) }}"/>
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Course Name</span>
                            </label>
                            <input type="text" class="input text-neutral input-bordered bg-white w-full max-w-xl" name="name"  placeholder="Enter Course Name" value="{{ old('name', $course->name) }}"/>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Course Credit</span>
                            </label>
                            <input type="text" class="input text-neutral input-bordered bg-white w-full max-w-xl" name="course_credit"  placeholder="Enter Course Credit" value="{{ old('course_credit', $course->course_credit) }}" />
                            <x-input-error :messages="$errors->get('course_credit')" class="mt-2" />
                        </div>

                        <div class="form-control w-full max-w-xl">
                            <label class="label">
                                <span class="label-text text-neutral font-bold">Course Type</span>
                            </label>
                            <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="type" id="type">
                                <option disabled>Select Course Type</option>
                                <option value="mandatory" {{ $course->type == "mandatory"? "selected": "" }}>Mandatory</option>
                                <option value="elective" {{ $course->type == "elective"? "selected": "" }}>Elective</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </div>
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
