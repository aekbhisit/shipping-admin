@extends('layouts.datatable')
@section('styles')
    <link href="{{ asset('assets/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatable/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Failed Login Attempts</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.user-activity.index') }}">User Activity Logs</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Failed Attempts</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Failed Login Attempts</h5>
                            <p class="mb-0 text-secondary">Monitor security events and failed authentication attempts</p>
                        </div>
                        <div class="ms-auto">
                            <button type="button" class="btn btn-danger btn-sm" onclick="clearFailedAttempts()">
                                <i class="bx bx-trash"></i> Clear All
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="failed-attempts-table" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>IP Address</th>
                                    <th>User Agent</th>
                                    <th>Attempt Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end page wrapper -->
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/responsive.bootstrap5.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#failed-attempts-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.user-activity.failed-attempts") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'username', name: 'username'},
                    {data: 'ip_address', name: 'ip_address'},
                    {data: 'user_agent', name: 'user_agent'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']],
                pageLength: 25,
                responsive: true
            });
        });

        function clearFailedAttempts() {
            if (confirm('Are you sure you want to clear all failed attempts? This action cannot be undone.')) {
                $.ajax({
                    url: '{{ route("admin.user-activity.clear-failed-attempts") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Failed attempts cleared successfully');
                            $('#failed-attempts-table').DataTable().ajax.reload();
                        } else {
                            alert('Error clearing failed attempts');
                        }
                    },
                    error: function() {
                        alert('Error clearing failed attempts');
                    }
                });
            }
        }
    </script>
@endsection 