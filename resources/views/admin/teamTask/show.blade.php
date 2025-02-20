@extends('layouts.app')

@section('content')
    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h1 class="h3 mb-0">Team Task Details</h1>
                        <span class="badge bg-light text-primary">{{ $teamTask->status }}</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 class="h4 text-primary mb-3">{{ $teamTask->title }}</h2>
                                <p class="text-muted mb-3">{{ $teamTask->description }}</p>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card border-info mb-3">
                                            <div class="card-header bg-info text-white">Start Date</div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center" id="startDate">
                                                    {{ \Carbon\Carbon::parse($teamTask->start_date)->format('F d, Y h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-warning mb-3">
                                            <div class="card-header bg-warning text-white">End Date</div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center" id="endDate">
                                                    {{ \Carbon\Carbon::parse($teamTask->end_date)->format('F d, Y h:i A') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="card border-success mb-3">
                                            <div class="card-header bg-success text-white">Area</div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center">
                                                    {{ $teamTask->area->name }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-danger mb-3">
                                            <div class="card-header bg-danger text-white">Team</div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center">
                                                    {{ $teamTask->team->name }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form to upload team task image -->
                                <form id="uploadTeamTaskImageForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="team_task_image" class="form-label">Upload Task Image</label>
                                        <input type="file" class="form-control" id="team_task_image" name="image" accept="image/*" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Upload</button>
                                </form>

                                <div class="mt-4">
                                    <h5>Team Task Images</h5>
                                    <div class="row">
                                        @foreach($teamTask->images as $image)
                                            <div class="col-md-4 mb-3">
                                                <div class="card">
                                                    <img src="/storage/{{ $image->image_path }}" class="card-img-top" alt="Team Task Image">
                                                    <div class="card-body text-center">
                                                        <button class="btn btn-danger btn-sm" onclick="deleteTeamTaskImage({{ $image->id }})">
                                                            <i class="fas fa-trash-alt"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                        Inventory Items
                                        <span class="badge bg-light text-success">{{ $teamTask->inventories->count() }} Items</span>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="list-group list-group-flush">
                                            @forelse($teamTask->inventories as $inventory)
                                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">{{ $inventory->name }}</h6>
                                                        <small class="text-muted">Inventory Code: {{ $inventory->sku ?? 'N/A' }}</small>
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill">
                                                        Qty: {{ $inventory->pivot->quantity }}
                                                    </span>
                                                    <button class="btn btn-danger btn-sm" onclick="returnSingleItem({{ $teamTask->id }}, {{ $inventory->id }})">Return</button>
                                                </div>
                                            @empty
                                                <div class="list-group-item text-center text-muted">
                                                    No inventory items assigned
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button class="btn btn-sm btn-success" onclick="addInventoryItem()">
                                            <i class="fas fa-plus me-1"></i>Add Inventory Item
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="returnAllItems({{ $teamTask->id }})">Return All Items</button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 shadow rounded bg-white">
                                <h3 class="mb-4 text-center text-primary">Update Task Status</h3>

                                <!-- Progress Bar -->
                                <div class="progress-container d-flex justify-content-between align-items-center mb-4">
                                    <div class="progress-step {{ $teamTask->status == 'To be Approved' ? 'active' : '' }}" data-status="To be Approved">
                                        <span>To be Approved</span>
                                    </div>
                                    <div class="progress-step {{ $teamTask->status == 'Checked' ? 'active' : '' }}" data-status="Checked">
                                        <span>Checked</span>
                                    </div>

                                    <div class="progress-step {{ $teamTask->status == 'On Progress' ? 'active' : '' }}" data-status="On Progress">
                                        <span>On Progress</span>
                                    </div>
                                    <div class="progress-step {{ $teamTask->status == 'Finished' ? 'active' : '' }}" data-status="Finished">
                                        <span>Finished</span>
                                    </div>
                                    <div class="progress-step {{ $teamTask->status == 'Cancel' ? 'active' : '' }}" data-status="Cancel">
                                        <span>Cancel</span>
                                    </div>
                                </div>

                                <!-- Form Submission -->
                                <form id="updateStatusForm" method="POST" action="{{ route('admin.teamTask.update', ['id' => $teamTask->id]) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="task_status" name="status" value="{{ $teamTask->status }}">

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">Update Status</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                        <h3 class="h4 mb-0">Assignees</h3>
{{--                        <button class="btn btn-success btn-sm" onclick="createAssignee()">Add Assignee</button>--}}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="assigneesTable" class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Employee ID</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($teamTask->assignees as $assignee)
                                    <tr>
                                        <td>{{ $assignee->user->name }}</td>
                                        <td>{{ $assignee->user->email }}</td>
                                        <td>{{ $assignee->user->employee_id }}</td>
                                        <td>
                                            <button class="btn btn-danger btn-sm" onclick="deleteAssignee({{ $assignee->id }})">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </td>
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
        document.querySelectorAll('.progress-step').forEach(step => {
            step.addEventListener('click', function() {
                document.querySelectorAll('.progress-step').forEach(s => s.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('task_status').value = this.getAttribute('data-status');
            });
        });

        $('#updateStatusForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route("admin.teamTask.update", ["id" => $teamTask->id]) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-HTTP-Method-Override': 'PUT',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response, status, xhr) {
                    if (xhr.status === 200) {
                        Swal.fire({
                            title: 'Updated!',
                            text: response.success,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    }
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

        document.addEventListener("DOMContentLoaded", function () {
            const startDateElement = document.getElementById("startDate");
            const endDateElement = document.getElementById("endDate");

            if (startDateElement && endDateElement) {
                const startDate = startDateElement.textContent.trim();
                const endDate = endDateElement.textContent.trim();

                fetchAvailableUsers(startDate, endDate);
            }
        });

        async function fetchAvailableUsers(startDate, endDate) {
            try {
                const response = await fetch(`/api/free/employee?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch available users');
                }
                return await response.json();
            } catch (error) {
                console.error('Error fetching available users:', error);
                return [];
            }
        }

        async function createAssignee() {
            const startDateElement = document.getElementById("startDate");
            const endDateElement = document.getElementById("endDate");

            if (startDateElement && endDateElement) {
                const startDate = startDateElement.textContent.trim();
                const endDate = endDateElement.textContent.trim();

                const users = await fetchAvailableUsers(startDate, endDate);

                if (users.length === 0) {
                    Swal.fire({
                        icon: 'info',
                        title: 'No Users Available',
                        text: 'There are no users available for the selected time range.',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    return;
                }

                let employeeOptions = users.map(user => `<option value="${user.id}">${user.name}</option>`).join('');

                Swal.fire({
                    title: 'Add Assignee',
                    html: `
                        <select id="swal-input1" class="swal2-input">
                            ${employeeOptions}
                        </select>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Add',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        let formData = new FormData();
                        formData.append('team_task_id', '{{ $teamTask->id }}');
                        formData.append('user_id', document.getElementById('swal-input1').value);
                        return formData;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        storeAssignee(result.value);
                    }
                });
            }
        }

        function storeAssignee(data) {
            $.ajax({
                url: '{{ route("admin.teamAssignee.store") }}',
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Added!',
                        text: 'Assignee has been added successfully.',
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
                    } else if (response.status === 400 && response.responseJSON.error) {
                        Swal.fire({
                            title: 'Error!',
                            text: response.responseJSON.error,
                            icon: 'error'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error adding the assignee.',
                            icon: 'error'
                        });
                    }
                }
            });
        }

        function deleteAssignee(assigneeId) {
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
                        url: '/admin/team-assignee/' + assigneeId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Deleted!',
                                text: 'Assignee has been deleted successfully.',
                                icon: 'success'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        $('#uploadTeamTaskImageForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            $.ajax({
                url: '{{ route("teamTaskImage.store", ["teamTaskId" => $teamTask->id]) }}',
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

        function deleteTeamTaskImage(imageId) {
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
                        url: `/api/delete/team-task-image/${imageId}`,
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

        function returnSingleItem(teamTaskId, inventoryId) {
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
                        url: `/item/return-single/${teamTaskId}/${inventoryId}/${quantity}`,
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

        function returnAllItems(teamTaskId) {
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
                        url: `/item/return-all/${teamTaskId}`,
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
                            team_task_id: '{{ $teamTask->id }}',
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
                url: '{{ route("admin.teamTaskInventory.store") }}',
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
    </script>
@endpush
