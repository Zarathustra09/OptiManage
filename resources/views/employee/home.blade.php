@extends('layouts.employee.app')

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">Finished Tasks</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="check-circle"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">{{ $finishedTaskCount }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">On Progress Tasks</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="loader"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">{{ $onProgressTaskCount }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col mt-0">
                                    <h5 class="card-title">To be Approved</h5>
                                </div>

                                <div class="col-auto">
                                    <div class="stat text-primary">
                                        <i class="align-middle" data-feather="clock"></i>
                                    </div>
                                </div>
                            </div>
                            <h1 class="mt-1 mb-3">{{ $toBeApprovedTaskCount }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Latest Tasks</h5>
                </div>
                <table class="table table-hover my-0">
                    <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Time Created</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($latestTasks as $task)
                        <tr>
                            <td data-label="Ticket ID">{{ $task->ticket_id }}</td>
                            <td data-label="Title">{{ $task->title }}</td>
                            <td data-label="Assigned">
                                @if(isset($task->assigned_user))
                                    {{ $task->assigned_user }}
                                @else
                                    {{ optional($task->user)->name }}
                                @endif
                            </td>
                            <td data-label="Status">
            <span class="badge
                @if($task->status == 'Finished') bg-success
                @elseif($task->status == 'On Progress') bg-warning
                @elseif($task->status == 'To be Approved') bg-primary
                @elseif($task->status == 'Checked') bg-info
                @elseif($task->status == 'Cancel') bg-danger
                @endif">
                {{ $task->status }}
            </span>
                            </td>
                            <td data-label="Time Created">{{ $task->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
@endsection
