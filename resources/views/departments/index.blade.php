@section('pageTitle', 'Departments')
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Departments') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    {{ Breadcrumbs::render('departments.index') }}
        <div class="pb-8">
            <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
                <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                    <x-button-link href="{{ route('departments.create') }}">
                        <i class="fa fa-plus"></i> {{ __('Create New Department') }}
                    </x-button-link>
                </div>
            </div>
            
            @if($departments->count() > 0)
                <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                    
                <div class="my-4 flex justify-end pr-4">
                    <label for="schoolFilter" class="mr-2">School Filter:</label>
                    <select id="schoolFilter" name="schoolFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                        <option value="all">All Schools</option>
                        @foreach ($schools as $school)
                            <option value="{{ $school->id }}">School of {{ ucfirst($school->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <table id="departmentsTable" class="border-collapse table-auto w-full bg-white table-striped relative">
                        <thead>
                            <tr class="text-left">
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-6">
                                    No
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                    Department Name
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                                    School Name
                                </th>
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($departments as $department)
                                <tr data-school="{{ $department->school->id }}">
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $loop->index + 1 }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $department->name }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ ucfirst($department->school->name) }}</td>
                                    <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                        <div class="flex flex-wrap space-x-4">
                                            <a href="{{ route('departments.edit', $department) }}" class="text-blue-500">Edit</a>
                                            <form method="POST" action="{{ route('departments.destroy', $department) }}">
                                                @csrf
                                                @method('delete')
                                                <button class="text-red-500" onclick="event.preventDefault(); confirm('Are you sure?') && this.closest('form').submit();">{{ __('Delete') }}</button>
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
                    No Departments found.
                </div>
            @endif
        </div>
    </div>
    
    <script>
        $(document).ready(function () {
            const schoolSelect = $('#schoolFilter');
            const departmentRows = $('tbody tr');

            schoolSelect.on('change', function () {
                const selectedSchoolId = schoolSelect.val();

                departmentRows.each(function (index, row) {
                    const rowSchoolId = $(row).data('school');

                    if (selectedSchoolId === 'all' || selectedSchoolId === rowSchoolId.toString()) {
                        $(row).show();
                    } else {
                        $(row).hide();
                    }
                });
            });
        });
    </script>
</x-app-layout>

