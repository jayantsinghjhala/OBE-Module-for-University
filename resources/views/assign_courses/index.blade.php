@section('pageTitle', 'Assign Courses')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Courses') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('assign_courses.index') }}
        <div class="">
            <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
                <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                    <x-button-link href="{{ route('assign_courses.create') }}">
                        <i class="fa fa-plus"></i> {{ __('Assign New Course') }}
                    </x-button-link>
                </div>
            </div>

            <div class="my-4 flex justify-end pr-4">
                <label for="schoolFilter" class="mr-2">School Filter:</label>
                <select id="schoolFilter" name="schoolFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                    <option value="all">All Schools</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}">School of {{ ucfirst($school->name) }}</option>
                    @endforeach
                </select>

                <!-- Department filter select (initially hidden) -->
                <label for="departmentFilter" class="mr-2 ml-4" style="display: none;">Department Filter:</label>
                <select id="departmentFilter" name="departmentFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50" style="display: none;">
                    <option value="all">All Departments</option>
                    <!-- Departments will be dynamically loaded here -->
                </select>

                <!-- Program filter select (initially hidden) -->
                <label for="programFilter" class="mr-2 ml-4" style="display: none;">Program Filter:</label>
                <select id="programFilter" name="programFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50" style="display: none;">
                    <option value="all">All Programs</option>
                    <!-- Populate with available programs -->
                </select>

                <!-- Semester filter select (initially hidden) -->
                <label for="semesterFilter" class="mr-2 ml-4" style="display: none;">Semester Filter:</label>
                <select id="semesterFilter" name="semesterFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50" style="display: none;">
                    <option value="all">All Semesters</option>
                    <option value="1">Semester 1</option>
                    <option value="2">Semester 2</option>
                    <option value="3">Semester 3</option>
                    <option value="4">Semester 4</option>
                    <option value="5">Semester 5</option>
                    <option value="6">Semester 6</option>
                    <option value="7">Semester 7</option>
                    <option value="8">Semester 8</option>
                </select>

                <label for="facultyFilter" class="mr-2 ml-4">Faculty Filter:</label>
                <select id="facultyFilter" name="facultyFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                    <option value="all">All Faculty</option>
                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->name }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-6">
                                No
                            </th>
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                Course Name
                            </th>
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                Program, Department, School
                            </th>
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                                Semester
                            </th>
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                                Faculty
                            </th>
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                         <!-- Display Primary Courses -->
                        @php $Counter = 1; @endphp
                        @foreach ($primaryCourses as $course)
                            @foreach ($course->teachers as $teacher)
                            @if ($teacher->pivot->role === 'primary')
                            <tr data-school="{{ $course->program->department->school_id }}" data-department="{{ $course->program->department_id }}" data-program="{{ $course->program_id }}" data-semester="{{ $course->semester }}" data-faculty="{{ $teacher->name }}">
                                <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $Counter }}</td>
                                <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ ucwords($course->name) }}</td>
                                <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate ">{{ ucwords($course->program->name) }}, {{ ucwords($course->program->department->name) }}, {{ ucfirst($course->program->department->school->name) }}</td>
                                <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $course->semester }}</td>
                                <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                            <span class="primary-teacher">{{ $teacher->name }}</span>
                                </td>
                                <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                    <a href="{{ route('assign_courses.edit_primary', $course) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Edit Primary') }}</a>
                                    <form action="{{ route('assign_courses.unassign_primary', $course->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Unassign Primary') }}</button>
                                    </form>
                                </td>
                                @php $Counter++; @endphp
                            </tr>
                            @endif
                            @endforeach
                        @endforeach

                        <!-- Display Secondary Courses -->
                        <!-- Display Secondary Courses -->
                        @foreach ($secondaryCourses as $course)
                            @foreach ($course->teachers as $teacher)
                                @if ($teacher->pivot->role === 'secondary')
                                    <tr data-school="{{ $course->program->department->school_id }}" data-department="{{ $course->program->department_id }}" data-program="{{ $course->program_id }}" data-semester="{{ $course->semester }}" data-faculty="{{ $teacher->name }}">
                                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $Counter }}</td>
                                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ ucwords($course->name) }}</td>
                                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ ucwords($course->program->name) }}, {{ ucwords($course->program->department->name) }}, {{ ucfirst($course->program->department->school->name) }}</td>
                                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $course->semester }}</td>
                                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                            <span class="secondary-teacher">{{ $teacher->name }}</span><br>
                                        </td>
                                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                        <a href="{{ route('assign_courses.edit_secondary', ['course_id' => $course->id, 'teacher_id' => $teacher->id]) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Edit Secondary') }}</a>
                                            <form action="{{ route('assign_courses.unassign_secondary', $course->id) }}" method="POST" class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Unassign Secondary') }}</button>
                                            </form>
                                        </td>
                                        @php $Counter++; @endphp
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach

                       
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
    $(document).ready(function () {
        const schoolSelect = $('#schoolFilter');
        const departmentSelect = $('#departmentFilter');
        const programSelect = $('#programFilter');
        const semesterSelect = $('#semesterFilter');
        const facultySelect = $('#facultyFilter');


        schoolSelect.on('change', function () {
            const selectedSchoolId = schoolSelect.val();

            // Reset and hide child filters
            if (selectedSchoolId == 'all') {
                resetAndHideFilters(departmentSelect, programSelect, semesterSelect);
            }else{
                resetFilters(departmentSelect, programSelect, semesterSelect);
            }

            // Use an AJAX request to load departments based on the selected school
            if (selectedSchoolId !== 'all') {
                $.ajax({
                    url: `/get_departments/${selectedSchoolId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 1) {
                            // Populate the Department filter
                            populateFilter(departmentSelect, res.departments);
                        }
                    }
                });
            }


            // Call the function to filter courses based on school, department, program, and semester
            filterCourses();
        });

        departmentSelect.on('change', function () {
            const selectedDepartmentId = departmentSelect.val();

            // Reset and hide child filters
            if (selectedDepartmentId == 'all') {
                resetAndHideFilters(programSelect, semesterSelect);
            }else{
                resetFilters(programSelect, semesterSelect);
            }

            // Use an AJAX request to load programs based on the selected department
            if (selectedDepartmentId !== 'all') {
                $.ajax({
                    url: `/get_programs/${selectedDepartmentId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 1) {
                            // Populate the Program filter
                            populateFilter(programSelect, res.programs);
                        }
                    }
                });
            }

            // Call the function to filter courses based on school, department, program, and semester
            filterCourses();
        });

        programSelect.on('change', function () {
            const selectedProgramId = programSelect.val();

            // Reset and hide child filters
            if (selectedProgramId == 'all') {
                resetAndHideFilters(semesterSelect);
            }else{
                resetFilters(semesterSelect);
            }

            // Show the Semester filter when a program is selected
            if (selectedProgramId !== 'all') {
                semesterSelect.show();
                semesterSelect.prev('label').show();
            }

            // Call the function to filter courses based on school, department, program, and semester
            filterCourses();
        });

        semesterSelect.on('change', function () {
            // Call the function to filter courses based on school, department, program, and semester
            filterCourses();
        });

        facultySelect.on('change', function () {
            // Call the function to filter courses based on school, department, program, semester, and faculty
            filterCourses();
        });

        function resetAndHideFilters(...filters) {
            filters.forEach(filter => {
                filter.hide();
                filter.prev('label').hide();
                filter.val('all');
            });
        }
        function resetFilters(...filters) {
            filters.forEach(filter => {
                filter.val('all');
            });
        }

        function populateFilter(filter, data) {
            filter.empty();
            filter.append('<option value="all">All</option>');

            $.each(data, function (index, item) {
                filter.append($('<option>', {
                    value: item.id,
                    text: item.name
                }));
            });

            // Show the filter
            filter.show();
            filter.prev('label').show();
        }

        function filterCourses() {
            const selectedSchool = schoolSelect.val();
            const selectedDepartment = departmentSelect.val();
            const selectedProgram = programSelect.val();
            const selectedSemester = semesterSelect.val();
            const selectedFaculty = facultySelect.val();
            const courseRows = $('tbody tr');

            courseRows.each(function (index, row) {
                const schoolId = $(row).data('school');
                const departmentId = $(row).data('department');
                const programId = $(row).data('program');
                const semester = $(row).data('semester');
                const faculty = $(row).data('faculty');
                
                if(!faculty){
                    return false;
                }

                if (
                    (selectedSchool === 'all' || selectedSchool === schoolId.toString()) &&
                    (selectedDepartment === 'all' || selectedDepartment === departmentId.toString()) &&
                    (selectedProgram === 'all' || selectedProgram === programId.toString()) &&
                    (selectedSemester === 'all' || selectedSemester === semester.toString()) &&
                    (selectedFaculty === 'all' || selectedFaculty === faculty.toString())
                ) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        }
    });
</script>