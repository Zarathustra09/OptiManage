@extends('layouts.app')

@section('content')
    <h1>Availabilities for {{ $user->name }}</h1>
    @include('layouts.session')
    <div class="mb-3">
        <a href="{{ route('availabilities.create', $user->id) }}" class="btn btn-success">Add Availability</a>
    </div>

    <table id="availabilityTable" class="table table-striped">
        <thead>
        <tr>
            <th>Day</th>
            <th>Available From</th>
            <th>Available To</th>
            <th>Shift Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($availabilities as $availability)
            <tr>
                <td>{{ \Carbon\Carbon::parse($availability->day)->format('l') }}</td>
                <td>{{ \Carbon\Carbon::parse($availability->available_from)->format('g:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($availability->available_to)->format('g:i A') }}</td>
                     @if($availability->shift_type == 0)
                      <td>
                            Day
                      </td>
                     @elseif($availability->shift_type == 1)
                      <td>
                            Night
                      </td>
                         @endif




                <td>{{ $availability->status }}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="editAvailability({{ $availability->id }})">Edit</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteAvailability({{ $availability->id }})">Delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#availabilityTable').DataTable();
        });

        function editAvailability(availabilityId) {
            $.get('/availabilities/single/' + availabilityId, function(availability) {
                Swal.fire({
                    title: 'Edit Availability',
                    html: `
                <input type="hidden" id="swal-input0" value="${availability.user_id}">
                <select id="swal-input1" class="swal2-input">
                    <option value="Monday" ${availability.day === 'Monday' ? 'selected' : ''}>Monday</option>
                    <option value="Tuesday" ${availability.day === 'Tuesday' ? 'selected' : ''}>Tuesday</option>
                    <option value="Wednesday" ${availability.day === 'Wednesday' ? 'selected' : ''}>Wednesday</option>
                    <option value="Thursday" ${availability.day === 'Thursday' ? 'selected' : ''}>Thursday</option>
                    <option value="Friday" ${availability.day === 'Friday' ? 'selected' : ''}>Friday</option>
                    <option value="Saturday" ${availability.day === 'Saturday' ? 'selected' : ''}>Saturday</option>
                    <option value="Sunday" ${availability.day === 'Sunday' ? 'selected' : ''}>Sunday</option>
                </select>
                <input id="swal-input2" class="swal2-input" type="time" value="${moment(availability.available_from, 'HH:mm:ss').format('HH:mm')}">
                <input id="swal-input3" class="swal2-input" type="time" value="${moment(availability.available_to, 'HH:mm:ss').format('HH:mm')}">
                <select id="swal-input4" class="swal2-input">
                    <option value="active" ${availability.status === 'active' ? 'selected' : ''}>Active</option>
                    <option value="inactive" ${availability.status === 'inactive' ? 'selected' : ''}>Inactive</option>
                </select>
                <select id="swal-input5" class="swal2-input">
                    <option value="0" ${availability.shift_type === 0 ? 'selected' : ''}>Day</option>
                    <option value="1" ${availability.shift_type === 1 ? 'selected' : ''}>Night</option>
                </select>
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Update',
                    cancelButtonText: 'Cancel',
                    preConfirm: () => {
                        const availableFrom = document.getElementById('swal-input2').value;
                        const availableTo = document.getElementById('swal-input3').value;
                        const timeFormat = /^([0-1]\d|2[0-3]):([0-5]\d)$/;

                        if (!timeFormat.test(availableFrom) || !timeFormat.test(availableTo)) {
                            Swal.showValidationMessage('Time must be in the format HH:mm');
                            return false;
                        }

                        return {
                            user_id: document.getElementById('swal-input0').value,
                            day: document.getElementById('swal-input1').value,
                            available_from: availableFrom,
                            available_to: availableTo,
                            status: document.getElementById('swal-input4').value,
                            shift_type: document.getElementById('swal-input5').value
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/availabilities/' + availabilityId,
                            type: 'PUT',
                            data: {
                                _token: '{{ csrf_token() }}',
                                ...result.value
                            },
                            success: function(response) {
                                Swal.fire('Updated!', 'Availability has been updated successfully.', 'success').then(() => {
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
                                    Swal.fire('Error!', 'There was an error updating the availability.', 'error');
                                }
                            }
                        });
                    }
                });
            });
        }

        function deleteAvailability(availabilityId) {
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
                        url: '/availabilities/' + availabilityId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            Swal.fire('Deleted!', 'Availability has been deleted successfully.', 'success').then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        }
    </script>
@endpush
