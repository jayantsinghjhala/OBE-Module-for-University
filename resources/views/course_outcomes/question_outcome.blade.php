@section('pageTitle', 'Assign Questions Marks')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Questions Marks') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-8">
        {{ Breadcrumbs::render('course_outcomes.question_outcome', $course) }}
        <div class="my-4 flex justify-end pr-4">
            <label for="assessmentTypeFilter" class="mr-2">Select Course Assessment:</label>
            <select id="assessmentTypeFilter" name="assessmentTypeFilter"
                class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                <option value="all" selected disabled>Select</option>
                <!-- Loop through course_assessments to generate options -->
                @foreach($course_assessments as $course_assessment)
                <option value="{{ $course_assessment->id }}">{{ $course_assessment->assessment_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Display table for assessment details -->
        <form id="assessmentDetailsForm"
            action="{{ route('course_outcomes.question_outcome_store', ['course_assessment_id' => '__ASSESSMENT_ID__']) }}"
            method="POST">
            @csrf
            <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead>
                        <tr class="text-left">
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-1/3">
                                Question Number
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-1/3">
                                Course Outcome
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-1/3">
                                Marks
                            </th>
                        </tr>
                    </thead>
                    <tbody id="assessmentDetailsBody">
                        <!-- Dynamically generated rows will be inserted here -->
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">No records yet</td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="order-5 sm:order-6 mr-2 sm:mr-3 m-2">
                <button type="submit" id="submitButton"
                    class="bg-white hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 border border-gray-300 rounded">
                    <i class="fa fa-save"></i> Submit
                </button>
            </div>
        </form>

        <script>
            $(document).ready(function () {
                var courseAssessments = @json($course_assessments);
                var courseOutcomes = @json($course_outcomes);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var selectedAssessmentId;

                function populateAssessmentDetails(selectedAssessmentId) {
                    $.ajax({
                        url: "{{ route('course_outcomes.get_assessment_details') }}",
                        method: "GET",
                        data: {
                            course_assessment_id: selectedAssessmentId
                        },
                        success: function (response) {
                            console.log(response.assessment_details.length);

                            if (response.assessment_details.length > 0) {
                                $('#assessmentDetailsBody').empty(); // Clear existing rows
                                $.each(response.assessment_details, function (index, detail) {
                                    var questionNumber = 'Q' + (index + 1);
                                    var courseOutcomeId = detail.course_outcome_id;
                                    var marks = detail.marks;
                                    console.log(courseOutcomeId);
                                    var options = ''; // Variable to store the generated options
                                    $.each(@json($course_outcomes), function (_, outcome) {
                                        // Check if the outcome id matches the courseOutcomeId and set 'selected' accordingly
                                        var selected = outcome.id == courseOutcomeId ? 'selected' : '';
                                        options += `<option value="${outcome.id}" ${selected}>${outcome.name}</option>`;
                                    });
                                    $('#assessmentDetailsBody').append(`
                                        <tr data-id="${detail.id}">
                                            <td class="border-b border-gray-100 px-7 py-1 text-gray-500 font-large tracking-wide uppercase truncate">${questionNumber}</td>
                                            <td class="border-b border-gray-100 px-7 py-1 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                                <select name="course_outcome_${index + 1}" class="mapping-selector border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                                                    <!-- Loop through course outcomes to generate options -->
                                                    <option value="" selected disabled>Select CO</option>
                                                    ${options} <!-- Insert the generated options here -->
                                                </select>
                                                <x-input-error :messages="$errors->get('course_outcome_${index + 1}')" class="mt-2" />
                                            </td>
                                            <td class="border-b border-gray-100 px-7 py-1 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                                <input type="text" name="marks_${index + 1}" placeholder="Marks" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50 px-2 py-1" maxlength="6" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" value="${marks}">
                                                <x-input-error :messages="$errors->get('marks_${index + 1}')" class="mt-2" />
                                            </td>
                                        </tr>
                                    `);
                                });
                            } else {
                                var selectedAssessment = courseAssessments.find(function (assessment) {
                                    return assessment.id == selectedAssessmentId;
                                });

                                if (selectedAssessment) {
                                    $('#assessmentDetailsBody').empty(); // Clear existing rows
                                    for (var i = 1; i <= selectedAssessment.num_questions; i++) {
                                        $('#assessmentDetailsBody').append(`
                                    <tr data-id="-1">
                                        <td class="border-b border-gray-100 px-7 py-1 text-gray-500 font-large tracking-wide uppercase truncate">
                                            Q${i}
                                        </td>
                                        <td class="border-b border-gray-100 px-7 py-1 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                            <select name="course_outcome_${i}" class="mapping-selector border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50">
                                                <!-- Loop through course outcomes to generate options -->
                                                <option value="" selected disabled>Select CO</option>
                                                @foreach($course_outcomes as $outcome)
                                                    <option value="{{ $outcome->id }}">{{ $outcome->name }}</option>
                                                @endforeach
                                            </select>
                                            <x-input-error :messages="$errors->get('course_outcome_${i}')" class="mt-2" />
                                        </td>
                                        <td class="border-b border-gray-100 px-7 py-1 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                            <input type="text" name="marks_${i}" placeholder="Marks" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50 px-2 py-1" maxlength="6" oninput="this.value = this.value.replace(/[^0-9.]/g, '');">
                                            <x-input-error :messages="$errors->get('marks_${i}')" class="mt-2" />
                                        </td>
                                    </tr>
                                `);
                                    }
                                }
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                }

                $('#assessmentTypeFilter').on('change', function () {
                    selectedAssessmentId = $(this).val();
                    var formAction = "{{ route('course_outcomes.question_outcome_store', ['course_assessment_id' => '__ASSESSMENT_ID__']) }}";
                    formAction = formAction.replace('__ASSESSMENT_ID__', selectedAssessmentId);
                    $('#assessmentDetailsForm').attr('action', formAction);

                    populateAssessmentDetails(selectedAssessmentId);
                });

                // Populate on initial page load if a value is pre-selected
                var initialSelectedId = $('#assessmentTypeFilter').val();
                if (initialSelectedId) {
                    populateAssessmentDetails(initialSelectedId);
                }

                $('#assessmentDetailsForm').on('submit', function (event) {
                    event.preventDefault(); // Prevent the default form submission
                    var allValid = true;
                    var marksCount=0;
                    // Loop through each input/select field and perform validation
                    $('#assessmentDetailsBody tr').each(function () {
                        var marksInput = $(this).find('input[name^="marks_"]');
                        var outcomeSelect = $(this).find('select[name^="course_outcome_"]');
                        
                        // Retrieve the value of the marks input field
                        var marksValue = marksInput.val().trim();
                        
                        // Check if the value is not empty and is a valid number
                        if (marksValue !== '' && !isNaN(parseFloat(marksValue))) {
                            marksCount += parseFloat(marksValue);
                        }

                        // Check if marks are filled and course outcome is selected
                        if (marksValue === '' || outcomeSelect.val() === null) {
                            allValid = false;
                            return false; // Break the loop if any field is empty
                        }
                    });

                    console.log(marksCount); 
                    var selectedAssessment = courseAssessments.find(function (assessment) {
                                    return assessment.id == selectedAssessmentId;
                                });
                    if (allValid && (marksCount<=selectedAssessment.maximum_marks)) {
                        // If all fields are filled, send data via AJAX
                        var assessmentTypeFilter = $('#assessmentTypeFilter').val();

                        // Prepare data for AJAX request
                        var assessmentDetails = [];
                        $('#assessmentDetailsBody tr').each(function () {
                            var questionNumber = $(this).find('td:eq(0)').text().trim().substring(1); // Extract question number from table cell
                            var courseOutcomeId = $(this).find('select[name^="course_outcome_"]').val();
                            var marks = $(this).find('input[name^="marks_"]').val();
                            var id=$(this).data("id");
                            assessmentDetails.push({
                                question_number: questionNumber,
                                course_outcome_id: courseOutcomeId,
                                marks: marks,
                                id: id
                            });
                        });

                        // Send data via AJAX
                        $.ajax({
                            url: "{{ route('course_outcomes.question_outcome_store', ['course_assessment_id' => ':course_assessment_id']) }}"
                                .replace(':course_assessment_id', assessmentTypeFilter),
                            method: "POST",
                            data: {
                                assessment_details: assessmentDetails
                            },
                            success: function (response) {
                                displayFlashMessage("Successfully Updated Assessment Details", "green");
                                $('#assessmentDetailsBody tr').each(function (index) {
                                    var assessmentDetailId = response.assessment_details[index].id;
                                    $(this).data("id", assessmentDetailId);
                                });
                                console.log(response);
                            },
                            error: function (xhr, status, error) {
                                displayFlashMessage("Unable to Update Assessment Details", "red");
                                // Handle error response
                                console.error(xhr.responseText);
                            }
                        });
                    }else if(marksCount>selectedAssessment.maximum_marks){
                        displayFlashMessage("Total marks should be less than or equal to "+selectedAssessment.maximum_marks, "red");
                    } else {
                        // If any field is empty, display an error message or perform an action
                        displayFlashMessage("Please fill all fields for each question", "red");
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
    </div>
</x-app-layout>



