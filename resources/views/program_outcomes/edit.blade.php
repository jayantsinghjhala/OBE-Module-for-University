@section('pageTitle', 'Edit Program Outcome')

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Program Outcome') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('program_outcomes.edit', $program_outcome) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <input type="hidden" name="department_store" value="{{ $current_department }}">
                <input type="hidden" name="program_store" value="{{ $program_outcome->program_id }}">
                <form method="POST" action="{{ route('program_outcomes.update', $program_outcome) }}">
                    @csrf
                    @method('patch')

                    <div class="form-control w-full max-w-xl">
                        <label class="label">
                            <span class="label-text text-neutral font-bold">School</span>
                        </label>
                        <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="school_id" id="school_id">
                            <option disabled selected>Select School</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ (old('school_id', $current_school) == $school->id) ? 'selected' : '' }}>School of {{ ucfirst($school->name) }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('school_id')" class="mt-2" />
                    </div>

                    <div class="form-control w-full max-w-xl">
                        <label class="label">
                            <span class="label-text text-neutral font-bold">Department</span>
                        </label>
                        <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="department_id" id="department_id" disabled>
                            <option disabled selected>Select Department</option>
                        </select>
                        <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                    </div>

                    <div class="form-control w-full max-w-xl">
                        <label class="label">
                            <span class="label-text text-neutral font-bold">Program</span>
                        </label>
                        <select class="select text-neutral input-bordered bg-white w-full max-w-xl" name="program_id" id="program_id" disabled>
                            <option disabled selected>Select Program</option>
                        </select>
                        <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Program Outcome Name</span>
                        </label>
                        <select name="name" class="select select-bordered w-full max-w-xs">
                            @for ($i = 1; $i <= 20; $i++)
                                <option value="PO{{ $i }}" {{ (old('name', $program_outcome->name) === "PO$i") ? 'selected' : '' }}>PO{{ $i }}</option>
                            @endfor
                        </select>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Outcome Description</span>
                        </label>
                        <textarea name="outcome_description" placeholder="Enter Outcome Description" class="textarea textarea-bordered w-full max-w-xs">{{ old('outcome_description', $program_outcome->outcome_description) }}</textarea>
                        <x-input-error :messages="$errors->get('outcome_description')" class="mt-2" />
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <a href="{{ route('program_outcomes.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const schoolSelect = $('#school_id');
            const departmentSelect = $('#department_id');
            const programSelect = $('#program_id');

            function fetchDepartments(selectedSchoolId) {
                departmentSelect.prop('disabled', true);
                programSelect.prop('disabled', true);

                if (selectedSchoolId !== '') {
                    $.ajax({
                        url: `/get_departments/${selectedSchoolId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == 1) {
                                departmentSelect.empty();
                                departmentSelect.append(`<option value="" selected disabled>Select Department</option>`);

                                $.each(res.departments, function (index, department) {
                                    departmentSelect.append($('<option>', {
                                        value: department.id,
                                        text: department.name
                                    }));
                                });

                                const departmentStoreValue = $("input[name='department_store']").val();
                                if (departmentStoreValue) {
                                    departmentSelect.val(departmentStoreValue);
                                    $("input[name='department_store']").val('');
                                }

                                departmentSelect.trigger('change');
                            }
                        },
                        complete: function () {
                            departmentSelect.prop('disabled', false);
                        }
                    });
                }
            }

            function fetchPrograms(selectedDepartmentId) {
                programSelect.prop('disabled', true);

                if (selectedDepartmentId !== '') {
                    $.ajax({
                        url: `/get_programs/${selectedDepartmentId}`,
                        method: 'GET',
                        dataType: 'json',
                        success: function (res) {
                            if (res.status == 1) {
                                programSelect.empty();
                                programSelect.append(`<option value="" selected disabled>Select Program</option>`);

                                $.each(res.programs, function (index, program) {
                                    programSelect.append($('<option>', {
                                        value: program.id,
                                        text: program.name
                                    }));
                                });

                                const programStoreValue = $("input[name='program_store']").val();
                                if (programStoreValue) {
                                    programSelect.val(programStoreValue);
                                    $("input[name='program_store']").val('');
                                }
                            }
                        },
                        complete: function () {
                            programSelect.prop('disabled', false);
                        }
                    });
                }
            }

            schoolSelect.on('change', function () {
                const selectedSchoolId = schoolSelect.val();
                fetchDepartments(selectedSchoolId);
            });

            departmentSelect.on('change', function () {
                const selectedDepartmentId = departmentSelect.val();
                fetchPrograms(selectedDepartmentId);
            });

            const selectedSchoolId = schoolSelect.val();
            if (selectedSchoolId) {
                fetchDepartments(selectedSchoolId);
            }
        });
    </script>
</x-app-layout>
