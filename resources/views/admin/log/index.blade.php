@extends('layouts.app')

@section('content')

    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Activity Logs</h2>
            </div>
            <div class="card-body">
                <table id="activityLogsTable" class="table table-hover table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th>Description</th>
                        <th>Event</th>
                        <th>Subject</th>
                        <th>Caused By</th>
                        <th>Timestamp</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($logs as $log)
                        <tr>
                            <td>{{ $log->description }}</td>
                            <td>
                                <span class="badge bg-{{ $log->event === 'created' ? 'success' : ($log->event === 'updated' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($log->event) }}
                                </span>
                            </td>
                            <td>
                                @if($log->subject_type == 'App\Models\Task')
                                    <strong>{{ $log->subject ? $log->subject->title : 'N/A' }}</strong>
                                    <small class="d-block text-muted">Task</small>
                                @else
                                    <strong>{{ $log->subject ? $log->subject->name : 'N/A' }}</strong>
                                    <small class="d-block text-muted">{{ class_basename($log->subject_type) }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $log->causer ? $log->causer->name : 'System' }}
                            </td>
                            <td>
                                    <span title="{{ $log->created_at }}">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="viewLogProperties({{ json_encode($log->properties) }})">
                                    View Properties
                                </button>
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
            $('#activityLogsTable').DataTable();
        });


        function viewLogProperties(properties) {
            let formattedHtml = '<div class="log-properties-container">';

            try {
                // Ensure properties is an object
                if (typeof properties === 'string') {
                    properties = JSON.parse(properties);
                }

                // Creation event
                if (properties.attributes && !properties.old) {
                    formattedHtml += `
                <div class="created-event">
                    <h4>Created Record Details</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Object.entries(properties.attributes).map(([key, value]) => `
                                <tr>
                                    <td><strong>${key}</strong></td>
                                    <td>${value !== null ? value : '<em>null</em>'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
                }
                // Update event
                else if (properties.old && properties.attributes) {
                    formattedHtml += `
                <div class="updated-event">
                    <h4>Updated Record Details</h4>
                    <table class="table table-bordered comparison-table">
                        <thead>
                            <tr>
                                <th class="text-danger w-50">Old Values</th>
                                <th class="text-success w-50">New Values</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${(() => {
                        const oldKeys = Object.keys(properties.old);
                        const newKeys = Object.keys(properties.attributes);
                        const allKeys = [...new Set([...oldKeys, ...newKeys])];

                        return allKeys.map(key => `
                                    <tr>
                                        <td class="text-danger">
                                            <strong>${key}:</strong>
                                            ${properties.old[key] !== undefined
                            ? (properties.old[key] !== null ? properties.old[key] : '<em>null</em>')
                            : '<em>No previous value</em>'}
                                        </td>
                                        <td class="text-success">
                                            <strong>${key}:</strong>
                                            ${properties.attributes[key] !== undefined
                            ? (properties.attributes[key] !== null ? properties.attributes[key] : '<em>null</em>')
                            : '<em>Value removed</em>'}
                                        </td>
                                    </tr>
                                `).join('');
                    })()}
                        </tbody>
                    </table>
                </div>
            `;
                }
                // Deletion event
                else if (properties.old) {
                    formattedHtml += `
                <div class="deleted-event">
                    <h4>Deleted Record Details</h4>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Attribute</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${Object.entries(properties.old).map(([key, value]) => `
                                <tr>
                                    <td><strong>${key}</strong></td>
                                    <td>${value !== null ? value : '<em>null</em>'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
                } else {
                    // Fallback for unexpected format
                    formattedHtml += '<pre>' + JSON.stringify(properties, null, 2) + '</pre>';
                }
            } catch (error) {
                // Error handling
                formattedHtml += '<pre>Unable to parse properties: ' + error.message + '</pre>';
            }

            formattedHtml += '</div>';

            Swal.fire({
                title: 'Log Properties',
                html: formattedHtml,
                width: 800,
                padding: '2em',
                background: '#f4f4f4',
                backdrop: 'rgba(0,0,0,0.1)',
                showCloseButton: true,
                customClass: {
                    popup: 'log-properties-popup'
                }
            });
        }

    </script>
@endpush
