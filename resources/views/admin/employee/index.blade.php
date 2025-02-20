@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Employees</h2>
                <a href="{{ route('admin.employee.create') }}" class="btn btn-success">Create Employee</a>
            </div>
            <div class="card-body">
                <table id="employeeTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Employee ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Area</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->employee_id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone_number }}</td>
                            <td>{{ $user->area ? $user->area->name : 'N/A' }}</td>
                            <td>
                                <a href="{{ route('availabilities.show', $user->id) }}" class="btn btn-primary btn-sm">View Availability</a>
                                <button class="btn btn-warning btn-sm" onclick="editUser({{ $user->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#employeeTable').DataTable();
        });

        function editUser(userId) {
            window.location.href = '/admin/employee/' + userId;
        }

        function deleteUser(userId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/employee/' + userId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.success, 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
