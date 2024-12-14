@extends('layouts.app')

@section('content')
    <h1>Create Task</h1>
    @include('layouts.session')
    <form action="{{ route('admin.task.store') }}" method="POST" id="createTaskForm" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
            @error('title')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
            @error('description')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="To be Approved">To be Approved</option>
                <option value="On Progress">On Progress</option>
                <option value="Finished">Finished</option>
                <option value="Cancel">Cancel</option>
            </select>
            @error('status')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="user_id">User</label>
            <select class="form-control" id="user_id" name="user_id" required>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            @error('user_id')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="start_date">Start Date and Time</label>
            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
            @error('start_date')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="end_date">End Date and Time</label>
            <input type="datetime-local" class="form-control" id="end_date" name="end_date" required>
            @error('end_date')
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
            @error('inventory_items')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="proof_of_work">Proof of Work</label>
            <input type="file" class="form-control" id="proof_of_work" name="proof_of_work" accept="image/*" required>
            @error('proof_of_work')
            <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Create Task</button>
    </form>

    <script>
        async function selectInventoryQuantity() {
            let inventories = await fetchInventories();
            let inventoryOptions = inventories.map(inventory => `<option value="${inventory.id}">${inventory.name} (${inventory.quantity} available)</option>`).join('');

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
                    if (quantity <= 0) {
                        Swal.showValidationMessage('Quantity must be greater than 0');
                        return false;
                    }
                    return {
                        inventory_id: document.getElementById('swal-input1').value,
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
    </script>
@endsection
