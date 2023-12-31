@section('pageTitle', 'Course Outcomes')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Course Outcomes') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('course_outcomes.map_co_index',$course) }}

        <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <form id="mappingForm" method="POST" action="{{ route('course_outcomes.save_mapping',$course) }}">
                @csrf
                <table class="border-collapse table-auto w-full bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                                CO|PO
                            </th>
                            <!-- Add Program Outcome headings -->
                            @foreach ($program_outcomes as $program_outcome)
                                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-16">
                                    {{ $program_outcome->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through Course Outcomes -->
                        @foreach ($course_outcomes as $course_outcome)
                            @if ($course_outcome->status == 1) <!-- Check if CO status is 1 (active) -->
                                <tr>
                                    <td class="border-b border-gray-100 px-6 py-1 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                        {{ $course_outcome->name }}
                                    </td>
                                    <!-- Add columns for Program Outcomes selection -->
                                    @foreach ($program_outcomes as $program_outcome)
                                        <td class="border-b border-gray-100 px-6 py-1 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                            <select class="mapping-selector border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50"
                                                    data-co="{{ $course_outcome->id }}" data-po="{{ $program_outcome->id }}">
                                                <option value="0">0</option>
                                                <!-- Retrieve and pre-select the strength value from the database -->
                                                @php
                                                    $existingStrength = App\Models\CourseOutcomeProgramOutcome::where('course_outcome_id', $course_outcome->id)
                                                                        ->where('program_outcome_id', $program_outcome->id)
                                                                        ->value('strength');
                                                @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}" {{ $existingStrength === null ? '' : ($existingStrength == $i ? 'selected' : '') }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="order-5 sm:order-6 mr-2 sm:mr-3 m-2">
                    <button type="submit" id="submitButton" class="bg-white hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 border border-gray-300 rounded">
                        <i class="fa fa-save"></i> Submit
                    </button>
                </div>
                
            </form>
        </div>

        <!-- Table for Program Outcome details -->
<div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
    <table class="border-collapse table-auto w-full bg-white table-striped relative">
        <thead>
            <tr class="text-left">
                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                    PO Name
                </th>
                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                    PO Description
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($program_outcomes as $program_outcome)
                <tr>
                    <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                        {{ $program_outcome->name }}
                    </td>
                    <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                        {{ $program_outcome->outcome_description }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Table for Course Outcome details -->
<div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
    <table class="border-collapse table-auto w-full bg-white table-striped relative">
        <thead>
            <tr class="text-left">
                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                    CO Name
                </th>
                <th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-64">
                    CO Description
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($course_outcomes as $course_outcome)
                <tr>
                    <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                        {{ $course_outcome->name }}
                    </td>
                    <td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                        {{ $course_outcome->outcome_description }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    </div>

    <script>
    $(document).ready(function () {
        $('#mappingForm').on('submit', function (event) {
            event.preventDefault(); // Prevent the form from submitting by default

            const validSelects = [];

            // Iterate through all the select elements
            $('.mapping-selector').each(function () {
                const value = $(this).val();
                validSelects.push($(this));
            });

            // If there are valid selections, construct form data and submit the form
            if (validSelects.length > 0) {
                const formData = new FormData();

                // Add valid selections to formData
                validSelects.forEach(select => {
                    formData.append('co_po_pairs[]', `${select.data('co')}_${select.data('po')}_${select.val()}`);
                });

                // Submit the form with valid selections
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        // Handle success response
                        console.log('Form submitted successfully:', response);
                        // Show success flash message
                        displayFlashMessage('Mapping successfully updated', 'green');
                    },
                    error: function (error) {
                        // Handle error response
                        console.error('Error submitting form:', error);
                        // Show error flash message
                        displayFlashMessage('Error updating mapping', 'red');
                    }
                });
            } else {
                // No valid selections, optionally display a message
                console.log('No valid selections found.');
            }
        });

        // Function to display flash messages
        function displayFlashMessage(message, color) {
            const flashMessage = $('<div></div>')
                .text(message)
                .css({
                    'position': 'fixed',
                    'top': '20px',
                    'right': '20px',
                    'padding': '10px',
                    'background-color': color,
                    'color': 'white',
                    'border-radius': '5px',
                    'z-index': '9999'
                });

            // Append flash message to the body and remove after 3 seconds
            $('body').append(flashMessage);
            setTimeout(function () {
                flashMessage.remove();
            }, 3000);
        }
    });
</script>

</x-app-layout>