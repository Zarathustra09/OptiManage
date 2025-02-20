@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Areas</h2>
                <button class="btn btn-success" onclick="createArea()">Create Area</button>
            </div>
            <div class="card-body">
                <table id="areaTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($areas as $area)
                        <tr>
                            <td>{{ $area->name }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewArea({{ $area->id }})">View</button>
                                <button class="btn btn-warning btn-sm" onclick="editArea({{ $area->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteArea({{ $area->id }})">Delete</button>
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
            $('#areaTable').DataTable();
        });

        async function createArea() {
            await Swal.fire({
                title: 'Create Area',
                html: `<input id="swal-input1" class="swal2-input" placeholder="Name">`,
                showConfirmButton: true,
                confirmButtonText: 'Create',
                showCloseButton: true,
                preConfirm: () => {
                    return {
                        name: document.getElementById('swal-input1').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    storeArea(result.value);
                }
            });
        }

        function storeArea(data) {
            $.ajax({
                url: '{{ route('admin.area.store') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire('Created!', 'Area has been created successfully.', 'success').then(() => {
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
                        Swal.fire('Error!', 'There was an error creating the area.', 'error');
                    }
                }
            });
        }

        async function editArea(areaId) {
            $.get('{{ url('/admin/area') }}/' + areaId, function(area) {
                Swal.fire({
                    title: 'Edit Area',
                    html: `<input id="swal-input1" class="swal2-input" value="${area.name}" placeholder="Name">`,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            name: document.getElementById('swal-input1').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('/admin/area') }}/' + areaId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire('Updated!', 'Area has been updated successfully.', 'success').then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        }

        function deleteArea(areaId) {
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
                        url: '{{ url('/admin/area') }}/' + areaId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Area has been deleted successfully.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        async function viewArea(areaId) {
            $.get('{{ url('/admin/area') }}/' + areaId, function(area) {
                Swal.fire({
                    title: 'Area Details',
                    html: `<p>Name: ${area.name}</p>`,
                    icon: 'info'
                });
            }).fail(function() {
                Swal.fire('Error!', 'Failed to fetch area details.', 'error');
            });
        }
    </script>
@endpush
