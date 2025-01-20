@extends('layouts.employee.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
{{--            <div class="card-header d-flex justify-content-between align-items-center">--}}
{{--                <h2 class="mb-0">Tasks</h2>--}}
{{--                <button class="btn btn-success" onclick="createTask()">Create Task</button>--}}
{{--            </div>--}}
            <div class="card-body">
                <table id="taskTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>Title</th>
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
                                <button class="btn btn-info btn-sm" onclick="viewTask({{ $task->id }})">View</button>
                                @if($task->status == 'On Progress')
                                    <button class="btn btn-warning btn-sm" onclick="editTask({{ $task->id }})">Edit</button>
                                @endif
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

        function createTask() {
            window.location.href = "{{ route('employee.task.create') }}";
        }

        function viewTask(taskId) {
            window.location.href = "{{ route('employee.task.showSingle', ':id') }}".replace(':id', taskId);
        }

        // function viewTask(taskId) {
        //     $.get('/employee/task/' + taskId, function(task) {
        //         let content = task.proof_of_work
        //             ? `<img src="/storage/${task.proof_of_work}" alt="Proof of Work" style="max-width: 100%;">`
        //             : 'No Proof of Work';
        //
        //         Swal.fire({
        //             title: 'Proof of Work',
        //             html: content,
        //             icon: 'info'
        //         });
        //     });
        // }

        function editTask(taskId) {
            $.get('/employee/task/' + taskId, function(task) {
                let categoryOptions = @json($categories).map(category =>
                    `<option value="${category.id}" ${task.task_category_id === category.id ? 'selected' : ''}>${category.name}</option>`
                ).join('');

                Swal.fire({
                    title: 'Edit Task',
                    html: `
                <input id="swal-input1" class="swal2-input" value="${task.title}" placeholder="Title">
                <input id="swal-input2" class="swal2-input" value="${task.description}" placeholder="Description">
                <select id="swal-input3" class="swal2-input">
                    <option value="Finished" ${task.status === 'Finished' ? 'selected' : ''}>Finished</option>
                    <option value="Cancel" ${task.status === 'Cancel' ? 'selected' : ''}>Cancel</option>
                </select>
                <select id="swal-input4" class="swal2-input">
                    ${categoryOptions}
                </select>
                <input id="swal-input5" class="swal2-input" type="datetime-local" value="${task.start_date ? task.start_date.replace(' ', 'T') : ''}" placeholder="Start Date">
                <input id="swal-input6" class="swal2-input" type="datetime-local" value="${task.end_date ? task.end_date.replace(' ', 'T') : ''}" placeholder="End Date">
                <input id="swal-input7" class="swal2-input" type="file" accept="image/*">
                ${task.proof_of_work ? `<img src="/storage/${task.proof_of_work}" alt="Proof of Work" style="max-width: 100px; max-height: 100px;">` : 'No Proof of Work'}
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        let formData = new FormData();
                        formData.append('title', document.getElementById('swal-input1').value);
                        formData.append('description', document.getElementById('swal-input2').value);
                        formData.append('status', document.getElementById('swal-input3').value);
                        formData.append('task_category_id', document.getElementById('swal-input4').value);
                        formData.append('start_date', document.getElementById('swal-input5').value);
                        formData.append('end_date', document.getElementById('swal-input6').value);
                        let proofOfWorkFile = document.getElementById('swal-input7').files[0];
                        if (proofOfWorkFile) {
                            formData.append('proof_of_work', proofOfWorkFile);
                        }
                        return formData;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/employee/task/' + taskId,
                            type: 'POST',
                            data: result.value,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-HTTP-Method-Override': 'PUT',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
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
                        url: '/employee/task/' + taskId,
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
