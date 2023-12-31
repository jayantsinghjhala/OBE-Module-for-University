@section('pageTitle', 'Students List')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Students') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-8">
        {{ Breadcrumbs::render('students.index') }}
        <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
            <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                <a href="{{ route('students.create') }}" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-2.5 sm:px-4 py-2 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="pr-1">Create New Student</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                         viewBox="0 0 612.001 612.001" style="enable-background:new 0 0 612.001 612.001;" xml:space="preserve">
                        <!-- Icon for creating a new student -->
                    </svg>
                </a>
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
        </div>
        <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead>
                    <tr class="text-left">
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Name
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Enrollment Number
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Program Name
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Session
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Current Semester
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Created Date
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($students as $student)
                    @php
                        $student->load('program.department.school');
                        $program = $student->program;
                        $department = $program->department;
                        $school = $department->school;
                    @endphp

                    <tr data-school="{{ $school->id }}" data-department="{{ $department->id }}" data-program="{{ $program->id }}" data-semester="{{ $student->semester }}">
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $student['student_name'] }}</td>
                        <!-- Add other columns here -->
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $student['enrollment_number'] }}</td>
                        
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            @php
                                $program = App\Models\Program::find($student['program_id']);
                                if ($program) {
                                    echo $program->name;
                                }
                            @endphp
                        </td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $student['session'] }}</td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $student['semester'] }}</td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            @php
                                $createdDate = \Carbon\Carbon::parse($student['created_at']);
                                echo $createdDate->format('d F Y');
                            @endphp
                        </td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            <div class="flex flex-wrap space-x-4">
                                <a href="{{ route('students.edit', $student) }}" class="text-blue-500">Edit</a>
                                <form method="POST" action="{{ route('students.destroy', $student) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="text-red-500" onclick="event.preventDefault(); confirm('Are you sure?') && this.closest('form').submit();">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 px-4 py-1 text-sm">
                            No Records Found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        const schoolSelect = $('#schoolFilter');
        const departmentSelect = $('#departmentFilter');
        const programSelect = $('#programFilter');
        const semesterSelect = $('#semesterFilter');

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
            const courseRows = $('tbody tr');

            courseRows.each(function (index, row) {
                const schoolId = $(row).data('school');
                const departmentId = $(row).data('department');
                const programId = $(row).data('program');
                const semester = $(row).data('semester');
                
                if(!schoolId){
                    return false;
                }
                if (
                    (selectedSchool === 'all' || selectedSchool === schoolId.toString()) &&
                    (selectedDepartment === 'all' || selectedDepartment === departmentId.toString()) &&
                    (selectedProgram === 'all' || selectedProgram === programId.toString()) &&
                    (selectedSemester === 'all' || selectedSemester === semester.toString())
                ) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        }
    });
</script>