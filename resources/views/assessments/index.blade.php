@section('pageTitle', 'Assessments List')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assessments') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-8">
        {{ Breadcrumbs::render('assessments.index') }}
        <div class="flex flex-row sm:justify-end mb-3 px-4 sm:px-0 -mr-2 sm:-mr-3">
            <div class="order-5 sm:order-6 mr-2 sm:mr-3">
                <a href="{{ route('assessments.create') }}" class="w-full bg-white border border-gray-300 rounded-md shadow-sm px-2.5 sm:px-4 py-2 inline-flex justify-center text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="pr-1">Create New Assessment</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 612.001 612.001" style="enable-background:new 0 0 612.001 612.001;" xml:space="preserve">
                        <!-- Icon for creating a new assessment -->
                    </svg>
                </a>
            </div>
        </div>
        <div class="my-4 flex justify-end pr-4">
                <label for="assessmentTypeFilter" class="mr-2">Assessment Type Filter:</label>
                <select id="assessmentTypeFilter" name="assessmentTypeFilter" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                    <option value="all">All Types</option>
                    <option value="CIA">CIA</option>
                    <option value="ETA">ETA</option>
                </select>
        </div>
        <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead>
                    <tr class="text-left">
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Name
                        </th>
                        <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Type
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
                @forelse ($assessments as $assessment)
                    <tr>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $assessment->assessment_name }}</td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $assessment->assessment_type }}</td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            @php
                                $createdDate = \Carbon\Carbon::parse($assessment->created_at);
                                echo $createdDate->format('d F Y');
                            @endphp
                        </td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            <div class="flex flex-wrap space-x-4">
                                @if(auth()->user()->role === 'admin' || auth()->user()->id === $assessment->user_id)
                                    <a href="{{ route('assessments.edit', $assessment) }}" class="text-blue-500">Edit</a>
                                    <form method="POST" action="{{ route('assessments.destroy', $assessment) }}">
                                        @csrf
                                        @method('delete')
                                        <button class="text-red-500" onclick="event.preventDefault(); confirm('Are you sure?') && this.closest('form').submit();">{{ __('Delete') }}</button>
                                    </form>
                                @else
                                    <span class="text-gray-400">No Permission</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-10 px-4 py-1 text-sm">
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
        const assessmentTypeSelect = $('#assessmentTypeFilter');

        assessmentTypeSelect.on('change', function () {
            filterAssessments();
        });

        function filterAssessments() {
            const selectedAssessmentType = assessmentTypeSelect.val();
            const assessmentRows = $('tbody tr');

            assessmentRows.each(function (index, row) {
                const type = $(row).find('td:eq(1)').text();

                if (selectedAssessmentType === 'all' || selectedAssessmentType === type) {
                    $(row).show();
                } else {
                    $(row).hide();
                }
            });
        }
    });
</script>
