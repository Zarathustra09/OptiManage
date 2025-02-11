@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Tasks</h2>
                <button class="btn btn-success" onclick="createTask()">Create Task</button>
            </div>
            <div class="card-body">
                <table id="taskTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Title</th>
                        <th>Assignee</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->ticket_id }}</td>
                            <td>{{ $task->title }}</td>
                            <td>{{ $task->user->name }}</td>
                            <td>
                                 <span class="badge
                                    @if($task->status == 'Finished') bg-success
                                    @elseif($task->status == 'On Progress') bg-warning
                                    @elseif($task->status == 'To be Approved') bg-primary
                                     @elseif($task->status == 'Checked') bg-info
                                    @elseif($task->status == 'Cancel') bg-danger
                                    @endif">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td>{{ $task->category->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->start_date)->format('F d Y h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->end_date)->format('F d Y h:i A') }}</td>
                            <td>
                                <a href="{{ route('admin.task.show', $task->id) }}" class="btn btn-info btn-sm">View</a>
                                <button class="btn btn-warning btn-sm" onclick="editTask({{ $task->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteTask({{ $task->id }})">Delete</button>
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

        const statuses = @json($statuses);

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
                let content = task.proof_of_work
                    ? `<img src="/storage/${task.proof_of_work}" alt="Proof of Work" style="max-width: 100%;">`
                    : 'No Proof of Work';

                Swal.fire({
                    title: 'Proof of Work',
                    html: content,
                    icon: 'info'
                });
            });
        }

        function editTask(taskId) {
            $.get('{{ route("admin.task.showSingle", ":id") }}'.replace(':id', taskId), function(task) {
                if (!task || !task.start_date || !task.end_date) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Task data is incomplete or not found.',
                        icon: 'error'
                    });
                    return;
                }

                let statusOptions = statuses.map(status => `<option value="${status}" ${task.status === status ? 'selected' : ''}>${status}</option>`).join('');

                Swal.fire({
                    title: 'Edit Task',
                    html: `
                        <input id="swal-input1" class="swal2-input" placeholder="Title" value="${task.title}">
                        <textarea id="swal-input2" class="swal2-textarea" placeholder="Description">${task.description}</textarea>
                        <select id="swal-input3" class="swal2-input">
                            ${statusOptions}
                        </select>
                        <input id="swal-input4" class="swal2-input" type="datetime-local" placeholder="Start Date" value="${task.start_date.replace(' ', 'T')}">
                        <input id="swal-input5" class="swal2-input" type="datetime-local" placeholder="End Date" value="${task.end_date.replace(' ', 'T')}">
                        <select id="swal-input6" class="swal2-input">
                            @foreach($categories as $category)
                    <option value="{{ $category->id }}" ${task.task_category_id === {{ $category->id }} ? 'selected' : ''}>{{ $category->name }}</option>
                            @endforeach
                    </select>
`,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            title: document.getElementById('swal-input1').value,
                            description: document.getElementById('swal-input2').value,
                            status: document.getElementById('swal-input3').value,
                            start_date: document.getElementById('swal-input4').value,
                            end_date: document.getElementById('swal-input5').value,
                            task_category_id: document.getElementById('swal-input6').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route("admin.task.updateAdmin", ":id") }}'.replace(':id', taskId),
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                title: result.value.title,
                                description: result.value.description,
                                status: result.value.status,
                                start_date: result.value.start_date,
                                end_date: result.value.end_date,
                                task_category_id: result.value.task_category_id
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Updated!',
                                    text: response.success,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(response) {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.responseJSON.message,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            }).fail(function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to fetch task data.',
                    icon: 'error'
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
