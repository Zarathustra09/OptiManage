@extends('layouts.employee.app')

@section('content')
    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Team Task Details</h1>
                        <span class="badge bg-light text-primary">{{ $task->status }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 class="h4 text-primary mb-3">{{ $task->title }}</h2>
                                <p class="text-muted mb-3">{{ $task->description }}</p>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card border-info mb-3">
                                            <div class="card-header bg-info text-white">Start Date</div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center">
                                                    {{ \Carbon\Carbon::parse($task->start_date)->format('F d, Y h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning text-white">End Date</div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center">
                                                    {{ \Carbon\Carbon::parse($task->end_date)->format('F d, Y h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @if($task->proof_of_work)
                                    <div class="mb-3">
                                        <div class="card">
                                            <div class="card-header bg-secondary text-white">Proof of Work</div>
                                            <div class="card-body p-2">
                                                <img src="/storage/{{ $task->proof_of_work }}" alt="Proof of Work" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Form to update proof of work -->
                                <form id="updateProofOfWorkForm" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="proof_of_work" class="form-label">Update Proof of Work</label>
                                        <input type="file" class="form-control" id="proof_of_work" name="proof_of_work" accept="image/*" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                        Inventory Items
                                        <span class="badge bg-light text-success">{{ $task->inventories->count() }} Items</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            @forelse($task->inventories as $inventory)
                                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">{{ $inventory->name }}</h6>
                                                        <small class="text-muted">Inventory Code: {{ $inventory->code ?? 'N/A' }}</small>
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">
                                                        Qty: {{ $inventory->pivot->quantity }}
                                                    </span>
                                                </div>
                                            @empty
                                                <div class="list-group-item text-center text-muted">
                                                    No inventory items assigned
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h3 class="h4 mb-0">Assignees</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="assigneesTable" class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Employee ID</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($task->assignees as $assignee)
                                    <tr>
                                        <td>{{ $assignee->user->name }}</td>
                                        <td>{{ $assignee->user->email }}</td>
                                        <td>{{ $assignee->user->employee_id }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#assigneesTable').DataTable();
        });

        $('#updateProofOfWorkForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route("employee.task.update", $task->id) }}',
                type: 'POST',
                data: formData,
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
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error!',
                        text: response.responseJSON.message,
                        icon: 'error'
                    });
                }
            });
        });
    </script>
@endpush
