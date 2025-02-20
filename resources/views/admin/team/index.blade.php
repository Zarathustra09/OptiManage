@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Teams</h2>
                <button class="btn btn-success" onclick="createTeam()">Create Team</button>
            </div>
            <div class="card-body">
                <table id="teamTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Area</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($teams as $team)
                        <tr>
                            <td>{{ $team->name }}</td>
                            <td>{{ $team->area->name ?? 'N/A' }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewTeam({{ $team->id }})">View</button>
                                <button class="btn btn-warning btn-sm" onclick="editTeam({{ $team->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteTeam({{ $team->id }})">Delete</button>
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
            $('#teamTable').DataTable();
        });

        async function createTeam() {
            await Swal.fire({
                title: 'Create Team',
                html: `<input id="swal-input1" class="swal2-input" placeholder="Name">
                       <select id="swal-input2" class="swal2-input">
                           <option value="">Select Area</option>
                           @foreach($areas as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
                           @endforeach
                </select>`,
                showConfirmButton: true,
                confirmButtonText: 'Create',
                showCloseButton: true,
                preConfirm: () => {
                    return {
                        name: document.getElementById('swal-input1').value,
                        area_id: document.getElementById('swal-input2').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    storeTeam(result.value);
                }
            });
        }

        function storeTeam(data) {
            $.ajax({
                url: '{{ route('admin.team.store') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire('Created!', 'Team has been created successfully.', 'success').then(() => {
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
                        Swal.fire('Error!', 'There was an error creating the team.', 'error');
                    }
                }
            });
        }

        async function editTeam(teamId) {
            $.get('{{ url('/admin/team') }}/' + teamId, function(team) {
                Swal.fire({
                    title: 'Edit Team',
                    html: `<input id="swal-input1" class="swal2-input" value="${team.name}" placeholder="Name">
                           <select id="swal-input2" class="swal2-input">
                               <option value="">Select Area</option>
                               @foreach($areas as $area)
                    <option value="{{ $area->id }}" ${team.area_id == {{ $area->id }} ? 'selected' : ''}>{{ $area->name }}</option>
                               @endforeach
                    </select>`,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            name: document.getElementById('swal-input1').value,
                            area_id: document.getElementById('swal-input2').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('/admin/team') }}/' + teamId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire('Updated!', 'Team has been updated successfully.', 'success').then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        }

        function deleteTeam(teamId) {
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
                        url: '{{ url('/admin/team') }}/' + teamId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Team has been deleted successfully.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        function viewTeam(teamId) {
            window.location.href = '{{ route('admin.team.show', '') }}/' + teamId;
        }
    </script>
@endpush
