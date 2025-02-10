@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Defects</h2>
                <button class="btn btn-success" onclick="createDefect()">Create Defect</button>
            </div>
            <div class="card-body">
                <table id="defectTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Inventory</th>
                        <th>Quantity</th>
                        <th>Reason</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($defects as $defect)
                        <tr>
                            <td>{{ $defect->inventory->name }}</td>
                            <td>{{ $defect->quantity }}</td>
                            <td>{{ $defect->reason }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewDefect({{ $defect->id }})">View</button>
                                <button class="btn btn-warning btn-sm" onclick="editDefect({{ $defect->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteDefect({{ $defect->id }})">Delete</button>
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
            $('#defectTable').DataTable();
        });

        async function createDefect() {
            let inventories = await fetchInventories();
            let inventoryOptions = inventories.map(inventory => `<option value="${inventory.id}" data-quantity="${inventory.quantity}">${inventory.name}</option>`).join('');

            await Swal.fire({
                title: 'Create Defect',
                html: `
            <select id="swal-input1" class="swal2-input" onchange="updateInventoryCount()">
                ${inventoryOptions}
            </select>
            <p id="inventory-count" class="mt-2">Current Count: ${inventories[0].quantity}</p>
            <input id="swal-input2" class="swal2-input" placeholder="Quantity">
            <input id="swal-input3" class="swal2-input" placeholder="Reason">
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
                        inventory_id: document.getElementById('swal-input1').value,
                        quantity: document.getElementById('swal-input2').value,
                        reason: document.getElementById('swal-input3').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    storeDefect(result.value);
                }
            });
        }

        function updateInventoryCount() {
            let selectedOption = document.getElementById('swal-input1').selectedOptions[0];
            let quantity = selectedOption.getAttribute('data-quantity');
            document.getElementById('inventory-count').innerText = `Current Count: ${quantity}`;
        }

        function storeDefect(data) {
            $.ajax({
                url: '{{ route('admin.defect.store') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    ...data
                },
                success: function(response) {
                    Swal.fire('Created!', 'Defect has been created successfully.', 'success').then(() => {
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
                        Swal.fire('Error!', 'There was an error creating the defect.', 'error');
                    }
                }
            });
        }

        async function editDefect(defectId) {
            let inventories = await fetchInventories();
            let inventoryOptions = inventories.map(inventory => `<option value="${inventory.id}">${inventory.name}</option>`).join('');

            $.get('{{ url('/admin/defect') }}/' + defectId, function(defect) {
                let selectedInventoryOptions = inventories.map(inventory => {
                    return `<option value="${inventory.id}" ${inventory.id === defect.inventory_id ? 'selected' : ''}>${inventory.name}</option>`;
                }).join('');

                Swal.fire({
                    title: 'Edit Defect',
                    html: `
                        <select id="swal-input1" class="swal2-input">
                            ${selectedInventoryOptions}
                        </select>
                        <input id="swal-input2" class="swal2-input" value="${defect.quantity}" placeholder="Quantity">
                        <input id="swal-input3" class="swal2-input" value="${defect.reason}" placeholder="Reason">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
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
                            inventory_id: document.getElementById('swal-input1').value,
                            quantity: document.getElementById('swal-input2').value,
                            reason: document.getElementById('swal-input3').value
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('/admin/defect') }}/' + defectId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire({
                                    title: 'Updated!',
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
            });
        }

        function deleteDefect(defectId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
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
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ url('/admin/defect') }}/' + defectId,
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

        async function fetchInventories() {
            let response = await fetch('{{ route('admin.inventory.list') }}');
            return await response.json();
        }

        async function viewDefect(defectId) {
            $.get('{{ url('/admin/defect') }}/' + defectId, function(defect) {
                if (defect && defect.inventory) {
                    Swal.fire({
                        title: 'Defect Details',
                        html: `<p>Inventory: ${defect.inventory.name}</p>
                               <p>Quantity: ${defect.quantity}</p>
                               <p>Reason: ${defect.reason}</p>`,
                        icon: 'info',
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
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Defect details could not be loaded.',
                        icon: 'error',
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
                        }
                    });
                }
            }).fail(function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to fetch defect details.',
                    icon: 'error',
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
                    }
                });
            });
        }
    </script>
@endpush
