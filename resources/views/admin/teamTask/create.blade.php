@extends('layouts.app')

@section('content')
    <h1>Create Team Task</h1>
    <form action="{{ route('admin.teamTask.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="ticket_id">Ticket ID</label>
            <input type="text" class="form-control" id="ticket_id" name="ticket_id" required>
            @error('ticket_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="To be Approved">To be Approved</option>
                <option value="On Progress">On Progress</option>
                <option value="Finished">Finished</option>
                <option value="Cancel">Cancel</option>
            </select>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date</label>
            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
        </div>

        <div class="form-group">
            <label for="task_category_id">Task Category</label>
            <div class="input-group">
                <select class="form-control" id="task_category_id" name="task_category_id" required onchange="handleCategoryChange(this)">
                    @if($categories->isEmpty())
                        <option value="" disabled selected>Select a category or create a new one</option>
                    @else
                        <option value="" disabled selected>Select a category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    @endif
                    <option value="create_new">Create New Category</option>
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" onclick="toggleDeleteCategory()">Delete Category</button>
                </div>
            </div>
            @error('task_category_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="inventory_items_display">Inventory Items</label>
            <div class="input-group">
                <input type="text" class="form-control" id="inventory_items_display" readonly required>
                <input type="hidden" id="inventory_items" name="inventory_items" required>
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" onclick="selectInventoryQuantity()">Add Inventory Items</button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
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

                    // Deduct the selected quantity from the initial inventory
                    initialInventories[result.value.inventory_id] -= result.value.quantity;

                    // Update the hidden input and display input
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
                customClass: {
                    container: 'my-custom-container-class',
                    popup: 'my-custom-popup-class',
                    header: 'my-custom-header-class',
                    title: 'my-custom-title-class',
                    closeButton: 'my-custom-close-button-class',
                    icon: 'my-custom-icon-class',
                    htmlContainer: 'my-custom-html-container-class',
                    input: 'my-custom-input-class',
                    inputLabel: 'my-custom-input-label-class',
                    actions: 'my-custom-actions-class',
                    confirmButton: 'my-custom-confirm-button-class',
                    cancelButton: 'my-custom-cancel-button-class'
                },
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
                        icon: 'success',
                        customClass: {
                            popup: 'my-custom-popup-class',
                            title: 'my-custom-title-class',
                            confirmButton: 'my-custom-confirm-button-class'
                        }
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
                            icon: 'error',
                            customClass: {
                                popup: 'my-custom-popup-class',
                                title: 'my-custom-title-class',
                                confirmButton: 'my-custom-cancel-button-class'
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error creating the category.',
                            icon: 'error',
                            customClass: {
                                popup: 'my-custom-popup-class',
                                title: 'my-custom-title-class',
                                confirmButton: 'my-custom-cancel-button-class'
                            }
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
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                    popup: 'my-custom-popup-class',
                    title: 'my-custom-title-class',
                    confirmButton: 'my-custom-confirm-button-class',
                    cancelButton: 'my-custom-cancel-button-class'
                }
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
                                icon: 'success',
                                customClass: {
                                    popup: 'my-custom-popup-class',
                                    title: 'my-custom-title-class',
                                    confirmButton: 'my-custom-confirm-button-class'
                                }
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
