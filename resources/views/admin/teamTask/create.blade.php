@extends('layouts.app')

@section('content')
    @include('layouts.session')
    <form action="{{ route('admin.teamTask.store') }}" method="POST" id="createTeamTaskForm" enctype="multipart/form-data" class="container bg-white shadow p-4 rounded">
        @csrf
        <h3 class="mb-4">Create Team Task</h3>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="ticket_id" class="form-label">Ticket ID</label>
                <input type="text" class="form-control" id="ticket_id" name="ticket_id" value="{{ old('ticket_id') }}" required>
                @error('ticket_id')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                @error('description')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="area_id" class="form-label">Area</label>
                <select class="form-select" id="area_id" name="area_id" required>
                    <option value="" disabled {{ old('area_id') ? '' : 'selected' }}>Select an area</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
                @error('area_id')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="team_id" class="form-label">Team</label>
                <select class="form-select" id="team_id" name="team_id" required>
                    <option value="" disabled {{ old('team_id') ? '' : 'selected' }}>Select a team</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id') == $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                    @endforeach
                </select>
                @error('team_id')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                @error('status')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="task_category_id" class="form-label">Task Category</label>
                <div class="input-group">
                    <select class="form-select" id="task_category_id" name="task_category_id" required onchange="handleCategoryChange(this)">
                        <option value="" disabled {{ old('task_category_id') ? '' : 'selected' }}>Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('task_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                        <option value="create_new">Create New Category</option>
                    </select>
                    <button type="button" class="btn btn-danger" onclick="toggleDeleteCategory()">Delete Category</button>
                </div>
                @error('task_category_id')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="start_date" class="form-label">Start Date and Time</label>
                <input type="datetime-local" class="form-control" id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                @error('start_date')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label for="end_date" class="form-label">End Date and Time</label>
                <input type="datetime-local" class="form-control" id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                @error('end_date')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="inventory_items_display" class="form-label">Inventory Items</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="inventory_items_display" value="{{ old('inventory_items_display') }}" readonly required>
                    <input type="hidden" id="inventory_items" name="inventory_items" value="{{ old('inventory_items') }}" required>
                    <button type="button" class="btn btn-primary" onclick="selectInventoryQuantity()">Add Inventory Items</button>
                </div>
                @error('inventory_items')
                <div class="alert alert-danger mt-2 p-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary">Create Team Task</button>
            </div>
        </div>
    </form>

    <script>
        let initialInventories = {};

        async function selectInventoryQuantity() {
            let inventories = await fetchInventories();
            inventories.forEach(inventory => {
                if (!initialInventories[inventory.id]) {
                    initialInventories[inventory.id] = inventory.quantity;
                }
            });

            let inventoryOptions = inventories.map(inventory => `<option value="${inventory.id}">${inventory.name} (${initialInventories[inventory.id]} available)</option>`).join('');

            await Swal.fire({
                title: 'Select Inventory and Quantity',
                html: `
                <select id="swal-input1" class="swal2-input" required>
                    ${inventoryOptions}
                </select>
                <input id="swal-input2" class="swal2-input" type="number" placeholder="Quantity" min="1" required>
            `,
                showConfirmButton: true,
                confirmButtonText: 'Confirm',
                showCloseButton: true,
                preConfirm: () => {
                    let quantity = document.getElementById('swal-input2').value;
                    let inventoryId = document.getElementById('swal-input1').value;
                    if (quantity <= 0 || quantity > initialInventories[inventoryId]) {
                        Swal.showValidationMessage('Quantity must be greater than 0 and less than or equal to available quantity');
                        return false;
                    }
                    return {
                        inventory_id: inventoryId,
                        inventory_name: document.getElementById('swal-input1').selectedOptions[0].text.split(' (')[0],
                        quantity: quantity
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let inventoryItems = document.getElementById('inventory_items');
                    let inventoryItemsDisplay = document.getElementById('inventory_items_display');
                    let currentItems = inventoryItems.value ? JSON.parse(inventoryItems.value) : [];
                    currentItems.push(result.value);

                    initialInventories[result.value.inventory_id] -= result.value.quantity;

                    inventoryItems.value = JSON.stringify(currentItems);
                    let displayText = currentItems.map(item => `${item.inventory_name}: ${item.quantity}`).join(', ');
                    inventoryItemsDisplay.value = displayText;
                }
            });
        }

        async function fetchInventories() {
            let response = await fetch('{{ route('admin.inventory.list') }}');
            return await response.json();
        }

        function handleCategoryChange(selectElement) {
            if (selectElement.value === 'create_new') {
                createCategory();
            }
        }

        async function createCategory() {
            await Swal.fire({
                title: 'Create New Category',
                html: `
                    <input id="swal-input1" class="swal2-input" placeholder="Name">
                    <input id="swal-input2" class="swal2-input" placeholder="Description">
                `,
                showConfirmButton: true,
                confirmButtonText: 'Create',
                showCloseButton: true,
                preConfirm: () => {
                    return {
                        name: document.getElementById('swal-input1').value,
                        description: document.getElementById('swal-input2').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    storeCategory(result.value);
                }
            });
        }

        function storeCategory(data) {
            $.ajax({
                url: '/admin/taskCategory',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Created!',
                        text: 'Category has been created successfully.',
                        icon: 'success'
                    }).then(() => {
                        // Fetch the updated list of categories
                        $.ajax({
                            url: '{{ route('taskCategory.list') }}',
                            type: 'GET',
                            success: function(categories) {
                                let categorySelect = $('#task_category_id');
                                categorySelect.empty();
                                categorySelect.append('<option value="" disabled>Select a category</option>');
                                categories.forEach(category => {
                                    categorySelect.append(new Option(category.name, category.id));
                                });
                                categorySelect.append('<option value="create_new">Create New Category</option>');
                                categorySelect.val(response.id).trigger('change');
                            }
                        });
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
                            text: 'There was an error creating the category.',
                            icon: 'error'
                        });
                    }
                }
            });
        }

        function toggleDeleteCategory() {
            let selectElement = document.getElementById('task_category_id');
            let selectedOption = selectElement.options[selectElement.selectedIndex];
            if (selectedOption.value !== 'create_new') {
                deleteCategory(selectedOption.value);
            }
        }

        function deleteCategory(categoryId) {
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
                        url: '/admin/taskCategory/' + categoryId,
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
@endsection
