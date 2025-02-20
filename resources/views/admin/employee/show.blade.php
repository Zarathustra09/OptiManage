@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">Edit Employee</h2>
            </div>
            <div class="card-body">
                <form id="editEmployeeForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="employee_id">Employee ID</label>
                        <input type="text" class="form-control" id="employee_id" name="employee_id" value="{{ $user->employee_id }}" required>
                    </div>
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ $user->phone_number }}" required>
                    </div>
                    <div class="form-group">
                        <label for="area_id">Area</label>
                        <select class="form-control" id="area_id" name="area_id" required>
                            @foreach($areas as $area)
                                <option value="{{ $area->id }}" {{ $user->area_id == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Update</button>
                    <a href="{{ route('admin.employee.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#editEmployeeForm').on('submit', function(e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('admin.employee.update', $user->id) }}',
                    type: 'PUT',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Updated!', 'Employee has been updated successfully.', 'success').then(() => {
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
                            Swal.fire('Error!', 'There was an error updating the employee.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
