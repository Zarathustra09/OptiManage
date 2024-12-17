@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Task Categories</h2>
                <button class="btn btn-success" onclick="createCategory()">Create Category</button>
            </div>
            <div class="card-body">
                <table id="categoryTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description }}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="viewCategory({{ $category->id }})">View</button>
                                <button class="btn btn-warning btn-sm" onclick="editCategory({{ $category->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteCategory({{ $category->id }})">Delete</button>
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
            $('#categoryTable').DataTable();
        });

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

        function viewCategory(categoryId) {
            $.get('/admin/taskCategory/' + categoryId, function(category) {
                Swal.fire({
                    title: 'Category Details',
                    html: `<p>Name: ${category.name}</p><p>Description: ${category.description}</p>`,
                    icon: 'info',
                    customClass: {
                        popup: 'my-custom-popup-class',
                        title: 'my-custom-title-class',
                        icon: 'my-custom-icon-class'
                    }
                });
            });
        }

        function editCategory(categoryId) {
            $.get('/admin/taskCategory/' + categoryId, function(category) {
                Swal.fire({
                    title: 'Edit Category',
                    html: `
                        <input id="swal-input1" class="swal2-input" value="${category.name}" placeholder="Name">
                        <input id="swal-input2" class="swal2-input" value="${category.description}" placeholder="Description">
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'my-custom-popup-class',
                        title: 'my-custom-title-class',
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
                        $.ajax({
                            url: '/admin/taskCategory/' + categoryId,
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
@endpush
