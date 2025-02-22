@extends('layouts.app')

@section('content')
    <h1>Create Availability for {{ $user->name }}</h1>

    <form action="{{ route('availabilities.store') }}" method="POST">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        <div class="mb-3">
            <label for="day" class="form-label">Day</label>
            <select class="form-control" id="day" name="day" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="available_from" class="form-label">Available From</label>
            <input type="time" class="form-control" id="available_from" name="available_from" required>
        </div>

        <div class="mb-3">
            <label for="available_to" class="form-label">Available To</label>
            <input type="time" class="form-control" id="available_to" name="available_to" required>
        </div>

        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Availability</button>
    </form>
@endsection
