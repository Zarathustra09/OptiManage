@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Team: {{ $team->name }}</h2>
                <a href="{{ route('admin.team.index') }}" class="btn btn-primary">Back to Teams</a>
                <button class="btn btn-success" onclick="addUserToTeam({{ $team->id }})">Add User</button>
            </div>
            <div class="card-body">
                <h4>Area: {{ $team->area->name ?? 'N/A' }}</h4>
                <h4>Assignees</h4>
                <table id="assigneeTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($team->assignees as $assignee)
                        <tr>
                            <td>{{ $assignee->user->name }}</td>
                            <td>{{ $assignee->user->email }}</td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deleteAssignee({{ $assignee->id }})">Remove</button>
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
            $('#assigneeTable').DataTable();
        });

        async function addUserToTeam(teamId) {
            const { value: userId } = await Swal.fire({
                title: 'Add User to Team',
                input: 'select',
                inputOptions: {
                    @foreach($users as $user)
                        {{ $user->id }}: '{{ $user->name }}',
                    @endforeach
                },
                inputPlaceholder: 'Select a user',
                showCancelButton: true,
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to select a user!'
                    }
                }
            });

            if (userId) {
                $.ajax({
                    url: '{{ route('admin.teamAssignee.store') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        team_id: teamId,
                        user_id: userId
                    },
                    success: function(response) {
                        Swal.fire('Added!', 'User has been added to the team successfully.', 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function(response) {
                        Swal.fire('Error!', 'There was an error adding the user to the team.', 'error');
                    }
                });
            }
        }

        function deleteAssignee(assigneeId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('/admin/team-assignee') }}/' + assigneeId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Removed!', 'Assignee has been removed successfully.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
