@extends('layouts.datatable')

@section('styles')
@endsection

@section('content')
    <!--start page wrapper -->
    <div class="page-wrapper">
        <div class="page-content">
            <!--breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">Product Management</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('admin.dashboard.index') }}"><i class="bx bx-home-alt"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Products</li>
                        </ol>
                    </nav>
                </div>
                <div class="ms-auto">
                    <div class="btn-group">
                        <a href="{{ route('admin.products.create') }}">
                            <button type="button" class="btn btn-primary"><i class="bx bx-plus me-1"></i>Add Product</button>
                        </a>
                    </div>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="products-datatable" class="table table-bordered table-striped text-nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="border-bottom-0" width="5%">#</th>
                                    <th class="border-bottom-0" width="10%">Image</th>
                                    <th class="border-bottom-0" width="20%">Product</th>
                                    <th class="border-bottom-0" width="15%">Category</th>
                                    <th class="border-bottom-0" width="10%">SKU</th>
                                    <th class="border-bottom-0" width="10%">Price</th>
                                    <th class="border-bottom-0" width="8%">Unit</th>
                                    <th class="border-bottom-0" width="8%">Status</th>
                                    <th class="border-bottom-0" width="14%">Actions</th>
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
            $('#products-datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("admin.products.datatable_ajax") }}',
                    type: 'GET'
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'category', name: 'category'},
                    {data: 'sku', name: 'sku'},
                    {data: 'price', name: 'price'},
                    {data: 'unit', name: 'unit'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false}
                ],
                order: [[0, 'desc']]
            });
        });
    </script>
@endsection 