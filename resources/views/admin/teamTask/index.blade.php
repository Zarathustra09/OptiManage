@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Team Task</h2>
                <button class="btn btn-success" onclick="createTeamTask()">Create Team Task</button>
            </div>
            <div class="card-body">
                <table id="teamTaskTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Category</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($tasks as $task)
                        <tr>
                            <td>{{ $task->ticket_id }}</td>
                            <td>{{ $task->title }}</td>
                            <td>
                                <span class="badge
                                    @if($task->status == 'Finished') bg-success
                                    @elseif($task->status == 'On Progress') bg-warning
                                    @elseif($task->status == 'To be Approved') bg-primary
                                    @elseif($task->status == 'Cancel') bg-danger
                                    @endif">
                                    {{ $task->status }}
                                </span>
                            </td>
                            <td>{{ $task->category->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->start_date)->format('F d Y h:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($task->end_date)->format('F d Y h:i A') }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewTeamTask({{ $task->id }})">View</button>
                                <button class="btn btn-warning btn-sm" onclick="editTeamTask({{ $task->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteTeamTask({{ $task->id }})">Delete</button>
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
            $('#teamTaskTable').DataTable();
        });

        function createTeamTask() {
            window.location.href = "{{ route('admin.teamTask.create') }}";
        }

        function viewTeamTask(taskId) {
            window.location.href = "{{ route('admin.teamTask.show', ':id') }}".replace(':id', taskId);
        }

        function editTeamTask(taskId) {
            $.get('{{ route("admin.teamTask.single", ":id") }}'.replace(':id', taskId), function(task) {
                Swal.fire({
                    title: 'Edit Team Task',
                    html: `
                <input id="swal-input1" class="swal2-input" placeholder="Title" value="${task.title}">
                <textarea id="swal-input2" class="swal2-textarea" placeholder="Description">${task.description}</textarea>
                <select id="swal-input3" class="swal2-input">
                    <option value="To be Approved" ${task.status === 'To be Approved' ? 'selected' : ''}>To be Approved</option>
                    <option value="On Progress" ${task.status === 'On Progress' ? 'selected' : ''}>On Progress</option>
                    <option value="Finished" ${task.status === 'Finished' ? 'selected' : ''}>Finished</option>
                    <option value="Cancel" ${task.status === 'Cancel' ? 'selected' : ''}>Cancel</option>
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
                            url: '/admin/team-task/update/' + taskId,
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
            });
        }


        function deleteTeamTask(taskId) {
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
                        url: '/admin/team-task/' + taskId,
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
