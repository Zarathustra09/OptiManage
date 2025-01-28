@extends('layouts.employee.app')

@section('content')
    <style>
        .progress-container {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 20px;
        }

        .progress-step {
            width: 25%;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease-in-out;
        }

        .progress-step.active {
            color: #fff;
            background: #007bff;
            border-radius: 8px;
        }

        .progress-step:not(.active) {
            background: #e0e0e0;
            color: #555;
            border-radius: 8px;
        }

        .progress-step span {
            display: block;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>

    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Task Details</h1>
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
                                                {{ \Carbon\Carbon::parse($task->start_date)->format('F d, Y h:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning text-white">End Date</div>
                                            <div class="card-body p-2">
                                                {{ \Carbon\Carbon::parse($task->end_date)->format('F d, Y h:i A') }}
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

                                <form id="updateProofOfWorkForm" enctype="multipart/form-data" method="POST" action="{{ route('image.store', ['taskId' => $task->id]) }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="task_image" class="form-label">Upload Task Image</label>
                                        <input type="file" class="form-control" id="task_image" name="image" accept="image/*" {{ $task->status == 'To be Approved' ? 'disabled' : '' }}>
                                    </div>
                                    <button type="submit" class="btn btn-primary" {{ $task->status == 'To be Approved' ? 'disabled' : '' }}>Upload</button>
                                </form>

                                <div class="mt-4">
                                    <h5>Task Images</h5>
                                    <div class="row">
                                        @foreach($task->images as $image)
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <img src="/storage/{{ $image->image_path }}" class="card-img-top" alt="Task Image">
                                                    <div class="card-body text-center">
                                                        <button class="btn btn-danger btn-sm" onclick="deleteImage({{ $image->id }})">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="p-4 shadow rounded bg-white">
                                    <h3 class="mb-4 text-center text-primary">Update Task Status</h3>

                                    <!-- Progress Bar -->
                                    <div class="progress-container d-flex justify-content-between align-items-center mb-4">
                                        <div class="progress-step {{ $task->status == 'To be Approved' ? 'active' : '' }}" data-status="To be Approved">
                                            <span>To be Approved</span>
                                        </div>
                                        <div class="progress-step {{ $task->status == 'On Progress' ? 'active' : '' }}" data-status="On Progress">
                                            <span>On Progress</span>
                                        </div>
                                        <div class="progress-step {{ $task->status == 'Finished' ? 'active' : '' }}" data-status="Finished">
                                            <span>Finished</span>
                                        </div>
                                        <div class="progress-step {{ $task->status == 'Cancel' ? 'active' : '' }}" data-status="Cancel">
                                            <span>Cancel</span>
                                        </div>
                                    </div>

                                    <!-- Form Submission -->
                                    <form id="updateStatusForm" method="POST" action="{{ route('admin.task.update', ['id' => $task->id]) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" id="task_status" name="status" value="{{ $task->status }}">

                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg">Update Status</button>
                                        </div>
                                    </form>
                                </div>

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
                                                    <button class="btn btn-danger btn-sm" onclick="returnSingleTaskItem({{ $task->id }}, {{ $inventory->id }})" {{ $task->status == 'To be Approved' ? 'disabled' : '' }}>Return</button>
                                                </div>
                                            @empty
                                                <div class="list-group-item text-center text-muted">
                                                    No inventory items assigned
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-sm btn-success" onclick="addInventoryItem()" {{ $task->status == 'To be Approved' ? 'disabled' : '' }}>
                                            <i class="fas fa-plus me-1"></i>Add Inventory Item
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="returnAllTaskItems({{ $task->id }})" {{ $task->status == 'To be Approved' ? 'disabled' : '' }}>Return All Items</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>

        document.querySelectorAll('.progress-step').forEach(step => {
            step.addEventListener('click', function() {
                document.querySelectorAll('.progress-step').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('task_status').value = this.getAttribute('data-status');
            });
        });

        $('#updateProofOfWorkForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route("image.store", ["taskId" => $task->id]) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Uploaded!',
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

        $('#updateStatusForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route("admin.task.update", ["id" => $task->id]) }}',
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

        function returnSingleTaskItem(taskId, inventoryId) {
            Swal.fire({
                title: 'Enter quantity to return',
                input: 'number',
                inputAttributes: {
                    min: 1,
                    step: 1
                },
                showCancelButton: true,
                confirmButtonText: 'Return',
                showLoaderOnConfirm: true,
                preConfirm: (quantity) => {
                    return $.ajax({
                        url: `/item/return-single-task-item/${taskId}/${inventoryId}/${quantity}`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(response => {
                        Swal.fire(
                            'Returned!',
                            response.success,
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    }).catch(error => {
                        Swal.fire(
                            'Error!',
                            error.responseJSON.message,
                            'error'
                        );
                    });
                }
            });
        }

        function returnAllTaskItems(taskId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to return all items.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, return all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/item/return-all-task-items/${taskId}`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Returned!',
                                response.success,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.message,
                                'error'
                            );
                        }
                    });
                }
            });
        }

        function addInventoryItem() {
            fetchInventories().then(inventories => {
                let inventoryOptions = inventories.map(inventory => `<option value="${inventory.id}">${inventory.name} (${inventory.quantity} available)</option>`).join('');
                Swal.fire({
                    title: 'Add Inventory Item',
                    html: `
                        <select id="swal-inventory-id" class="swal2-input">
                            ${inventoryOptions}
                        </select>
                        <input id="swal-quantity" class="swal2-input" type="number" placeholder="Quantity" min="1" required>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        let inventoryId = document.getElementById('swal-inventory-id').value;
                        let quantity = document.getElementById('swal-quantity').value;
                        if (quantity <= 0) {
                            Swal.showValidationMessage('Quantity must be greater than 0');
                            return false;
                        }
                        return {
                            task_id: '{{ $task->id }}',
                            inventory_id: inventoryId,
                            quantity: quantity
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        storeInventoryItem(result.value);
                    }
                });
            });
        }

        function storeInventoryItem(data) {
            $.ajax({
                url: '{{ route("admin.taskInventory.store") }}',
                type: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Added!',
                        text: 'Inventory item has been added successfully.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error adding the inventory item.',
                        icon: 'error'
                    });
                }
            });
        }

        async function fetchInventories() {
            let response = await fetch('{{ route('admin.inventory.list') }}');
            return await response.json();
        }

        function deleteImage(imageId) {
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
                        url: `/api/delete/image/${imageId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire(
                                'Deleted!',
                                response.success,
                                'success'
                            ).then(() => {
                                location.reload();
                            });
                        },
                        error: function(response) {
                            Swal.fire(
                                'Error!',
                                response.responseJSON.message,
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
@endpush
