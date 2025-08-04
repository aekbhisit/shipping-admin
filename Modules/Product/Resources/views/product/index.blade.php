@extends('layouts.datatable')

@section('title', 'Product Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-boxes me-2"></i>Product Management
                        </h4>
                        <div class="card-actions">
                            <a href="{{ route('admin.product.form') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i>Add New Product
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Status Filter -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select id="status-filter" class="form-select">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <div class="text-muted">
                                <small><i class="fas fa-info-circle me-1"></i>Click on product name to search, use status filter to narrow results</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- DataTable -->
                    <div class="table-responsive">
                        <table id="products-table" class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th width="80">Image</th>
                                    <th>Product Name</th>
                                    <th width="120">Price</th>
                                    <th width="120">Cost</th>
                                    <th width="100">Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#products-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.product.datatable") }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: function(d) {
                d.status = $('#status-filter').val();
            }
        },
        columns: [
            {
                data: 'image',
                name: 'image',
                orderable: false,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'name',
                name: 'name',
                className: 'fw-medium'
            },
            {
                data: 'price_formatted',
                name: 'price',
                className: 'text-end fw-bold text-primary'
            },
            {
                data: 'cost_formatted',
                name: 'cost',
                className: 'text-end'
            },
            {
                data: 'status_badge',
                name: 'status',
                orderable: true,
                searchable: false,
                className: 'text-center'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: '<"row"<"col-sm-6"l><"col-sm-6"f>>rtip',
        language: {
            search: "Search Products:",
            lengthMenu: "Show _MENU_ products per page",
            info: "Showing _START_ to _END_ of _TOTAL_ products",
            infoEmpty: "No products available",
            infoFiltered: "(filtered from _MAX_ total products)",
            zeroRecords: "No matching products found"
        },
        drawCallback: function() {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });

    // Status filter change
    $('#status-filter').change(function() {
        table.draw();
    });

    // Handle status toggle
    $(document).on('click', '.btn-status', function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var button = $(this);
        
        Swal.fire({
            title: 'Confirm Status Change',
            text: 'Are you sure you want to ' + (status == 1 ? 'activate' : 'deactivate') + ' this product?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.product.status") }}',
                    method: 'POST',
                    data: {
                        id: id,
                        status: status,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success == 1) {
                            Swal.fire(
                                'Updated!',
                                response.msg,
                                'success'
                            );
                            table.draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                response.msg,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Something went wrong!',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Handle delete
    $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Delete Product',
            text: 'Are you sure you want to delete this product? This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.product.delete") }}',
                    method: 'POST',
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success == 1) {
                            Swal.fire(
                                'Deleted!',
                                response.msg,
                                'success'
                            );
                            table.draw();
                        } else {
                            Swal.fire(
                                'Error!',
                                response.msg,
                                'error'
                            );
                        }
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Something went wrong!',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
.table th {
    background-color: #495057;
    color: white;
    font-weight: 600;
    border-color: #495057;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.775rem;
}

.badge {
    font-size: 0.75em;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
}
</style>
@endpush 