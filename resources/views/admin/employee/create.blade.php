@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Create Employee</h2>
            </div>
            <div class="card-body">
                <form id="createEmployeeForm">
                    @csrf
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>

{{--                    <div class="form-group">--}}
{{--                        <label for="shift">Shift</label>--}}
{{--                        <select class="form-control" id="shift" name="shift" required>--}}
{{--                            <option value="day">Day Shift</option>--}}
{{--                            <option value="night">Night Shift</option>--}}
{{--                        </select>--}}
{{--                    </div>--}}

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-success">Create</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#createEmployeeForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('admin.employee.store') }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Created!', 'Employee has been created successfully.', 'success').then(() => {
                            window.location.href = '{{ route('admin.employee.index') }}';
                        });
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            let errors = response.responseJSON.errors;
                            let errorMessages = '';
                            for (let field in errors) {
                                errorMessages += `${errors[field].join(', ')}<br>`;
                            }
                            Swal.fire('Error!', errorMessages, 'error');
                        } else {
                            Swal.fire('Error!', 'There was an error creating the employee.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
