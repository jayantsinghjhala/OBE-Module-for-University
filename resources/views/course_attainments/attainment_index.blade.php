<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Course Attainment Details') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Display Course Attainment Details -->
        <div class="mt-8">

            <div class="overflow-x-auto">
                <!-- Table for CIA Assessments -->
                <table class="border-collapse w-full bg-white table-auto rounded-lg shadow-md">
                    <!-- Table Headings for CIA Assessments -->
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">Assessment Type (CIA) |
                                CO</th>
                            @foreach ($courseAttainment['coThresholdPercentagesCIA'] as $coId => $percentage)
                            @php
                            $courseOutcome = \App\Models\CourseOutcome::find($coId);
                            @endphp
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">
                                @isset($courseOutcome)
                                {{ $courseOutcome->name }}
                                @else
                                CO{{ $coId }}
                                @endisset
                            </th>
                            @endforeach
                        </tr>

                    </thead>
                    <!-- Table Body for CIA Assessments -->
                    <tbody>
                        <!-- Rows for CIA Assessments -->
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Number of Students above threshold for CIA
                            </td>
                            @foreach ($courseAttainment['coStudentsAboveThresholdCIA'] as $coId => $count)
                            <td class="px-6 py-4 border-b border-gray-200">{{ $count }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Percentage of Students above threshold for
                                CIA</td>
                            @foreach ($courseAttainment['coThresholdPercentagesCIA'] as $coId => $percentage)
                            <td class="px-6 py-4 border-b border-gray-200">{{ number_format($percentage, 2) }}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Attainment for CIA</td>
                            @foreach ($courseAttainment['overallCIAAttainment'] as $coId => $attainment)
                            <td class="px-6 py-4 border-b border-gray-200">{{ number_format($attainment, 2) }}</td>
                            @endforeach
                        </tr>
                        <!-- Repeat other rows for CIA Assessments -->
                    </tbody>
                </table>
                <!-- Table for ETA Assessments -->
                <table class="border-collapse w-full mt-8 bg-white table-auto rounded-lg shadow-md">
                    <!-- Table Headings for ETA Assessments -->
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">Assessment Type (ETA) |
                                CO</th>
                            @foreach ($courseAttainment['coThresholdPercentagesCIA'] as $coId => $percentage)
                            @php
                            $courseOutcome = \App\Models\CourseOutcome::find($coId);
                            @endphp
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">
                                @isset($courseOutcome)
                                {{ $courseOutcome->name }}
                                @else
                                CO{{ $coId }}
                                @endisset
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <!-- Table Body for ETA Assessments -->
                    <tbody>
                        <!-- Rows for ETA Assessments -->
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Number of Students above threshold for ETA
                            </td>
                            @foreach ($courseAttainment['coStudentsAboveThresholdETA'] as $coId => $count)
                            <td class="px-6 py-4 border-b border-gray-200">{{ $count }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Percentage of Students above threshold for
                                ETA</td>
                            @foreach ($courseAttainment['coThresholdPercentagesETA'] as $coId => $percentage)
                            <td class="px-6 py-4 border-b border-gray-200">{{ number_format($percentage, 2) }}%</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Attainment for ETA</td>
                            @foreach ($courseAttainment['overallETAAttainment'] as $coId => $attainment)
                            <td class="px-6 py-4 border-b border-gray-200">{{ number_format($attainment, 2) }}</td>
                            @endforeach
                        </tr>
                        <!-- Repeat other rows for ETA Assessments -->
                    </tbody>
                </table>


                <!-- Table for Additional Information -->
                <table class="border-collapse w-full mt-8 bg-white table-auto rounded-lg shadow-md">
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">Course Outcome Attainment
                                Table
                            </th>
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase"></th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200 font-bold">Attainment through CIA</td>
                            <td class="px-6 py-4 border-b border-gray-200">{{
                                number_format($courseAttainment['overallCIA'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200 font-bold">Attainment through ETA</td>
                            <td class="px-6 py-4 border-b border-gray-200">{{
                                number_format($courseAttainment['overallETA'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200 font-bold">Weightage given to CIA (40%):</td>
                            <td class="px-6 py-4 border-b border-gray-200">{{
                                number_format($courseAttainment['weightageCIAAttainment'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200 font-bold">Weightage given to ETA (60%):</td>
                            <td class="px-6 py-4 border-b border-gray-200">{{
                                number_format($courseAttainment['weightageETAAttainment'], 2) }}</td>
                        </tr>
                        <tr>
                            <td class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">Final attainment level of
                                the course (by
                                Direct Assessment):</td>
                            <td class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">{{
                                number_format($courseAttainment['overallAttainment'], 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <!-- Table for PO and CO Mapping -->
                <!-- Add the table at the end -->


                <table class="border-collapse w-full mt-8 bg-white table-auto rounded-lg shadow-md">
                    <thead>
                        <tr class="text-left">
                            <th class="bg-gray-500 px-6 py-3 text-gray-500 font-bold uppercase">
                                Program Outcome Attainment Table
                            </th>
                            <!-- Replace 'PO1', 'PO2', etc. with actual PO names dynamically -->

                            <th class="bg-gray-500 px-6 py-3 text-gray-500 font-bold uppercase">

                            </th>

                        </tr>
                        <tr class="text-left">
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">
                                CO | PO
                            </th>
                            <!-- Replace 'PO1', 'PO2', etc. with actual PO names dynamically -->
                            @foreach ($courseAttainment['table_data']['programOutcomes'] as $po)
                            <th class="bg-gray-50 px-6 py-3 text-gray-500 font-bold uppercase">
                                {{ $po->name }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through COs and their related data -->
                        @foreach ($courseAttainment['table_data']['courseOutcomes'] as $co)
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">{{ $co->name }}</td>
                            <!-- Access each PO attainment based on CO -->
                            @foreach ($courseAttainment['table_data']['programOutcomes'] as $po)
                            <td class="px-6 py-4 border-b border-gray-200">
                                <?php
                            // Get the strength value for the current CO and PO
                            $strength = $co->program_outcomes()
                                            ->wherePivot('program_outcome_id', $po->id)
                                            ->value('strength');
                            echo ($strength !== null) ? number_format($strength, 2) : '0.00';
                        ?>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach

                        <!-- Add rows for 'Average Mapping' and 'PO Attainment Level' -->
                        <!-- Populate with dynamic data -->
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">Average Mapping(M)</td>
                            @foreach ($courseAttainment['table_data']['avg_mapping'] as $mapping)
                            <td class="px-6 py-4 border-b border-gray-200">{{ $mapping }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="px-6 py-4 border-b border-gray-200">PO Attainment Level</td>
                            @foreach ($courseAttainment['table_data']['programOutcomes'] as $po)
                            <td class="px-6 py-4 border-b border-gray-200">
                                {{ isset($courseAttainment['table_data']['avg_mapping'][$po->id]) ?
                                number_format($courseAttainment['table_data']['avg_mapping'][$po->id], 2) : '' }}
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>




            </div>
            <!-- Add more tables or sections if needed -->
        </div>
    </div>

    <!-- ...Remaining JavaScript code... -->
</x-app-layout>