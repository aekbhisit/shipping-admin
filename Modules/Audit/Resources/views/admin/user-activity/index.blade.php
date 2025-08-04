@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">User Activity Logs</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">User Activity Logs</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.user-activity.failed-attempts') }}">
                            <button type="button" class="btn btn-warning"><i class="bx bx-shield-x me-1"></i>Failed Attempts</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="user-activity-datatable" class="table table-bordered table-striped text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">#</th>
                                    <th class="border-bottom-0" width="15%">User</th>
                                    <th class="border-bottom-0" width="10%">Action</th>
                                    <th class="border-bottom-0" width="15%">Module</th>
                                    <th class="border-bottom-0" width="25%">Description</th>
                                    <th class="border-bottom-0" width="10%">IP Address</th>
                                    <th class="border-bottom-0" width="15%">Date</th>
                                    <th class="border-bottom-0" width="5%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
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
    <script>
        $(document).ready(function() {
            $('#user-activity-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.user-activity.datatable_ajax") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'user_name', name: 'user_name'},
                    {data: 'action', name: 'action'},
                    {data: 'module', name: 'module'},
                    {data: 'description', name: 'description'},
                    {data: 'ip_address', name: 'ip_address'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[6, 'desc']]
            });
        });
    </script>
@endsection 