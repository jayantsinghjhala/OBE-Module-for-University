@section('pageTitle', 'Course Outcomes')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Course Outcomes') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('course_outcomes.co_index',$course) }}
        <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
            <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                <x-button-link href="{{ route('course_outcomes.create',$course) }}">
                    <i class="fa fa-plus"></i> {{ __('Create New Course Outcome') }}
                </x-button-link>
            </div>
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
                            Course
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                            Outcome Description
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                            Active
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-48">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($course_outcomes as $course_outcome)
                    @php
                        $course_outcome->load('course.program.department.school');
                        $program = $course_outcome->course->program;
                        $department = $program->department;
                        $school = $department->school;
                    @endphp

                    <tr data-school="{{ $course_outcome->course->program->department->school_id }}" data-department="{{ $course_outcome->course->program->department_id }}" data-program="{{ $course_outcome->course->program_id }}">
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $loop->iteration }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $course_outcome->name }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $course->name }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">{{ $course_outcome->outcome_description }}</td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                            <!-- Checkbox for Active/Inactive -->
                            <input type="checkbox" class="status-checkbox" data-route="{{ route('course_outcomes.update_status', $course_outcome->id) }}" data-course_outcome="{{ $course_outcome->id }}" {{ $course_outcome->status ? 'checked' : '' }}>
                        </td>
                        <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                            <a href="{{ route('course_outcomes.edit', [$course->id, $course_outcome->id]) }}" class="text-blue-600 hover:text-blue-900 mr-2">{{ __('Edit') }}</a>
                            <form action="{{ route('course_outcomes.destroy',[$course->id, $course_outcome->id]) }}" method="POST" class="inline-block">
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
        
        // Handle checkbox changes and auto-save
        $('.status-checkbox').change(function () {
            const course = $(this).data('course_outcome');
            const isChecked = $(this).prop('checked') ? 1 : 0;

            // AJAX call to update the status
            $.ajax({
                url: $(this).data('route'),
                method: 'POST',
                data: { status: isChecked },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    // Handle success if needed
                    console.log('Status updated successfully');
                },
                error: function (error) {
                    // Handle error if needed
                    console.error('Error updating status:', error);
                }
            });
        });
    });
</script>
