@section('pageTitle', 'Courses')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Courses') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('teacher_courses.index') }}
        <div class="pb-8">
            <!-- <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
                <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                    <x-button-link href="{{ route('teacher_courses.create') }}">
                        <i class="fa fa-plus"></i> {{ __('Create New Course') }}
                    </x-button-link>
                </div>
            </div> -->
            <div class="my-4 flex justify-end pr-4">
                <label for="schoolFilter" class="mr-2">School Filter:</label>
                <select id="schoolFilter" name="schoolFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                    <option value="all">All Schools</option>
                    @foreach ($schools as $school)
                        <option value="{{ $school->id }}">School of {{ ucfirst($school->name) }}</option>
                    @endforeach
                </select>

                <!-- Department filter (initially hidden) -->
                <label for="departmentFilter" class="mr-2 ml-4" style="display: none;">Department Filter:</label>
                <select id="departmentFilter" name="departmentFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50" style="display: none;">
                    <option value="all">All Departments</option>
                </select>

                <!-- Program filter (initially hidden) -->
                <label for="programFilter" class="mr-2 ml-4" style="display: none;">Program Filter:</label>
                <select id="programFilter" name="programFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50" style="display: none;">
                    <option value="all">All Programs</option>
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

            <!-- Static block for "No Courses found" message (initially hidden) -->
            <div id="noCoursesFound" class="text-center p-8" style="display: none;">No Courses found.</div>

            <div class="course-list">
                @if($courses->count() > 0)
                    @foreach ($courses as $course)
                        
                        <div class="card bg-base-100 shadow-xl mb-5"
                                data-school="{{ $course->program->department->school->id }}"
                                data-department="{{ $course->program->department->id }}"
                                data-program="{{ $course->program->id }}"
                                data-semester="{{ $course->semester }}">
                            <div class="card-body">
                                <a href="{{ route('teacher_courses.show',$course->id) }}" class="text-blue-500 hover:text-blue-700">
                                    <h2 class="card-title">{{$course->name}}</h2>
                                </a>
                                <!-- <p>{{$course->code}} - <strong>{{ucfirst($course->type)}}</strong> - <strong>{{$course->course_credit}} Credits</strong> </p> -->
                                <p>{{$course->code}} - <strong>{{ucfirst($course->Program()->find($course->program_id)->name)}}</strong> - <strong>{{$course->program->department->name}}</strong> - <strong>School of {{ucfirst($course->program->department->school->name)}}</strong> </p>
                                
                                <!-- <div class="pt-5 card-actions border-t-2">
                                    <p>Created by <a href="{{ route('users.show', $course->creator) }}" class="text-blue-500 hover:text-blue-700">{{ $course->creator->name }}</a></p>
                                </div>
                                <div class="flex justify-end">
                                    <form action="{{ route('teacher_courses.edit',$course->id) }}" method="get">
                                        <button class="pr-10" value="{{ $course->id }}"><strong>Edit</strong></button>
                                    </form>
                                    <form action="{{route('teacher_courses.destroy', $course->id)}}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="text-red-600" value="{{ $course->id }}" onclick="return confirm('Are you sure you want to delete this course?');"><strong>Delete</strong></button>
                                    </form>
                                </div> -->
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

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
            const courseRows = $('.course-list .card');

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
</x-app-layout>
