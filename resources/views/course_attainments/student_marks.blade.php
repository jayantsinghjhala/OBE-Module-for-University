@section('pageTitle', 'Assign Student Marks')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Student Marks') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-8">
        {{ Breadcrumbs::render('course_outcomes.student_marks', $course) }}
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
        <form id="assessmentDetailsForm" action="{{ route('course_outcomes.student_marks_store') }}" style="display:none;">
            @csrf
            <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full whitespace-no-wrap bg-white table-striped relative">
                    <thead class="assessmentDetailsHead">
                        <tr class="text-left">
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                Question Number
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                Q1
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                Q2
                            </th>
                        </tr>
                        <tr class="text-left">
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                Question Marks
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                1
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                2
                            </th>
                        </tr>
                        <tr class="text-left">
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                Student | CO
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                CO1
                            </th>
                            <th
                                class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">
                                CO2
                            </th>
                        </tr>

                    </thead>
                    <tbody id="assessmentDetailsBody">
                        <!-- Dynamically generated rows will be inserted here -->
                        @foreach($students as $student)
                        <tr>
                            <td
                                class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                {{ $student->enrollment_number }}
                            </td>
                            <td
                                class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                {{ $student->student_name }}
                            </td>
                            <td
                                class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                <input type="text" name="marks_{{$student->enrollment_number}}" placeholder="Marks" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50 px-1 py-1" maxlength="6" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" >
                            </td>
                            <td
                                class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                <input type="text" name="marks_{{$student->enrollment_number}}" placeholder="Marks" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50 px-1 py-1" maxlength="6" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" >
                            </td>
                            <td
                                class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">
                                <input type="text" name="marks_{{$student->enrollment_number}}" placeholder="Marks" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50 px-1 py-1" maxlength="6" oninput="this.value = this.value.replace(/[^0-9.]/g, '');" >
                            </td>
                
                        </tr>
                        @endforeach
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
    </div>

    <script>
        function checkMarks(inputElement, maxMarks) {
            var enteredValue = parseFloat(inputElement.value);

            if (!isNaN(enteredValue) && enteredValue > maxMarks) {
                // You can also reset the value if desired
                displayFlashMessage('Entered marks (' + enteredValue + ') is greater than allowed marks (' + maxMarks + ')', "red");
                inputElement.value = ''; // This line will clear the entered value
            }
        }
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
        $(document).ready(function () {
            var courseAssessments = @json($course_assessments);
            var students = @json($students);
            var assessment_detail=[];
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var selectedAssessmentId;
            
            function populateHeader(selectedAssessmentId) {
                $.ajax({
                    url: "{{ route('course_outcomes.get_assessment_details') }}",
                    method: "GET",
                    data: {
                        course_assessment_id: selectedAssessmentId
                    },
                    success: function (response) {

                        if (response.assessment_details.length > 0) {
                            var questionHeaders = '<tr class="text-left"><th  class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate" rowspan="3">Enrollment</th><th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">Question Number</th>';
                            var marksHeaders = '<tr class="text-left"><th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">Question Marks</th>';
                            var courseOutcomeHeaders = '<tr class="text-left"><th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">Student | CO</th>';

                            $.each(response.assessment_details, function (index, detail) {
                                var questionNumber = 'Q' + (index + 1);
                                var courseOutcomeName = detail.course_outcome_name;
                                var marks = detail.marks;
                                assessment_detail.push(detail);
                                questionHeaders += '<th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">' + questionNumber + '</th>';
                                marksHeaders += '<th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">' + marks + '</th>';
                                courseOutcomeHeaders += '<th class="bg-gray-50 sticky top-0 border-b border-gray-100 px-7 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">' + courseOutcomeName + '</th>';
                            });

                            questionHeaders += '</tr>';
                            marksHeaders += '</tr>';
                            courseOutcomeHeaders += '</tr>';

                            // $('#assessmentDetailsBody').empty(); // Clear existing rows
                            $('.assessmentDetailsHead').empty(); // Clear existing headers

                            $('.assessmentDetailsHead').append(questionHeaders);
                            $('.assessmentDetailsHead').append(marksHeaders);
                            $('.assessmentDetailsHead').append(courseOutcomeHeaders);
                            // var assessment_detail_id = assessment_detail_ids.split(' ');
                            // assessment_detail_id.pop();
                            populateBody(assessment_detail, students); 
                        }
                    }
                });
            }

            function populateBody(assessmentDetails, students) {
                var tbody = $('#assessmentDetailsBody');
                tbody.empty(); // Clear existing rows

                // AJAX request to fetch existing marks
                $.ajax({
                    url: "{{ route('course_outcomes.fetch_existing_marks') }}", // Replace with your route to fetch existing marks
                    method: "GET",
                    data: {
                        assessmentDetails: assessmentDetails,
                        students: students
                    },
                    success: function (response) {
                        $.each(students, function (studentIndex, student) {
                            var newRow = $('<tr></tr>');

                            // Adding columns for student data (enrollment number and student name)
                            newRow.append('<td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">' + student.enrollment_number + '</td>');
                            newRow.append('<td class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">' + student.student_name + '</td>');

                            // Adding columns for marks input fields based on assessment details
                            $.each(assessmentDetails, function (index, detail) {
                                var existingMark = response[student.id][detail.id]; // Fetch existing mark for the student and assessment detail
                                console.log(existingMark);
                                var inputField = '<td class="marks_division" data-id="' + (existingMark.length > 0 ? existingMark[0].id : -1) + '" data-assessment_detail_id="' + detail.id + '" data-student_id="' + student.id + '" class="border-b border-gray-100 px-6 py-3 text-gray-500 font-medium tracking-wide uppercase text-xs truncate">' +
                                '<input type="text" name="marks_' + detail.id + '" placeholder="Marks" class="border-gray-300 rounded-md focus:ring focus:ring-blue-100 focus:ring-opacity-50 px-1 py-1" maxlength="6" onchange="checkMarks(this, ' + detail.marks + ')" oninput="this.value = this.value.replace(/[^0-9.]/g, \'\');" value="' + (existingMark.length > 0 ? existingMark[0].obtained_marks : '') + '">' +
                                '</td>';

                                newRow.append(inputField);
                            });

                            tbody.append(newRow);
                        });

                        $("#assessmentDetailsForm").show();
                    },
                    error: function (xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText);
                    }
                });
            }


            
            $('#assessmentTypeFilter').on('change', function () {
                selectedAssessmentId = $(this).val();
                
                populateHeader(selectedAssessmentId);
                
            });



            $('#assessmentDetailsForm').on('submit', function (event) {
                event.preventDefault(); // Prevent the default form submission
                var allValid = true;
                // Loop through each input/select field and perform validation
                $('#assessmentDetailsBody tr').each(function () {
                    var marksInput = $(this).find('input[name^="marks_"]');

                    marksInput.each(function () {
                        var marksValue = $(this).val().trim();
                        // Check if marks are filled
                        if (marksValue === '') {
                            allValid = false;
                            return false; // Break the loop if any field is empty
                        }
                    });
                });


                if (allValid) {
                    var assessmentDetails = [];
                    $('#assessmentDetailsBody tr').each(function () {
                        var marksInput = $(this).find('input[name^="marks_"]');

                        marksInput.each(function () {
                            var assessmentDetailId = $(this).parent(".marks_division").data('assessment_detail_id');
                            var studentId = $(this).parent(".marks_division").data('student_id');
                            var marks = $(this).val();
                            var id=$(this).parent(".marks_division").data("id");
                            
                            assessmentDetails.push({
                                assessment_detail_id: assessmentDetailId,
                                student_id: studentId,
                                obtained_marks: marks,
                                id: id
                            });
                        });
                    });
                    console.log(assessmentDetails);

                    // Send data via AJAX
                    $.ajax({
                        url: "{{ route('course_outcomes.student_marks_store') }}",
                        method: "POST",
                        data: {
                            student_marks: assessmentDetails
                        },
                        success: function (response) {
                            // Handle success response
                            var index_ct=0;
                            displayFlashMessage("Successfully Updated Assessment Details", "green");
                            $('#assessmentDetailsBody tr').each(function () {
                                var marksInput = $(this).find('input[name^="marks_"]');

                                marksInput.each(function () {
                                    var studentMarksId = response.marks_details[index_ct].id;
                                    $(this).parent(".marks_division").data("id", studentMarksId);
                                    index_ct+=1;
                                });
                            });
                        },
                        error: function (xhr, status, error) {
                            displayFlashMessage("Unable to Update Student Marks", "red");
                            // Handle error response
                            console.error(xhr.responseText);
                        }
                    });
                }else {
                    // If any field is empty, display an error message or perform an action
                    displayFlashMessage("Please fill all fields for each Student", "red");
                }//this was second


                // var selectedAssessment = courseAssessments.find(function (assessment) {
                //     return assessment.id == selectedAssessmentId;
                // });
                
            });

            
        });
    </script>
    </div>
</x-app-layout>