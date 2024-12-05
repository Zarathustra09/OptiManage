@extends('layouts.app')

@section('content')
    <h1>Tasks</h1>

    <div class="mb-3">
        <button class="btn btn-success" onclick="createTask()">Create Task</button>
    </div>

    <table id="taskTable" class="table table-striped">
        <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
            <th>Time Created</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($tasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ $task->description }}</td>
                <td>{{ $task->status }}</td>
                <td>{{ $task->created_at }}</td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="viewTask({{ $task->id }})">View</button>
                    <button class="btn btn-warning btn-sm" onclick="editTask({{ $task->id }})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteTask({{ $task->id }})">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    <script>
        $(document).ready(function() {
            $('#taskTable').DataTable();
        });

        function createTask() {
            window.location.href = "{{ route('admin.task.create') }}";
        }

        function storeTask(data) {
            $.ajax({
                url: '/admin/task',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Created!',
                        text: 'Task has been created successfully.',
                        icon: 'success'
                    }).then(() => {
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
                        Swal.fire({
                            title: 'Error!',
                            html: errorMessages,
                            icon: 'error'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error creating the task.',
                            icon: 'error'
                        });
                    }
                }
            });
        }

        function viewTask(taskId) {
            $.get('/admin/task/' + taskId, function(task) {
                Swal.fire({
                    title: 'Task Details',
                    html: `<p>Title: ${task.title}</p><p>Description: ${task.description}</p><p>Status: ${task.status}</p>`,
                    icon: 'info'
                });
            });
        }

        function editTask(taskId) {
            $.get('/admin/task/' + taskId, function(task) {
                Swal.fire({
                    title: 'Edit Task',
                    html: `
                        <input id="swal-input1" class="swal2-input" value="${task.title}" placeholder="Title">
                        <input id="swal-input2" class="swal2-input" value="${task.description}" placeholder="Description">
                        <select id="swal-input3" class="swal2-input">
                            <option value="To be Approved" ${task.status === 'To be Approved' ? 'selected' : ''}>To be Approved</option>
                            <option value="On Progress" ${task.status === 'On Progress' ? 'selected' : ''}>On Progress</option>
                            <option value="Finished" ${task.status === 'Finished' ? 'selected' : ''}>Finished</option>
                            <option value="Cancel" ${task.status === 'Cancel' ? 'selected' : ''}>Cancel</option>
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            title: document.getElementById('swal-input1').value,
                            description: document.getElementById('swal-input2').value,
                            status: document.getElementById('swal-input3').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/task/' + taskId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Updated!',
                                    text: response.success,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        }

        function deleteTask(taskId) {
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
                        url: '/admin/task/' + taskId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: response.success,
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
