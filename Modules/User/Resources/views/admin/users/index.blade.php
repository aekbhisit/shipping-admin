@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">ผู้ใช้งาน</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">All Users</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.users.create') }}">
                            <button type="button" class="btn btn-info"><i class="lni lni-plus me-1"></i>เพิ่ม</button>
                        </a>
                    </div>
                </div>
            </div>

            <!--end breadcrumb-->
            
            <!-- Stats Cards -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bx bx-users font-24"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $stats['total_users'] ?? 0 }}</h4>
                                    <p class="mb-0">Total Users</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bx bx-user-check font-24"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $stats['company_admins'] ?? 0 }}</h4>
                                    <p class="mb-0">Company Admins</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bx bx-user-pin font-24"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $stats['branch_admins'] ?? 0 }}</h4>
                                    <p class="mb-0">Branch Admins</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="bx bx-user font-24"></i>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $stats['branch_staff'] ?? 0 }}</h4>
                                    <p class="mb-0">Branch Staff</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="users-datatable" class="table table-bordered table-striped text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">#</th>
                                    <th class="border-bottom-0" width="20%">Name</th>
                                    <th class="border-bottom-0" width="25%">Email</th>
                                    <th class="border-bottom-0" width="15%">User Type</th>
                                    <th class="border-bottom-0" width="15%">Branch</th>
                                    <th class="border-bottom-0" width="10%">Status</th>
                                    <th class="border-bottom-0" width="10%">Actions</th>
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
            $('#users-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.users.datatable_ajax") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'user_type_display', name: 'user_type'},
                    {data: 'branch_name', name: 'branch_id'},
                    {data: 'status_display', name: 'is_active'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });
        });
    </script>
@endsection 