@section('pageTitle', "Programs")
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Programs') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{ Breadcrumbs::render('programs.edit',$program) }}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <input type="hidden" id="department_store" name="department_store" value="{{$program->department_id}}">
                <form method="POST" action="{{ route('programs.update', [$program]) }}">
                    @csrf
                    @method('patch')
                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">School</span>
                        </label>
                        <select name="school_id" placeholder="School Name" class="select select-bordered w-full max-w-xs">
                            <option value="" selected disabled>Select School</option>
                            @foreach ($schools as $school)
                                <option value="{{ $school->id }}" {{ $current_school == $school->id ? 'selected' : '' }}>{{ ucfirst($school->name) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Department</span>
                        </label>
                        <select name="department_id" placeholder="Department Name" class="select select-bordered w-full max-w-xs">
                            <!-- Departments will be loaded dynamically based on the selected school -->
                            <option value="" selected disabled>Select Department</option>
                        </select>
                    </div>

                    <div class="form-control w-full p-3">
                        <label class="label">
                            <span class="label-text">Program Name</span>
                        </label>
                        <input type="text" name="name" placeholder="Program Name"
                               class="input input-bordered w-full max-w-xs" value="{{ $program->name }}"/>
                        <x-input-error :messages="$errors->get('name')" class="mt-2"/>
                    </div>

                    <div class="mt-4 p-4 space-x-2">
                        <button type="submit" class="btn btn-sm px-7">
                            Save
                        </button>
                        <a href="{{ route('programs.index') }}">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            const schoolSelect = $('select[name="school_id"]');
            const departmentSelect = $('select[name="department_id"]');
            const initialOption = schoolSelect.find('.initial-option');
            $('select[name="school_id"]').change()
            // Function to load departments based on the selected school
            function loadDepartments(selectedSchoolId) {
                $.ajax({
                    url: `/get_departments/${selectedSchoolId}`,
                    method: 'GET',
                    dataType: 'json',
                    beforeSend: function () {
                        // This function will be called before the request is sent
                        // You can add loading indicators or any other pre-request logic here
                    },
                    success: function (res) {
                        if (res.status == 1) {
                            departmentSelect.empty(); // Clear existing options
                            departmentSelect.append(`<option value="" selected disabled>Select Department</option>`);

                            $.each(res.departments, function (index, department) {
                                departmentSelect.append($('<option>', {
                                    value: department.id,
                                    text: department.name
                                }));
                            });
                        }
                    },
                    complete: function () {
                        if($("#department_store").val()!=""){
                            var current_department=$("#department_store").val()
                            $("select[name=department_id]").val(current_department)
                            $("#department_store").val("")
                        }
                    }
                });
            }


            // Initial load of departments based on the default selected school
            loadDepartments(schoolSelect.val());
            
            schoolSelect.on('change', function () {
                const selectedSchoolId = schoolSelect.val();
                loadDepartments(selectedSchoolId);
            });

            initialOption.addClass('initial-option');
        });
    </script>
</x-app-layout>
