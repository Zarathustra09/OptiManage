@extends('layouts.app')

@section('content')
    <h1>Inventories</h1>

    <div class="mb-3">
        <button class="btn btn-success" onclick="createInventory()">Create Inventory</button>
    </div>

    <table id="inventoryTable" class="table table-striped">
        <thead>
        <tr>
            <th>Category</th>
            <th>Name</th>
            <th>Quantity</th>
            <th>Description</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($inventories as $inventory)
            <tr>
                <td>{{ $inventory->category->name }}</td>
                <td>{{ $inventory->name }}</td>
                <td>{{ $inventory->quantity }}</td>
                <td>{{ $inventory->description }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editInventory({{ $inventory->id }})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteInventory({{ $inventory->id }})">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#inventoryTable').DataTable();
        });

        async function createInventory() {
            let categories = await fetchCategories();
            let categoryOptions = categories.map(category => `<option value="${category.id}">${category.name}</option>`).join('');

            await Swal.fire({
                title: 'Create Inventory',
                html: `
                <select id="swal-input1" class="swal2-input">
                    ${categoryOptions}
                </select>
                <input id="swal-input2" class="swal2-input" placeholder="Name">
                <input id="swal-input3" class="swal2-input" placeholder="Quantity">
                <input id="swal-input4" class="swal2-input" placeholder="Description">
            `,
                showConfirmButton: true,
                confirmButtonText: 'Create',
                showCloseButton: true,
                preConfirm: () => {
                    return {
                        category_id: document.getElementById('swal-input1').value,
                        name: document.getElementById('swal-input2').value,
                        quantity: document.getElementById('swal-input3').value,
                        description: document.getElementById('swal-input4').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    storeInventory(result.value);
                }
            });
        }

        async function fetchCategories() {
            let response = await fetch('{{ route('admin.category.list') }}');
            return await response.json();
        }

        function storeInventory(data) {
            $.ajax({
                url: '/admin/inventory',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire('Created!', 'Inventory has been created successfully.', 'success').then(() => {
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
                        Swal.fire('Error!', 'There was an error creating the inventory.', 'error');
                    }
                }
            });
        }

        async function editInventory(inventoryId) {
            let categories = await fetchCategories();
            let categoryOptions = categories.map(category => `<option value="${category.id}">${category.name}</option>`).join('');

            $.get('/admin/inventory/' + inventoryId, function(inventory) {
                let selectedCategoryOptions = categories.map(category => {
                    return `<option value="${category.id}" ${category.id === inventory.category_id ? 'selected' : ''}>${category.name}</option>`;
                }).join('');

                Swal.fire({
                    title: 'Edit Inventory',
                    html: `
                    <select id="swal-input1" class="swal2-input">
                        ${selectedCategoryOptions}
                    </select>
                    <input id="swal-input2" class="swal2-input" value="${inventory.name}" placeholder="Name">
                    <input id="swal-input3" class="swal2-input" value="${inventory.quantity}" placeholder="Quantity">
                    <input id="swal-input4" class="swal2-input" value="${inventory.description}" placeholder="Description">
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        return {
                            category_id: document.getElementById('swal-input1').value,
                            name: document.getElementById('swal-input2').value,
                            quantity: document.getElementById('swal-input3').value,
                            description: document.getElementById('swal-input4').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/admin/inventory/' + inventoryId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire('Updated!', response.success, 'success').then(() => {
                                    location.reload();
                                });
                            }
                        });
                    }
                });
            });
        }

        function deleteInventory(inventoryId) {
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
                        url: '/admin/inventory/' + inventoryId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', response.success, 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
