@extends('layouts.app')

@section('content')
    <h1>Employees</h1>

    <div class="mb-3">
        <button class="btn btn-success" onclick="createEmployee()">Create Employee</button>
    </div>

    <table id="employeeTable" class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->phone_number }}</td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="viewUser({{ $user->id }})">View</button>
                    <button class="btn btn-warning btn-sm" onclick="editUser({{ $user->id }})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteUser({{ $user->id }})">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

@push('scripts')

    <script>
        $(document).ready(function() {
            $('#employeeTable').DataTable();
        });

        async function createEmployee() {
            await Swal.fire({
                title: 'Create Employee',
                html: `
                <input id="swal-input1" class="swal2-input" placeholder="Name">
                <input id="swal-input2" class="swal2-input" placeholder="Email">
                <input id="swal-input3" class="swal2-input" placeholder="Phone Number">
                <input id="swal-input4" class="swal2-input" type="password" placeholder="Password">
                <input id="swal-input5" class="swal2-input" type="password" placeholder="Confirm Password">
            `,
                showConfirmButton: true,
                confirmButtonText: 'Create',
                showCloseButton: true,
                preConfirm: () => {
                    return {
                        name: document.getElementById('swal-input1').value,
                        email: document.getElementById('swal-input2').value,
                        phone_number: document.getElementById('swal-input3').value,
                        password: document.getElementById('swal-input4').value,
                        password_confirmation: document.getElementById('swal-input5').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    storeEmployee(result.value);
                }
            });
        }

        function storeEmployee(data) {
            $.ajax({
                url: '/admin/employee',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire('Created!', 'Employee has been created successfully.', 'success').then(() => {
                        location.reload();
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
        }

        function viewUser(userId) {
            $.get('/admin/employee/' + userId, function(user) {
                Swal.fire({
                    title: 'User Details',
                    html: `<p>Name: ${user.name}</p><p>Email: ${user.email}</p><p>Phone Number: ${user.phone_number}</p>`,
                    icon: 'info'
                });
            });
        }

        function editUser(userId) {
            $.get('/admin/employee/' + userId, function(user) {
                Swal.fire({
                    title: 'Edit User',
                    html: `
                        <input id="swal-input1" class="swal2-input" value="${user.name}" placeholder="Name">
                        <input id="swal-input2" class="swal2-input" value="${user.email}" placeholder="Email">
                        <input id="swal-input3" class="swal2-input" value="${user.phone_number}" placeholder="Phone Number">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            name: document.getElementById('swal-input1').value,
                            email: document.getElementById('swal-input2').value,
                            phone_number: document.getElementById('swal-input3').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/employee/' + userId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire('Updated!', response.success, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
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
