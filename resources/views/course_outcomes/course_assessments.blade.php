@section('pageTitle', 'Select Assessments')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Select Assessments') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-8">
        {{ Breadcrumbs::render('course_outcomes.course_assessments',$course) }}
        <!-- <div class="my-4 flex justify-end pr-4">
            <label for="assessmentTypeFilter" class="mr-2">Assessment Type Filter:</label>
            <select id="assessmentTypeFilter" name="assessmentTypeFilter"
                class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                <option value="all">All Types</option>
                <option value="CIA">CIA</option>
                <option value="ETA">ETA</option>
            </select>
        </div> -->
        <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <thead>
                    <tr class="text-left">
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Name
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Type
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Created Date
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assessments as $assessment)
                    <tr>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $assessment->assessment_name }}
                        </td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $assessment->assessment_type }}
                        </td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            @php
                            $createdDate = \Carbon\Carbon::parse($assessment->created_at);
                            echo $createdDate->format('d F Y');
                            @endphp
                        </td>
                        <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                            <input type="checkbox" class="assessment-checkbox" data-id="{{ $assessment->id }}">
                            <input type="hidden" class="course_assessment_id" value="">
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

    <!-- Secondary Table -->
    <div class="max-w-7xl mx-auto px-8">
        <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
            <table id="selectedAssessmentsTable"
                class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                <!-- Table Headings -->
                <thead>
                    <tr class="text-left">
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Name
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Type
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Number of Questions
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Maximum Marks
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Assessment Date
                        </th>
                        <th
                            class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                            Action
                        </th>
                    </tr>
                </thead>
                <!-- Table Body for Selected Assessments -->
                <tbody>
                    <!-- Rows for selected assessments will be dynamically added here using jQuery -->
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script>
    $(document).ready(function () {
        const course_id = {{ $course-> id
    }}

        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

    // Event listener for changing the checkbox
    $('body').on('change', '.assessment-checkbox', function () {
        const assessment_id = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const assessmentRow = $(this).closest('tr');
        const courseAssessmentIdInput = assessmentRow.find('.course_assessment_id');

        if (isChecked) {
            $.post(`/course_assessments`, { course_id: course_id, assessment_id: assessment_id })
                .done(function (response) {
                    // Create a new form within a table row for each assessment
                    const newRow = `
                        <tr data-id="${response.course_assessment.id}">
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">${response.assessment_name}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">${response.assessment_type}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <input type="text" class="numQuestions" name="numQuestions" placeholder="Number of Questions">
                            </td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <input type="text" class="maxMarks" name="maxMarks" placeholder="Maximum Marks">
                            </td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <input type="date" class="assessmentDate" name="assessmentDate">
                            </td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <button class="btn-secondary p-1 rounded saveAssessmentDetails" type="submit">Save</button>
                            </td>
                        </tr>`;
                    $('#selectedAssessmentsTable tbody').append(newRow);
                    courseAssessmentIdInput.val(response.course_assessment.id);
                    displayFlashMessage('Course Assessment Successfully Added', 'green');

                })
                .fail(function (error) {
                    console.error(error);
                    $(this).prop('checked', false);
                    displayFlashMessage('Unable to Add Course Assessment', 'red');

                });
        } else {
            // Delete the corresponding course assessment if checkbox is unchecked
            const course_assessment_id = courseAssessmentIdInput.val();

            if (confirm("Are you sure you want to delete this assessment from this Course?")) {
                $.ajax({
                    url: `/course_assessments/${course_assessment_id}`,
                    type: 'DELETE',
                    success: function (result) {
                        // Remove the row from the selectedAssessmentsTable
                        $(`#selectedAssessmentsTable tbody tr[data-id="${course_assessment_id}"]`).remove();
                        displayFlashMessage('Course Assessment Successfully Deleted', 'green');

                    },
                    error: function (error) {
                        console.error(error);
                        $(this).prop('checked', true);
                        displayFlashMessage('Unable to Delete Course Assessment', 'red');

                    }
                });
            } else {
                // Uncheck the checkbox if deletion is canceled
                $(this).prop('checked', true);
            }
        }
    });

    // Event listener for submitting assessment details
    $(document).on('click', '.saveAssessmentDetails', function (e) {
        e.preventDefault();
        const course_assessment_id = $(this).closest('tr').data('id');
        const numQuestions = $(this).closest('tr').find('.numQuestions').val();
        const maxMarks = $(this).closest('tr').find('.maxMarks').val();
        console.log(maxMarks);
        const assessmentDate = $(this).closest('tr').find('.assessmentDate').val();

        $.ajax({
            url: `/course_assessments/${course_assessment_id}`,
            type: 'PUT',
            data: {
                num_questions: numQuestions,
                maximum_marks: maxMarks,
                assessment_date: assessmentDate
            },
            success: function (result) {
                // Update the assessment details in the table
                $(`#selectedAssessmentsTable tbody tr[data-id="${course_assessment_id}"] .numQuestions`).val(result.data.num_questions);
                $(`#selectedAssessmentsTable tbody tr[data-id="${course_assessment_id}"] .maxMarks`).val(result.data.maximum_marks);
                $(`#selectedAssessmentsTable tbody tr[data-id="${course_assessment_id}"] .assessmentDate`).val(result.data.assessment_date);
                displayFlashMessage('Course Assessment Successfully Updated', 'green');
            },
            error: function (error) {
                console.error(error);
                displayFlashMessage('Unable to Update Course Assessment', 'red');

            }
        });
    });
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
    // Function to populate tables
    function populateTables() {
        // Auto-check checkboxes in the first table based on existing assessments
        $('tbody .assessment-checkbox').each(function () {
            const assessment_id = $(this).data('id');
            const checkbox = $(this);
            $.get(`/course_assessments/${assessment_id}/${course_id}`)
                .done(function (response) {
                    if (response.exists) {
                        checkbox.prop('checked', true);
                    }
                })
                .fail(function (error) {
                    console.error(error);
                });
        });

        // Fetch and populate the second table with available assessment data
        $.get(`/course_assessments/course/assessment_data/${course_id}`)
            .done(function (response) {
                $.each(response, function (index, data) {
                    const newRow = `
                        <tr data-id="${data.id}">
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">${data.assessment_name}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">${data.assessment_type}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <input type="text" class="numQuestions" name="numQuestions" placeholder="Number of Questions" value="${data.num_questions || ''}">
                            </td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <input type="text" class="maxMarks" name="maxMarks" placeholder="Maximum Marks" value="${data.maximum_marks || ''}">
                            </td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <input type="date" class="assessmentDate" name="assessmentDate" value="${data.assessment_date || ''}">
                            </td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">
                                <button class="btn-secondary p-1 rounded saveAssessmentDetails" type="submit">Save</button>
                            </td>
                        </tr>`;
                    $('#selectedAssessmentsTable tbody').append(newRow);
                });
            })
            .fail(function (error) {
                console.error(error);
            });
    }

    // Call the function to populate tables
    populateTables();
    const assessmentTypeSelect = $('#assessmentTypeFilter');
    // Handle change event for assessment type filter
    assessmentTypeSelect.on('change', function () {
        filterAssessments();
    });

    // Filter assessments based on selected assessment type
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