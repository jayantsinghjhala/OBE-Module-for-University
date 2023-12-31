@section('pageTitle', 'Assign New Course')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign New Course') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('assign_courses.create') }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('assign_courses.store') }}">
                    @csrf

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">School</span>
                        </label>
                        <select name="school_id" placeholder="School Name" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select School</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" >School of {{ ucfirst($school->name) }}</option>
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
                            <span class="label-text">Program</span>
                        </label>
                        <select name="program_id" placeholder="Program Name" class="select select-bordered w-full max-w-xs">
                            <!-- Programs will be loaded dynamically based on the selected department -->
                            <option value="" selected disabled>Select Program</option>
                        </select>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Semester</span>
                        </label>
                        <select name="semester" placeholder="Semester" class="select select-bordered w-full max-w-xs" >
                            <option value="" selected disabled>Select Semester</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Course</span>
                        </label>
                        <select name="course_id" placeholder="Course Name" class="select select-bordered w-full max-w-xs">
                            <!-- Courses will be loaded dynamically based on the selected program and semester -->
                            <option value="" selected disabled>Select Course</option>
                        </select>
                        <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Faculty (Teacher)</span>
                        </label>
                        <select name="teacher_id" placeholder="Select Faculty" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select Faculty</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('teacher_id')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Role</span>
                        </label>
                        <select name="role" placeholder="Select Teacher Role" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select Teacher Role</option>
                            <option value="primary" {{ old('role') === 'primary' ? 'selected' : '' }}>Primary Teacher</option>
                            <option value="secondary" {{ old('role') === 'secondary' ? 'selected' : '' }}>Secondary Teacher</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>


                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Assign Course
                        </button>
                        <a href="{{ route('assign_courses.index') }}">{{ __('Cancel') }}</a>
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
        const semesterSelect = $('select[name="semester"]');
        const courseSelect = $('select[name="course_id"]');

        // Disable department, program, course, and semester selects initially
        departmentSelect.prop('disabled', true);
        programSelect.prop('disabled', true);
        semesterSelect.prop('disabled', true);
        courseSelect.prop('disabled', true);

        schoolSelect.on('change', function () {
            // Disable department, program, course, and semester selects initially
            departmentSelect.prop('disabled', true);
            programSelect.prop('disabled', true);
            semesterSelect.prop('disabled', true);
            courseSelect.prop('disabled', true);

            const selectedSchoolId = schoolSelect.val();

            $.ajax({
                url: `/get_departments/${selectedSchoolId}`,
                method: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) {
                        departmentSelect.empty();
                        departmentSelect.append('<option value="" selected disabled>Select Department</option>');

                        $.each(res.departments, function (index, department) {
                            departmentSelect.append($('<option>', {
                                value: department.id,
                                text: department.name
                            }));
                        });

                        departmentSelect.prop('disabled', false);
                    }
                }
            });
        });

        departmentSelect.on('change', function () {
            programSelect.prop('disabled', true);
            semesterSelect.prop('disabled', true);
            courseSelect.prop('disabled', true);

            const selectedDepartmentId = departmentSelect.val();

            $.ajax({
                url: `/get_programs/${selectedDepartmentId}`,
                method: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) {
                        programSelect.empty();
                        programSelect.append('<option value="" selected disabled>Select Program</option>');

                        $.each(res.programs, function (index, program) {
                            programSelect.append($('<option>', {
                                value: program.id,
                                text: program.name
                            }));
                        });

                        programSelect.prop('disabled', false);
                    }
                }
            });
        });

        programSelect.on('change', function () {
            semesterSelect.prop('disabled', false);
        });

        semesterSelect.on('change', function () {
            courseSelect.prop('disabled', true);

            const selectedProgramId = programSelect.val();
            const selectedSemester = semesterSelect.val();

            $.ajax({
                url: `/get_courses/${selectedProgramId}/${selectedSemester}`,
                method: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) {
                        courseSelect.empty();
                        courseSelect.append('<option value="" selected disabled>Select Course</option>');

                        $.each(res.courses, function (index, course) {
                            const courseText = `${course.name} - (${course.code})`;
                            courseSelect.append($('<option>', {
                                value: course.id,
                                text: courseText
                            }));
                        });

                        courseSelect.prop('disabled', false);
                    }
                }
            });
        });
    });
    </script>
</x-app-layout>
