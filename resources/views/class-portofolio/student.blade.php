@section('pageTitle', 'Student Portofolio'. ' - ' . $cc->name)
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-start gap-4">
            <x-back-link />
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Student Portofolio') }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('class-portofolio.student', $cc) }}
        <div class="pb-8">
            <div class="mb-5 overflow-x-auto bg-white rounded-lg shadow overflow-y-auto relative">
                <table class="border-collapse table-auto w-full bg-white table-striped relative">
                    <tr>
                        <th rowspan="2" class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-6">No</th>
                        <th rowspan="2" class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate">Name</th>
                        <th rowspan="2" class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-32">Student ID</th>
                        <th colspan="{{ $llos->count() }}" class="text-center bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-2">LLO Achievement (%)</th>
                        <th rowspan="2" class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-32">{{ __('Grade') }}</th>
                        <th rowspan="2" class="bg-gray-50 sticky top-0 border-b border-gray-100 px-6 py-3 text-gray-500 font-bold tracking-wider uppercase text-xs truncate w-32">{{ __('Letter') }}</th>
                    </tr>
                    <tr>
                        @foreach ($llos as $llo)
                            <th class="bg-gray-50 text-center font-semibold text-gray-500">
                                <div class="tooltip tooltip-bottom cursor-pointer"
                                     data-tip="{{ $llo->description }}">
                                    {{ $llo->code }}
                                </div>
                            </th>
                        @endforeach
                    </tr>

                    <?php $i = 1; ?>
                    @foreach ($userData as $data)
                        <tr>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $i }}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $data['name'] }}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100">{{ $data['nim'] }}</td>
                            @foreach ($data['cpmk'] as $cpmk)
                            <td class="text-center text-gray-600 px-6 py-3 border-t border-gray-100">
                                {{ round($cpmk['point']/$cpmk['maxPoint']*100,2) }}
                            </td>
                            @endforeach
                                <?php
                                $totalPoint = $data['cpmk']->sum('point');

                                if ($totalPoint > 80) {
                                    $pointLetter = 'A';
                                } elseif ($totalPoint > 75) {
                                    $pointLetter = 'B+';
                                } elseif ($totalPoint > 69) {
                                    $pointLetter = 'B';
                                } elseif ($totalPoint > 60) {
                                    $pointLetter = 'C+';
                                } elseif ($totalPoint > 55) {
                                    $pointLetter = 'C';
                                } elseif ($totalPoint > 50) {
                                    $pointLetter = 'D+';
                                } elseif ($totalPoint > 44) {
                                    $pointLetter = 'D';
                                } else {
                                    $pointLetter = 'E';
                                }
                                ?>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100 text-center">{{ $totalPoint }}</td>
                            <td class="text-gray-600 px-6 py-3 border-t border-gray-100 text-center">{{ $pointLetter }}</td>
                        </tr>
                            <?php $i++; ?>
                        @endforeach
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
