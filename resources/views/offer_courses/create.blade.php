@section('pageTitle', 'Offer New Course')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Offer New Course') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('offer_courses.create') }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form method="POST" action="{{ route('offer_courses.store') }}">
                    @csrf

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">School</span>
                        </label>
                        <select name="school_id" placeholder="School Name" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select School</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}">School of {{ ucfirst($school->name) }}</option>
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
                            <span class="label-text">Course</span>
                        </label>
                        <select name="course_id" placeholder="Course Name" class="select select-bordered w-full max-w-xs">
                            <!-- Courses will be loaded dynamically based on the selected program -->
                            <option value="" selected disabled>Select Course</option>
                        </select>
                        <x-input-error :messages="$errors->get('course_id')" class="mt-2"/>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Semester</span>
                        </label>
                        <select name="semester" placeholder="Semester" class="select select-bordered w-full max-w-xs">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                        <x-input-error :messages="$errors->get('semester')" class="mt-2"/>
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Offer Course
                        </button>
                        <a href="{{ route('offer_courses.index') }}">{{ __('Cancel') }}</a>
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
        const courseSelect = $('select[name="course_id"]');

        // Disable department, program, and course selects initially
        departmentSelect.prop('disabled', true);
        programSelect.prop('disabled', true);
        courseSelect.prop('disabled', true);

        schoolSelect.on('change', function () {
            // Disable department, program, and course selects initially
            departmentSelect.prop('disabled', true);
            programSelect.prop('disabled', true);
            courseSelect.prop('disabled', true);

            const selectedSchoolId = schoolSelect.val();

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

                        // Enable department select once departments are loaded
                        departmentSelect.prop('disabled', false);
                    }
                },
                complete: function () {
                    // Enable department select
                    departmentSelect.prop('disabled', false);
                }
            });
        });

        departmentSelect.on('change', function () {
            // Disable program and course selects initially
            programSelect.prop('disabled', true);
            courseSelect.prop('disabled', true);

            const selectedDepartmentId = departmentSelect.val();

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

                        // Enable program select once programs are loaded
                        programSelect.prop('disabled', false);
                    }
                },
                complete: function () {
                    // Enable program select
                    programSelect.prop('disabled', false);
                }
            });
        });

        programSelect.on('change', function () {
            // Disable course select initially
            courseSelect.prop('disabled', true);

            const selectedProgramId = programSelect.val();

            $.ajax({
                url: `/get_courses/${selectedProgramId}`,
                method: 'GET',
                dataType: 'json',
                success: function (res) {
                    if (res.status == 1) {
                        courseSelect.empty(); // Clear existing options
                        courseSelect.append('<option value="" selected disabled>Select Course</option>');

                        $.each(res.courses, function (index, course) {
                            // Concatenate course name and code
                            const courseText = `${course.name}-(${course.code})`;
                            courseSelect.append($('<option>', {
                                value: course.id,
                                text: courseText
                            }));
                        });

                        // Enable course select once courses are loaded
                        courseSelect.prop('disabled', false);
                    }
                },
                complete: function () {
                    // Enable course select
                    courseSelect.prop('disabled', false);
                }
            });
        });
    });
</script>

</x-app-layout>
