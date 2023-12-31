@section('pageTitle', 'Courses')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Courses') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('courses.index') }}
        <div class="pb-8">
            <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
                <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                    <x-button-link href="{{ route('courses.create') }}">
                        <i class="fa fa-plus"></i> {{ __('Create New Course') }}
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
            </div>

            <!-- Static block for "No Courses found" message (initially hidden) -->
            <div id="noCoursesFound" class="text-center p-8" style="display: none;">No Courses found.</div>

            <div class="course-list">
                @if($courses->count() > 0)
                    @foreach ($courses as $course)
                        <div class="card bg-base-100 shadow-xl mb-5"
                                data-school="{{ $course->program->department->school->id }}"
                                data-department="{{ $course->program->department->id }}"
                                data-program="{{ $course->program->id }}">
                            <div class="card-body">
                                <a href="{{ route('courses.show',$course->id) }}" class="text-blue-500 hover:text-blue-700">
                                    <h2 class="card-title">{{$course->name}}</h2>
                                </a>
                                <p>{{$course->code}} - <strong>{{ucfirst($course->type)}}</strong> - <strong>{{$course->course_credit}} Credits</strong> </p>
                                <div class="pt-5 card-actions border-t-2">
                                    <p>Created by <a href="{{ route('users.show', $course->creator) }}" class="text-blue-500 hover:text-blue-700">{{ $course->creator->name }}</a></p>
                                </div>
                                <div class="flex justify-end">
                                    <form action="{{ route('courses.edit',$course->id) }}" method="get">
                                        <button class="pr-10" value="{{ $course->id }}"><strong>Edit</strong></button>
                                    </form>
                                    <form action="{{route('courses.destroy', $course->id)}}" method="post">
                                        @csrf
                                        @method('delete')
                                        <button class="text-red-600" value="{{ $course->id }}" onclick="return confirm('Are you sure you want to delete this course?');"><strong>Delete</strong></button>
                                    </form>
                                </div>
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

            schoolSelect.on('change', function () {
                const selectedSchoolId = schoolSelect.val();
                const selectedDepartmentId = departmentSelect.val();

                // Use an AJAX request to load departments based on the selected school
                $.ajax({
                    url: `/get_departments/${selectedSchoolId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 1) {
                            departmentSelect.empty();
                            departmentSelect.append('<option value="all">All Departments</option>');

                            $.each(res.departments, function (index, department) {
                                departmentSelect.append($('<option>', {
                                    value: department.id,
                                    text: department.name
                                }));
                            });

                            // Show the department filter once departments are loaded
                            departmentSelect.show();
                            departmentSelect.prev('label').show();

                            // Hide the program filter
                            programSelect.val('all').hide();
                            programSelect.prev('label').hide();
                        } else {
                            // Hide the department filter if no departments are available
                            departmentSelect.hide();
                            departmentSelect.prev('label').hide();

                            // Hide the program filter
                            programSelect.val('all').hide();
                            programSelect.prev('label').hide();
                        }

                        // Call the function to filter courses based on school and department
                        filterCourses();
                    }
                });
            });

            // Add an event listener to the department filter for filtering courses
            departmentSelect.on('change', function () {
                const selectedDepartmentId = departmentSelect.val();

                // Use an AJAX request to load programs based on the selected department
                $.ajax({
                    url: `/get_programs/${selectedDepartmentId}`,
                    method: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.status == 1) {
                            programSelect.empty();
                            programSelect.append('<option value="all">All Programs</option>');

                            $.each(res.programs, function (index, program) {
                                programSelect.append($('<option>', {
                                    value: program.id,
                                    text: program.name
                                }));
                            });

                            // Show the program filter once programs are loaded
                            programSelect.show();
                            programSelect.prev('label').show();
                        } else {
                            // Hide the program filter if no programs are available
                            programSelect.hide();
                            programSelect.prev('label').hide();
                        }

                        // Call the function to filter courses based on school, department, and program
                        filterCourses();
                    }
                });
            });

            // Add an event listener to the program filter for filtering courses
            programSelect.on('change', function () {
                filterCourses();
            });

            // Function to filter courses based on selected filters
            function filterCourses() {
                const selectedSchool = schoolSelect.val();
                const selectedDepartment = departmentSelect.val();
                const selectedProgram = programSelect.val();
                const courseCards = $('.card');

                // Hide all courses before filtering
                courseCards.hide();

                courseCards.each(function (index, card) {
                    const schoolId = $(card).data('school');
                    const departmentId = $(card).data('department');
                    const programId = $(card).data('program');

                    if (
                        (selectedSchool === 'all' || selectedSchool === schoolId.toString()) &&
                        (selectedDepartment === 'all' || selectedDepartment === departmentId.toString()) &&
                        (selectedProgram === 'all' || selectedProgram === programId.toString())
                    ) {
                        $(card).show();
                    }
                });

                // Display the "No Courses found" message if no courses match the filter
                const visibleCourses = courseCards.filter(':visible');
                if (visibleCourses.length === 0) {
                    $('#noCoursesFound').show();
                } else {
                    $('#noCoursesFound').hide();
                }
            }

            // Initial filtering when the page loads
            filterCourses();
        });
    </script>
</x-app-layout>
