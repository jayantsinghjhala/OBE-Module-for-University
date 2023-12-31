@section('pageTitle', "All Programs")

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Programs
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('programs.index') }}
        <div class="">
            <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
                <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                    <x-button-link href="{{ route('programs.create') }}">
                        <i class="fa fa-plus"></i> {{ __('Create New Program') }}
                    </x-button-link>
                </div>
            </div>
            @if ($programs->count() > 0)
                <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    <div class="my-4 flex justify-end pr-4">
                        <label for="schoolFilter" class="mr-2">School Filter:</label>
                        <select id="schoolFilter" name="schoolFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                            <option value="all">All Schools</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}">School of {{ ucfirst($school->name) }}</option>
                            @endforeach
                        </select>

                        <!-- Add the department filter select element (initially hidden) -->
                        <label for="departmentFilter" class="mr-2 ml-4" style="display: none;">Department Filter:</label>
                        <select id="departmentFilter" name="departmentFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50" style="display: none;">
                            <option value="all">All Departments</option>
                            <!-- Departments will be dynamically loaded here -->
                        </select>
                    </div>

                    <table class="border-collapse table-auto w-full bg-white table-striped relative">
                        <thead>
                            <tr class="text-left">
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-6">
                                    No
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                    Program Name
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                    Department
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                    School
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($programs as $program)
                                <tr data-school="{{ $program->department->school->id }}" data-department="{{ $program->department->id }}">
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $loop->index + 1 }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $program->name }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $program->department->name }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ ucfirst($program->department->school->name) }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                        <div class="flex flex-wrap space-x-4">
                                            <a href="{{ route('programs.edit', $program) }}" class="text-blue-500">Edit</a>
                                            <form method="POST" action="{{ route('programs.destroy', $program) }}">
                                                @csrf
                                                @method('delete')
                                                <button class="text-red-500" onclick="event.preventDefault(); confirm('Are you sure?') && this.closest('form').submit();">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center p-8">
                    No Programs found.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        const schoolSelect = $('#schoolFilter');
        const departmentSelect = $('#departmentFilter');

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
                    } else {
                        // Hide the department filter if no departments are available
                        departmentSelect.hide();
                        departmentSelect.prev('label').hide();
                    }

                    // Call the function to filter programs based on both school and department
                    filterPrograms();
                }
            });
        });

        // Add an event listener to the department filter for filtering programs
        departmentSelect.on('change', function () {
            filterPrograms();
        });

        // Function to filter programs based on selected filters
        function filterPrograms() {
            const selectedSchool = schoolSelect.val();
            const selectedDepartment = departmentSelect.val();
            const programRows = $('tbody tr');

            programRows.each(function (index, row) {
                const schoolId = $(row).data('school');
                const departmentId = $(row).data('department');

                if (
                    (selectedSchool === 'all' || selectedSchool === schoolId.toString()) &&
                    (selectedDepartment === 'all' || selectedDepartment === departmentId.toString())
                ) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        }
    });
</script>
