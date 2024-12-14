@extends('layouts.app')

@section('content')
    <h1>Create Team Task</h1>
    <form action="{{ route('admin.teamTask.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
            <label for="proof_of_work">Proof of Work</label>
            <input type="file" class="form-control" id="proof_of_work" name="proof_of_work" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
@endsection
