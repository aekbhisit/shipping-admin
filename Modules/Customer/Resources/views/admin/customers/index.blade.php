@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Customer Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.homepage') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Customers</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.customers.create') }}">
                            <button type="button" class="btn btn-primary"><i class="bx bx-plus me-1"></i>Add New Customer</button>
                        </a>
                        <a href="{{ route('admin.customers.export') }}">
                            <button type="button" class="btn btn-info"><i class="bx bx-export me-1"></i>Export</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="customers-datatable" class="table table-bordered table-striped text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">#</th>
                                    <th class="border-bottom-0" width="20%">Customer Name</th>
                                    <th class="border-bottom-0" width="10%">Type</th>
                                    <th class="border-bottom-0" width="15%">Email</th>
                                    <th class="border-bottom-0" width="10%">Phone</th>
                                    <th class="border-bottom-0" width="10%">Senders</th>
                                    <th class="border-bottom-0" width="15%">Branch</th>
                                    <th class="border-bottom-0" width="10%">Status</th>
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
            $('#customers-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.customers.datatable") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'customer_type', name: 'customer_type'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'senders_count', name: 'senders_count'},
                    {data: 'branch_name', name: 'branch_name'},
                    {data: 'status', name: 'status'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });
        });
    </script>
@endsection 