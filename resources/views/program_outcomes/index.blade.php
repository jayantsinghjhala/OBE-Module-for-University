@section('pageTitle', 'Program Outcomes')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Program Outcomes') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('program_outcomes.index') }}
        <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
            <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                <x-button-link href="{{ route('program_outcomes.create') }}">
                    <i class="fa fa-plus"></i> {{ __('Create New Program Outcome') }}
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
        </div>

        <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <table class="border-collapse table-auto w-full bg-white table-striped relative">
                <thead>
                    <tr class="text-left">
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-6">
                            No
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                            Name
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                            Program
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                            Outcome Description
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($program_outcomes as $program_outcome)
                    @php
                        $program_outcome->load('program.department.school');
                        $program = $program_outcome->program;
                        $department = $program->department;
                        $school = $department->school;
                    @endphp

                    <tr data-school="{{ $program_outcome->program->department->school_id }}" data-department="{{ $program_outcome->program->department_id }}" data-program="{{ $program_outcome->program_id }}">
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $loop->iteration }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $program_outcome->name }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $program->name }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $program_outcome->outcome_description }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                            <a href="{{ route('program_outcomes.edit', $program_outcome) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Edit') }}</a>
                            <form action="{{ route('program_outcomes.destroy', $program_outcome->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                            </form>
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

        schoolSelect.on('change', function () {
            const selectedSchoolId = schoolSelect.val();

            // Reset and hide child filters
            if (selectedSchoolId == 'all') {
                resetAndHideFilters(departmentSelect, programSelect);
            }else{
                resetFilters(departmentSelect, programSelect);
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
                resetAndHideFilters(programSelect);
            }else{
                resetFilters(programSelect);
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
            const courseRows = $('tbody tr');

            courseRows.each(function (index, row) {
                const schoolId = $(row).data('school');
                const departmentId = $(row).data('department');
                const programId = $(row).data('program');
                
                if(!schoolId){
                    return false;
                }
                if (
                    (selectedSchool === 'all' || selectedSchool === schoolId.toString()) &&
                    (selectedDepartment === 'all' || selectedDepartment === departmentId.toString()) &&
                    (selectedProgram === 'all' || selectedProgram === programId.toString()) 
                ) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        }
    });
</script>
